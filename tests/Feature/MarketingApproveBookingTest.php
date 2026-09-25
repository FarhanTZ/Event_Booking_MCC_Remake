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

class MarketingApproveBookingTest extends TestCase
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
            'name' => 'Ruang Workshop Lantai 3',
            'slug' => 'ruang-workshop-lantai-3',
            'floor_level' => 3,
            'capacity' => 50,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 150000.00,
            'is_active' => true,
            'description' => 'Ruang workshop modern.',
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
            'full_name' => 'Ahmad Fauzi',
            'email' => 'fauzi@komunitasekraf.id',
            'phone_number' => '+6281299887766',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Game Developer Malang',
            'address' => 'Lowokwaru, Malang',
        ]);
    }

    protected function createSampleBooking(BookingStatus $status = BookingStatus::AWAITING_MARKETING): Booking
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-20260925-GMDEV',
            'user_id' => $this->applicantUser->id,
            'status' => $status,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => Carbon::now()->addDays(4)->setTime(9, 0),
            'event_end_at' => Carbon::now()->addDays(4)->setTime(13, 0),
            'operational_range' => '["2026-09-29 09:00:00", "2026-09-29 13:30:00")',
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Game Jam & Showcase Komunitas Ekraf 2026',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Game & Aplikasi',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 40,
            'event_description' => 'Kompetisi pembuatan game edukatif 24 jam.',
            'proposal_file_url' => '/storage/proposals/gamejam.pdf',
            'pic_name_snapshot' => 'Ahmad Fauzi',
            'pic_phone_snapshot' => '+6281299887766',
            'institution_name_snapshot' => 'Komunitas Game Developer Malang',
            'tos_agreed_at' => Carbon::now(),
        ]);

        return $booking;
    }

    public function test_marketing_can_approve_booking_with_subsidy_scheme(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.approve', ['booking' => $booking->id]),
            [
                'pricing_scheme' => 'subsidy',
                'approval_notes' => 'Disetujui penuh untuk kegiatan pengembangan komunitas game lokal.',
            ]
        );

        $response->assertRedirect(route('marketing.dashboard'));
        $response->assertSessionHas('success');

        $booking->refresh();

        // Verifikasi REQ-MKT-07 Skema Subsidi:
        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);
        $this->assertEquals('subsidy', $booking->pricing_scheme);
        $this->assertEquals(0.00, (float) $booking->nominal_amount);
        $this->assertEquals($this->marketingUser->id, $booking->confirmed_by);
        $this->assertNotNull($booking->checkin_token);
        $this->assertEquals(64, strlen($booking->checkin_token));

        // Verifikasi log approval tercatat
        $this->assertDatabaseHas('booking_approvals', [
            'booking_id' => $booking->id,
            'action_by' => $this->marketingUser->id,
            'decision' => 'approved',
            'notes' => 'Disetujui penuh untuk kegiatan pengembangan komunitas game lokal.',
        ]);

        // Verifikasi slot tetap aktif
        $this->assertTrue($booking->slots()->first()->is_active);
    }

    public function test_marketing_can_approve_booking_with_commercial_scheme(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.approve', ['booking' => $booking->id]),
            [
                'pricing_scheme' => 'commercial',
                'nominal_amount' => 1200000,
                'payment_notes' => 'Transfer ke Rekening Kas Daerah Bank Jatim 0111-0022-3344 a.n BPKAD Kota Malang.',
                'approval_notes' => 'Tarif komersial 4 jam sesuai Perda Kota Malang.',
            ]
        );

        $response->assertRedirect(route('marketing.dashboard'));
        $response->assertSessionHas('success');

        $booking->refresh();

        // Verifikasi REQ-MKT-07 Skema Komersial:
        $this->assertEquals(BookingStatus::AWAITING_PAYMENT, $booking->status);
        $this->assertEquals('commercial', $booking->pricing_scheme);
        $this->assertEquals(1200000.00, (float) $booking->nominal_amount);
        $this->assertEquals('Transfer ke Rekening Kas Daerah Bank Jatim 0111-0022-3344 a.n BPKAD Kota Malang.', $booking->payment_notes);
        
        // E-Pass ditahan (checkin_token null) hingga pembayaran divalidasi
        $this->assertNull($booking->checkin_token);
        $this->assertNull($booking->confirmed_by);

        // Hak seluruh slot jadwal di database tetap aman terikat (is_active = true)
        $this->assertTrue($booking->slots()->first()->is_active);

        // Verifikasi log approval tercatat
        $this->assertDatabaseHas('booking_approvals', [
            'booking_id' => $booking->id,
            'action_by' => $this->marketingUser->id,
            'decision' => 'approved',
            'notes' => 'Tarif komersial 4 jam sesuai Perda Kota Malang.',
        ]);
    }

    public function test_commercial_scheme_fails_if_nominal_missing(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.approve', ['booking' => $booking->id]),
            [
                'pricing_scheme' => 'commercial',
                'nominal_amount' => '',
                'payment_notes' => '',
            ]
        );

        $response->assertSessionHasErrors(['nominal_amount']);
        
        $booking->refresh();
        $this->assertEquals(BookingStatus::AWAITING_MARKETING, $booking->status);
    }

    public function test_commercial_scheme_fails_if_nominal_negative(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.approve', ['booking' => $booking->id]),
            [
                'pricing_scheme' => 'commercial',
                'nominal_amount' => -50000,
                'payment_notes' => 'Rekening Kasda',
            ]
        );

        $response->assertSessionHasErrors(['nominal_amount']);
    }

    public function test_revised_booking_can_also_be_approved_by_marketing(): void
    {
        $booking = $this->createSampleBooking(BookingStatus::REVISED);

        $response = $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.approve', ['booking' => $booking->id]),
            [
                'pricing_scheme' => 'subsidy',
                'approval_notes' => 'Dokumen revisi telah sesuai syarat.',
            ]
        );

        $response->assertRedirect(route('marketing.dashboard'));
        $booking->refresh();

        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);
        $this->assertNotNull($booking->checkin_token);
    }

    public function test_regular_user_cannot_approve_booking(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->applicantUser)->post(
            route('marketing.bookings.approve', ['booking' => $booking->id]),
            [
                'pricing_scheme' => 'subsidy',
            ]
        );

        $response->assertStatus(403);
    }

    public function test_guest_cannot_approve_booking(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->post(
            route('marketing.bookings.approve', ['booking' => $booking->id]),
            [
                'pricing_scheme' => 'subsidy',
            ]
        );

        $response->assertRedirect(route('login'));
    }

    public function test_approve_booking_supports_json_request(): void
    {
        $booking = $this->createSampleBooking();

        $response = $this->actingAs($this->marketingUser)
            ->postJson(
                route('marketing.bookings.approve', ['booking' => $booking->id]),
                [
                    'pricing_scheme' => 'subsidy',
                    'approval_notes' => 'API approval test',
                ]
            );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonPath('booking.status', BookingStatus::CONFIRMED->value);
    }
}
