<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingApproval;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\KtpVaultLog;
use App\Models\Room;
use App\Models\RoomIncident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsAndAuditTrailTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $marketingUser;
    private User $foUser;
    private User $doUser;
    private User $applicant;
    private Room $roomL2;
    private Room $roomL5;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'full_name' => 'Super Admin Test',
            'email' => 'superadmin@mcc.test',
            'role' => UserRole::SUPER_ADMIN,
        ]);

        $this->marketingUser = User::factory()->create([
            'full_name' => 'Marketing Curation',
            'email' => 'marketing@mcc.test',
            'role' => UserRole::MARKETING,
        ]);

        $this->foUser = User::factory()->create([
            'full_name' => 'Front Office Staff',
            'email' => 'fo@mcc.test',
            'role' => UserRole::FRONT_OFFICE,
        ]);

        $this->doUser = User::factory()->create([
            'full_name' => 'Duty Officer Field',
            'email' => 'do@mcc.test',
            'role' => UserRole::DUTY_OFFICER,
        ]);

        $this->applicant = User::factory()->create([
            'full_name' => 'Komunitas Kreatif Malang',
            'email' => 'komunitas@mcc.test',
            'role' => UserRole::USER,
        ]);

        $this->roomL2 = Room::create([
            'name' => 'Auditorium Bioskop L2',
            'slug' => 'auditorium-bioskop-l2',
            'description' => 'Auditorium bioskop lantai 2 untuk pemutaran film dan seminar.',
            'floor_level' => 2,
            'capacity' => 120,
            'room_size_sqm' => 200,
            'base_hourly_rate' => 250000,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'supported_layouts' => ['Theater'],
            'is_active' => true,
        ]);

        $this->roomL5 = Room::create([
            'name' => 'Studio Animasi L5',
            'slug' => 'studio-animasi-l5',
            'description' => 'Studio animasi lantai 5 untuk pelatihan dan workshop.',
            'floor_level' => 5,
            'capacity' => 40,
            'room_size_sqm' => 80,
            'base_hourly_rate' => 150000,
            'buffer_minutes' => 15,
            'is_commercial_allowed' => true,
            'supported_layouts' => ['Classroom'],
            'is_active' => true,
        ]);
    }

    // =========================================================================
    // FR-ADM-06: DASBOR EKSEKUTIF & ANALITIK KINERJA TESTS
    // =========================================================================

    public function test_guest_and_staff_cannot_access_executive_analytics(): void
    {
        $guestResponse = $this->get(route('admin.analytics.index'));
        $guestResponse->assertRedirect(route('login'));

        $staffResponse = $this->actingAs($this->marketingUser)->get(route('admin.analytics.index'));
        $staffResponse->assertStatus(403);
    }

    public function test_super_admin_can_view_executive_dashboard_with_kpis(): void
    {
        // 1. Create Confirmed Booking
        $booking = Booking::create([
            'booking_code' => 'MCC-ANALYTICS-01',
            'user_id' => $this->applicant->id,
            'status' => BookingStatus::CONFIRMED,
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0,
            'clearance_status' => 'pending',
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Festival Film Malang',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Film, Animasi & Video',
            'estimated_participants' => 100,
            'event_description' => 'Festival film tahunan.',
            'proposal_file_url' => 'proposals/film.pdf',
            'pic_name_snapshot' => 'Budi Film',
            'pic_phone_snapshot' => '081234567890',
            'institution_name_snapshot' => 'Komunitas Film',
            'tos_agreed_at' => now(),
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->roomL2->id,
            'event_start_at' => Carbon::now()->startOfMonth()->setTime(10, 0),
            'event_end_at' => Carbon::now()->startOfMonth()->setTime(14, 0), // 4 hours
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.analytics.index'));

        $response->assertStatus(200);
        $response->assertSee('Dasbor Eksekutif &amp; Analitik Utilisasi Gedung MCC', false);
        $response->assertSee('Tingkat Okupansi per Tingkat Lantai');
        $response->assertSee('Distribusi 17 Sub-Sektor Ekraf');
        $response->assertSee('Film, Animasi &amp; Video', false);
    }

    public function test_super_admin_can_export_executive_analytics_to_pdf_and_csv(): void
    {
        $pdfResponse = $this->actingAs($this->superAdmin)->get(route('admin.analytics.export.pdf'));
        $pdfResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('Content-Type'));

        $csvResponse = $this->actingAs($this->superAdmin)->get(route('admin.analytics.export.csv'));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $csvResponse->headers->get('Content-Type'));
    }

    // =========================================================================
    // FR-ADM-07: AUDIT TRAIL SISTEM MENYELURUH TESTS
    // =========================================================================

    public function test_guest_and_staff_cannot_access_audit_trail(): void
    {
        $guestResponse = $this->get(route('admin.audit.index'));
        $guestResponse->assertRedirect(route('login'));

        $foResponse = $this->actingAs($this->foUser)->get(route('admin.audit.index'));
        $foResponse->assertStatus(403);
    }

    public function test_super_admin_can_view_cross_checkpoint_system_audit_trail(): void
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-AUDIT-001',
            'user_id' => $this->applicant->id,
            'status' => BookingStatus::CONFIRMED,
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0,
            'clearance_status' => 'cleared',
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Workshop Game Development',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Pengembangan Permainan (Game)',
            'estimated_participants' => 30,
            'event_description' => 'Workshop game.',
            'proposal_file_url' => 'proposals/game.pdf',
            'pic_name_snapshot' => 'Andi Gamer',
            'pic_phone_snapshot' => '081987654321',
            'institution_name_snapshot' => 'Studio Game',
            'tos_agreed_at' => now(),
        ]);

        // 1. Marketing Approval Log
        BookingApproval::create([
            'booking_id' => $booking->id,
            'action_by' => $this->marketingUser->id,
            'decision' => 'approved',
            'notes' => 'Disetujui subsidi penuh sesuai kelayakan kurasi.',
            'created_at' => now()->subHours(5),
        ]);

        // 2. KTP Vault Log
        KtpVaultLog::create([
            'booking_id' => $booking->id,
            'ktp_holder_name' => 'Andi Gamer',
            'storage_drawer_number' => 'LACI-05',
            'received_by_fo' => $this->foUser->id,
            'retained_at' => now()->subHours(4),
            'returned_by_fo' => $this->foUser->id,
            'returned_at' => now()->subHours(1),
        ]);

        // 3. DO Room Incident Log
        RoomIncident::create([
            'booking_id' => $booking->id,
            'reported_by_do' => $this->doUser->id,
            'category' => 'Kerusakan Kabel HDMI',
            'item_name' => 'Kabel HDMI 4K',
            'description' => 'Kabel HDMI putus di meja instruktur.',
            'is_resolved' => true,
            'resolution_notes' => 'Telah diganti oleh panitia.',
            'resolved_at' => now(),
            'created_at' => now()->subHours(2),
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.audit.index'));

        $response->assertStatus(200);
        $response->assertSee('Audit Trail &amp; Rekam Jejak Aktivitas Sistem Menyeluruh', false);
        $response->assertSee('MCC-AUDIT-001');
        $response->assertSee('Marketing Curation');
        $response->assertSee('Penyimpanan KTP Fisik Pemohon');
        $response->assertSee('Kerusakan Kabel HDMI');
    }

    public function test_super_admin_can_filter_audit_trail_by_type_and_search(): void
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-FILTER-LOG',
            'user_id' => $this->applicant->id,
            'status' => BookingStatus::CONFIRMED,
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0,
            'clearance_status' => 'pending',
        ]);

        BookingApproval::create([
            'booking_id' => $booking->id,
            'action_by' => $this->marketingUser->id,
            'decision' => 'approved',
            'notes' => 'Kurasi catatan khusus event unik.',
            'created_at' => now(),
        ]);

        $typeResponse = $this->actingAs($this->superAdmin)->get(route('admin.audit.index', [
            'type' => 'curation',
        ]));
        $typeResponse->assertStatus(200);
        $typeResponse->assertSee('MCC-FILTER-LOG');

        $searchResponse = $this->actingAs($this->superAdmin)->get(route('admin.audit.index', [
            'search' => 'event unik',
        ]));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('MCC-FILTER-LOG');
    }
}
