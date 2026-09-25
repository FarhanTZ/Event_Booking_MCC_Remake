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

class MarketingRejectBookingTest extends TestCase
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
            'name' => 'Ruang Bioskop Lantai 5',
            'slug' => 'ruang-bioskop-lantai-5',
            'floor_level' => 5,
            'capacity' => 80,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Ruang bioskop dan pemutaran film screening.',
        ]);

        $this->marketingUser = User::create([
            'full_name' => 'Marketing Officer MCC',
            'email' => 'marketing@mcc.id',
            'phone_number' => '+6281234567890',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::MARKETING,
            'account_type' => AccountType::INSTANSI,
            'institution_name' => 'Malang Creative Center',
            'address' => 'Kota Malang',
        ]);

        $this->applicantUser = User::create([
            'full_name' => 'Raditya Wicaksono',
            'email' => 'raditya@komunitasfilm.id',
            'phone_number' => '+6281333999888',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Sinema Malang',
            'address' => 'Klojen, Malang',
        ]);
    }

    protected function createSampleBooking(BookingStatus $status = BookingStatus::AWAITING_MARKETING): Booking
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-20260925-REJECT',
            'user_id' => $this->applicantUser->id,
            'status' => $status,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => Carbon::now()->addDays(5)->setTime(14, 0),
            'event_end_at' => Carbon::now()->addDays(5)->setTime(18, 0),
            'operational_range' => '["2026-09-30 14:00:00", "2026-09-30 18:30:00")',
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Festival Screening Film Komunitas',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Film, Animasi & Video',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 70,
            'event_description' => 'Penayangan film indie lokal malang raya.',
            'proposal_file_url' => '/storage/proposals/film.pdf',
            'pic_name_snapshot' => 'Raditya Wicaksono',
            'pic_phone_snapshot' => '+6281333999888',
            'institution_name_snapshot' => 'Komunitas Sinema Malang',
            'tos_agreed_at' => Carbon::now(),
        ]);

        return $booking;
    }

    public function test_marketing_can_reject_booking_permanently_and_release_slots(): void
    {
        $booking = $this->createSampleBooking();

        $rejectionCategory = 'di_luar_ekraf';
        $rejectionNotes = 'Kegiatan yang diajukan tidak relevan dengan 17 sub-sektor ekraf binaan gedung MCC.';

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.reject', ['booking' => $booking->id]),
            [
                'rejection_category' => $rejectionCategory,
                'rejection_notes' => $rejectionNotes,
            ]
        );

        $response->assertRedirect(route('marketing.dashboard'));
        $response->assertSessionHas('success');

        $booking->refresh();

        // 1. Status permohonan berubah menjadi rejected (REQ-MKT-09)
        $this->assertEquals(BookingStatus::REJECTED, $booking->status);

        // 2. Pelepasan Slot Otomatis: Sistem seketika menonaktifkan seluruh baris slot terkait (is_active = false)
        $this->assertFalse((bool) $booking->slots()->first()->is_active);
        $this->assertEquals(0, $booking->slots()->where('is_active', true)->count());

        // 3. Catatan log keputusan penolakan dicatat di tabel booking_approvals
        $this->assertDatabaseHas('booking_approvals', [
            'booking_id' => $booking->id,
            'action_by' => $this->marketingUser->id,
            'decision' => 'rejected',
        ]);

        $approval = BookingApproval::where('booking_id', $booking->id)->first();
        $this->assertStringContainsString('di_luar_ekraf', $approval->notes);
        $this->assertStringContainsString($rejectionNotes, $approval->notes);
    }

    public function test_marketing_can_reject_booking_in_revised_status(): void
    {
        $booking = $this->createSampleBooking(BookingStatus::REVISED);

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.reject', ['booking' => $booking->id]),
            [
                'rejection_category' => 'regulasi_gedung',
                'rejection_notes' => 'Berkas perbaikan revisi tetap melanggar regulasi teknis penggunaan gedung MCC.',
            ]
        );

        $response->assertRedirect(route('marketing.dashboard'));

        $booking->refresh();
        $this->assertEquals(BookingStatus::REJECTED, $booking->status);
        $this->assertFalse((bool) $booking->slots()->first()->is_active);
    }

    public function test_rejection_fails_if_category_is_missing_or_invalid(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.reject', ['booking' => $booking->id]),
            [
                'rejection_category' => 'invalid_category_choice',
                'rejection_notes' => 'Catatan penolakan yang cukup panjang melebihi 20 karakter.',
            ]
        );

        $response->assertSessionHasErrors(['rejection_category']);

        $booking->refresh();
        $this->assertEquals(BookingStatus::AWAITING_MARKETING, $booking->status);
        $this->assertTrue((bool) $booking->slots()->first()->is_active);
    }

    public function test_rejection_fails_if_notes_less_than_20_characters(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.reject', ['booking' => $booking->id]),
            [
                'rejection_category' => 'regulasi_gedung',
                'rejection_notes' => 'Alasan pendek', // < 20 chars
            ]
        );

        $response->assertSessionHasErrors(['rejection_notes']);

        $booking->refresh();
        $this->assertEquals(BookingStatus::AWAITING_MARKETING, $booking->status);
        $this->assertTrue((bool) $booking->slots()->first()->is_active);
    }

    public function test_regular_user_cannot_reject_booking(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->applicantUser)->post(
            route('marketing.bookings.reject', ['booking' => $booking->id]),
            [
                'rejection_category' => 'regulasi_gedung',
                'rejection_notes' => 'Uraian alasan penolakan lebih dari dua puluh karakter.',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_reject_supports_json_api_response(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)
            ->postJson(
                route('marketing.bookings.reject', ['booking' => $booking->id]),
                [
                    'rejection_category' => 'norma_ketertiban',
                    'rejection_notes' => 'Kegiatan berpotensi menimbulkan gangguan ketertiban umum di sekitar gedung.',
                ]
            );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonPath('booking.status', BookingStatus::REJECTED->value);
    }
}
