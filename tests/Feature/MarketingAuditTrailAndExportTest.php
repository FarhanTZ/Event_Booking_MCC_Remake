<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingApproval;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingAuditTrailAndExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $marketingUser;
    protected User $regularUser;
    protected Room $roomL2;
    protected Room $roomL5;

    protected function setUp(): void
    {
        parent::setUp();

        $this->marketingUser = User::factory()->create([
            'role' => 'marketing',
            'full_name' => 'Staf Marketing MCC',
            'email' => 'marketing@mcc.id',
        ]);

        $this->regularUser = User::factory()->create([
            'role' => 'user',
            'full_name' => 'Budi Santoso',
            'institution_name' => 'Komunitas Kreatif Malang',
        ]);

        $this->roomL2 = Room::create([
            'id' => 10,
            'name' => 'Auditorium Bioskop',
            'slug' => 'auditorium-bioskop',
            'floor_level' => 2,
            'capacity' => 100,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 200000.00,
            'is_active' => true,
            'description' => 'Auditorium Bioskop Lantai 2',
        ]);

        $this->roomL5 = Room::create([
            'id' => 20,
            'name' => 'Coworking Space',
            'slug' => 'coworking-space',
            'floor_level' => 5,
            'capacity' => 40,
            'buffer_minutes' => 15,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 100000.00,
            'is_active' => true,
            'description' => 'Coworking Space Lantai 5',
        ]);
    }

    public function test_curatorial_audit_trail_is_recorded_and_displayed(): void
    {
        $booking = Booking::create([
            'user_id' => $this->regularUser->id,
            'status' => BookingStatus::AWAITING_MARKETING,
            'booking_code' => 'MCC-2026-TEST-AUDIT',
            'version' => 1,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Workshop Desain Grafis',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'DKV',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 30,
            'event_description' => 'Workshop desain grafis.',
            'proposal_file_url' => '/storage/proposals/workshop.pdf',
            'pic_name_snapshot' => 'Budi Santoso',
            'pic_phone_snapshot' => '081234567890',
            'institution_name_snapshot' => 'Komunitas Kreatif Malang',
            'tos_agreed_at' => Carbon::now(),
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->roomL2->id,
            'event_start_at' => Carbon::now()->addDays(5)->setTime(10, 0),
            'event_end_at' => Carbon::now()->addDays(5)->setTime(12, 0),
            'is_active' => true,
        ]);

        // 1. Marketing request revision
        $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.revise', ['booking' => $booking->id]),
            ['revision_notes' => 'Proposal perlu dilengkapi dengan rundown detail acara 2 hari.']
        );

        // 2. User resubmits revision
        $this->actingAs($this->regularUser)->post(
            route('bookings.revise.submit', ['booking' => $booking->id]),
            [
                'event_name' => 'Workshop Desain Grafis Revisi',
                'event_category' => 'Komunitas',
                'ekraf_subsector' => 'DKV',
                'event_type' => 'Terbuka Untuk Umum',
                'estimated_participants' => 35,
                'event_description' => 'Workshop desain grafis untuk UMKM Malang Raya.',
                'equipment_needed' => 'Proyektor HDMI',
                'pic_name' => 'Budi Santoso',
                'pic_phone' => '081234567890',
                'pic_emergency_phone' => '081234567891',
                'tos_agreed' => 1,
            ]
        );

        // 3. Marketing approves proposal
        $this->actingAs($this->marketingUser)->post(
            route('marketing.bookings.approve', ['booking' => $booking->id]),
            [
                'pricing_scheme' => 'subsidy',
                'approval_notes' => 'Disetujui subsidi penuh setelah perbaikan rundown.',
            ]
        );

        $booking->refresh();

        // Assert audit trail count and sequence
        $this->assertCount(3, $booking->approvals);
        $this->assertEquals('revised', $booking->approvals[0]->decision);
        $this->assertEquals('revision_submitted', $booking->approvals[1]->decision);
        $this->assertEquals('approved', $booking->approvals[2]->decision);
        $this->assertEquals($this->marketingUser->id, $booking->approvals[0]->action_by);
        $this->assertEquals($this->regularUser->id, $booking->approvals[1]->action_by);
        $this->assertEquals($this->marketingUser->id, $booking->approvals[2]->action_by);

        // Assert audit trail displayed on user dashboard
        $userDashboardResponse = $this->actingAs($this->regularUser)->get(route('dashboard'));
        $userDashboardResponse->assertOk();
        $userDashboardResponse->assertSee('Riwayat Evaluasi Kurasi');
        $userDashboardResponse->assertSee('Proposal perlu dilengkapi dengan rundown detail acara 2 hari.');
        $userDashboardResponse->assertSee('Disetujui subsidi penuh setelah perbaikan rundown.');
    }

    public function test_marketing_can_export_confirmed_schedule_to_pdf(): void
    {
        $confirmedBooking = Booking::create([
            'user_id' => $this->regularUser->id,
            'status' => BookingStatus::CONFIRMED,
            'booking_code' => 'MCC-EXP-01',
            'clearance_status' => 'cleared',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
        ]);

        EventDetail::create([
            'booking_id' => $confirmedBooking->id,
            'event_name' => 'Seminar Teknologi Malang',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Aplikasi',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 60,
            'event_description' => 'Seminar teknologi.',
            'proposal_file_url' => '/storage/proposals/seminar.pdf',
            'pic_name_snapshot' => 'Budi Santoso',
            'pic_phone_snapshot' => '081234567890',
            'institution_name_snapshot' => 'Komunitas Kreatif Malang',
            'tos_agreed_at' => Carbon::now(),
        ]);

        BookingSlot::create([
            'booking_id' => $confirmedBooking->id,
            'room_id' => $this->roomL2->id,
            'event_start_at' => Carbon::now()->addDays(2)->setTime(9, 0),
            'event_end_at' => Carbon::now()->addDays(2)->setTime(12, 0),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->marketingUser)->get(
            route('marketing.schedule.export.pdf', [
                'date_from' => Carbon::now()->toDateString(),
                'date_to' => Carbon::now()->addDays(7)->toDateString(),
            ])
        );

        $response->assertOk();
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment;', (string)$response->headers->get('Content-Disposition'));
    }

    public function test_marketing_can_export_confirmed_schedule_to_csv(): void
    {
        $confirmedBooking = Booking::create([
            'user_id' => $this->regularUser->id,
            'status' => BookingStatus::CONFIRMED,
            'booking_code' => 'MCC-EXP-02',
            'pricing_scheme' => 'subsidy',
            'clearance_status' => 'cleared',
            'nominal_amount' => 0.00,
        ]);

        EventDetail::create([
            'booking_id' => $confirmedBooking->id,
            'event_name' => 'Pameran Seni Rupa Malang',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Seni Rupa',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 50,
            'event_description' => 'Pameran karya seni.',
            'proposal_file_url' => '/storage/proposals/pameran.pdf',
            'pic_name_snapshot' => 'Budi Santoso',
            'pic_phone_snapshot' => '081234567890',
            'institution_name_snapshot' => 'Komunitas Kreatif Malang',
            'tos_agreed_at' => Carbon::now(),
        ]);

        BookingSlot::create([
            'booking_id' => $confirmedBooking->id,
            'room_id' => $this->roomL5->id,
            'event_start_at' => Carbon::now()->addDays(3)->setTime(13, 0),
            'event_end_at' => Carbon::now()->addDays(3)->setTime(17, 0),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->marketingUser)->get(
            route('marketing.schedule.export.csv', [
                'floor_level' => 5,
            ])
        );

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string)$response->headers->get('Content-Type'));
    }

    public function test_regular_user_cannot_export_schedule(): void
    {
        $response = $this->actingAs($this->regularUser)->get(
            route('marketing.schedule.export.pdf')
        );

        $response->assertForbidden();
    }
}
