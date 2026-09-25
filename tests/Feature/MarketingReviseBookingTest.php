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

class MarketingReviseBookingTest extends TestCase
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
            'name' => 'Ruang Teater Lantai 4',
            'slug' => 'ruang-teater-lantai-4',
            'floor_level' => 4,
            'capacity' => 120,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Ruang teater dan seni pertunjukan.',
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
            'full_name' => 'Bagus Prayogo',
            'email' => 'bagus@teatermalang.id',
            'phone_number' => '+6281333444555',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Teater Malang',
            'address' => 'Sukun, Malang',
        ]);
    }

    protected function createSampleBooking(BookingStatus $status = BookingStatus::AWAITING_MARKETING): Booking
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-20260925-TEATER',
            'user_id' => $this->applicantUser->id,
            'status' => $status,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => Carbon::now()->addDays(6)->setTime(13, 0),
            'event_end_at' => Carbon::now()->addDays(6)->setTime(17, 0),
            'operational_range' => '["2026-10-01 13:00:00", "2026-10-01 17:30:00")',
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Pementasan Teater Cerita Rakyat Jawa Timur',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Seni Pertunjukan',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 100,
            'event_description' => 'Pementasan teater edukatif untuk pelajar.',
            'proposal_file_url' => '/storage/proposals/teater.pdf',
            'pic_name_snapshot' => 'Bagus Prayogo',
            'pic_phone_snapshot' => '+6281333444555',
            'institution_name_snapshot' => 'Komunitas Teater Malang',
            'tos_agreed_at' => Carbon::now(),
        ]);

        return $booking;
    }

    public function test_marketing_can_request_revision_with_valid_notes(): void
    {
        $booking = $this->createSampleBooking();

        $revisionNotes = 'Mohon perbarui file proposal dengan melampirkan rundown teknis tata lampu dan surat izin resmi.';

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.revise', ['booking' => $booking->id]),
            [
                'revision_notes' => $revisionNotes,
            ]
        );

        $response->assertRedirect(route('marketing.dashboard'));
        $response->assertSessionHas('success');

        $booking->refresh();

        // Verifikasi REQ-MKT-08:
        // 1. Status permohonan berpindah menjadi revised
        $this->assertEquals(BookingStatus::REVISED, $booking->status);

        // 2. Menetapkan tenggat waktu: revision_deadline = NOW() + INTERVAL '24 hours'
        $this->assertNotNull($booking->revision_deadline);
        $expectedDeadline = Carbon::now()->addHours(24);
        $this->assertTrue(
            $booking->revision_deadline->diffInMinutes($expectedDeadline) <= 2,
            'Revision deadline must be set to 24 hours from now.'
        );

        // 3. Mempertahankan seluruh slot jadwal di database agar tidak direbut orang lain (is_active = true)
        $this->assertTrue($booking->slots()->first()->is_active);

        // 4. Riwayat kurasi tercatat di tabel booking_approvals
        $this->assertDatabaseHas('booking_approvals', [
            'booking_id' => $booking->id,
            'action_by' => $this->marketingUser->id,
            'decision' => 'revised',
            'notes' => $revisionNotes,
        ]);
    }

    public function test_revision_fails_if_notes_less_than_20_characters(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.revise', ['booking' => $booking->id]),
            [
                'revision_notes' => 'Perbaiki proposal', // 17 karakter (kurang dari 20)
            ]
        );

        $response->assertSessionHasErrors(['revision_notes']);

        $booking->refresh();
        $this->assertEquals(BookingStatus::AWAITING_MARKETING, $booking->status);
        $this->assertNull($booking->revision_deadline);
    }

    public function test_revision_fails_if_notes_empty(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.revise', ['booking' => $booking->id]),
            [
                'revision_notes' => '',
            ]
        );

        $response->assertSessionHasErrors(['revision_notes']);

        $booking->refresh();
        $this->assertEquals(BookingStatus::AWAITING_MARKETING, $booking->status);
    }

    public function test_regular_user_cannot_request_revision(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->applicantUser)->post(
            route('marketing.bookings.revise', ['booking' => $booking->id]),
            [
                'revision_notes' => 'Mohon lampirkan berkas perbaikan proposal kegiatan.',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_guest_cannot_request_revision(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->post(
            route('marketing.bookings.revise', ['booking' => $booking->id]),
            [
                'revision_notes' => 'Mohon lampirkan berkas perbaikan proposal kegiatan.',
            ]
        );

        $response->assertRedirect(route('login'));
    }

    public function test_revision_supports_json_request(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)
            ->postJson(
                route('marketing.bookings.revise', ['booking' => $booking->id]),
                [
                    'revision_notes' => 'Mohon lampirkan surat rekomendasi dinas terkait pada proposal.',
                ]
            );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonPath('booking.status', BookingStatus::REVISED->value);
    }
}
