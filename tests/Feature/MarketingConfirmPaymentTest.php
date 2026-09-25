<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingApproval;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MarketingConfirmPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $marketingUser;
    protected User $applicantUser;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->room = Room::create([
            'id' => 1,
            'name' => 'Grand Hall Lantai 2',
            'slug' => 'grand-hall-lantai-2',
            'floor_level' => 2,
            'capacity' => 250,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 1500000.00,
            'is_active' => true,
            'description' => 'Aula serbaguna untuk pameran dan konser komersial.',
        ]);

        $this->marketingUser = User::create([
            'full_name' => 'Kasir & Kurator Marketing',
            'email' => 'marketing@mcc.id',
            'phone_number' => '+6281234567890',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::MARKETING,
            'account_type' => AccountType::INSTANSI,
            'institution_name' => 'Malang Creative Center',
            'address' => 'Kota Malang',
        ]);

        $this->applicantUser = User::create([
            'full_name' => 'Hendri Wijaya',
            'email' => 'hendri@promoter.co.id',
            'phone_number' => '+6281299887766',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'PT Kreasi Event Nusantara',
            'address' => 'Lowokwaru, Malang',
        ]);
    }

    protected function createCommercialBooking(BookingStatus $status = BookingStatus::AWAITING_PAYMENT): Booking
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-20260925-KOMERSIAL',
            'user_id' => $this->applicantUser->id,
            'status' => $status,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'commercial',
            'nominal_amount' => 4500000.00,
            'payment_notes' => 'Transfer Kas Daerah Bank Jatim: 0111-0022-3344 a.n BPKAD Kota Malang.',
            'checkin_token' => null, // Belum diterbitkan sebelum bayar
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => Carbon::now()->addDays(7)->setTime(10, 0),
            'event_end_at' => Carbon::now()->addDays(7)->setTime(13, 0),
            'operational_range' => '["2026-10-02 10:00:00", "2026-10-02 13:30:00")',
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Expo Inovasi Produk Kreatif 2026',
            'event_category' => 'Komersial',
            'ekraf_subsector' => 'Desain Produk',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 200,
            'event_description' => 'Pameran brand kreatif dan bazaar komersial.',
            'proposal_file_url' => '/storage/proposals/expo.pdf',
            'pic_name_snapshot' => 'Hendri Wijaya',
            'pic_phone_snapshot' => '+6281299887766',
            'institution_name_snapshot' => 'PT Kreasi Event Nusantara',
            'tos_agreed_at' => Carbon::now(),
        ]);

        return $booking;
    }

    public function test_marketing_can_confirm_payment_with_valid_reference_and_activate_epass(): void
    {
        $booking = $this->createCommercialBooking();

        $reference = 'STS-BPKAD-2026-09-887766';
        $notes = 'Pembayaran tervalidasi di rekening koran Bank Jatim Kas Daerah.';

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.confirm-payment', ['booking' => $booking->id]),
            [
                'payment_reference' => $reference,
                'payment_notes' => $notes,
            ]
        );

        $response->assertRedirect(route('marketing.dashboard'));
        $response->assertSessionHas('success');

        $booking->refresh();

        // 1. Status permohonan berpindah dari awaiting_payment menjadi confirmed (REQ-MKT-10)
        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);

        // 2. Timestamp bayar tercatat (paid_at = NOW())
        $this->assertNotNull($booking->paid_at);
        $this->assertTrue($booking->paid_at->isToday());

        // 3. Identitas kurator penyetuju tercatat (confirmed_by = auth()->id())
        $this->assertEquals($this->marketingUser->id, $booking->confirmed_by);

        // 4. Checkin token & E-Pass aktif diterbitkan (64 characters)
        $this->assertNotNull($booking->checkin_token);
        $this->assertEquals(64, strlen($booking->checkin_token));

        // 5. Slot ruangan tetap aktif dan terikat aman
        $this->assertTrue((bool) $booking->slots()->first()->is_active);

        // 6. Riwayat approval tercatat di booking_approvals
        $this->assertDatabaseHas('booking_approvals', [
            'booking_id' => $booking->id,
            'action_by' => $this->marketingUser->id,
            'decision' => 'payment_verified',
        ]);

        $approval = BookingApproval::where('booking_id', $booking->id)->first();
        $this->assertStringContainsString($reference, $approval->notes);
        $this->assertStringContainsString($notes, $approval->notes);
    }

    public function test_confirm_payment_fails_if_reference_is_missing(): void
    {
        $booking = $this->createCommercialBooking();

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.confirm-payment', ['booking' => $booking->id]),
            [
                'payment_reference' => '',
            ]
        );

        $response->assertSessionHasErrors(['payment_reference']);

        $booking->refresh();
        $this->assertEquals(BookingStatus::AWAITING_PAYMENT, $booking->status);
        $this->assertNull($booking->paid_at);
        $this->assertNull($booking->checkin_token);
    }

    public function test_confirm_payment_fails_if_booking_not_awaiting_payment(): void
    {
        $booking = $this->createCommercialBooking(BookingStatus::AWAITING_MARKETING);

        $this->expectException(\InvalidArgumentException::class);

        $action = app(\App\Actions\Marketing\ConfirmPaymentAction::class);
        $dto = new \App\DataTransferObjects\Marketing\ConfirmPaymentData(
            bookingId: $booking->id,
            paymentReference: 'STS-12345',
            paymentNotes: 'Catatan',
            marketingUserId: $this->marketingUser->id,
        );

        $action->execute($dto);
    }

    public function test_regular_user_cannot_confirm_payment(): void
    {
        $booking = $this->createCommercialBooking();

        $response = $this->actingAs($this->applicantUser)->post(
            route('marketing.bookings.confirm-payment', ['booking' => $booking->id]),
            [
                'payment_reference' => 'STS-123456789',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_confirm_payment_supports_json_request(): void
    {
        $booking = $this->createCommercialBooking();

        $response = $this->actingAs($this->marketingUser)
            ->postJson(
                route('marketing.bookings.confirm-payment', ['booking' => $booking->id]),
                [
                    'payment_reference' => 'STS-MCC-API-2026',
                    'payment_notes' => 'Pembayaran via QRIS Kasda BPKAD',
                ]
            );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonPath('booking.status', BookingStatus::CONFIRMED->value);
        $response->assertJsonPath('booking.confirmed_by', $this->marketingUser->id);
    }
}
