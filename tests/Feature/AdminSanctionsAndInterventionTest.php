<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingApproval;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\Room;
use App\Models\RoomIncident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSanctionsAndInterventionTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $marketingUser;
    private User $applicant;
    private User $suspendedApplicant;
    private Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'full_name' => 'Super Admin Test',
            'email' => 'superadmin@mcc.test',
            'phone_number' => '081111111111',
            'role' => UserRole::SUPER_ADMIN,
            'is_suspended' => false,
        ]);

        $this->marketingUser = User::factory()->create([
            'full_name' => 'Marketing Staff',
            'email' => 'marketing@mcc.test',
            'phone_number' => '082222222222',
            'role' => UserRole::MARKETING,
            'is_suspended' => false,
        ]);

        $this->applicant = User::factory()->create([
            'full_name' => 'Komunitas Film Malang',
            'email' => 'film@mcc.test',
            'phone_number' => '083333333333',
            'role' => UserRole::USER,
            'is_suspended' => false,
        ]);

        $this->suspendedApplicant = User::factory()->create([
            'full_name' => 'Pelanggar Gedung Blacklist',
            'email' => 'blacklist@mcc.test',
            'phone_number' => '084444444444',
            'role' => UserRole::USER,
            'is_suspended' => true,
            'suspension_reason' => 'Panitia kabur tanpa checkout dan merusak fasilitas sound system.',
            'suspended_at' => now(),
        ]);

        $this->room = Room::create([
            'name' => 'Bioskop Mini Lantai 5',
            'slug' => 'bioskop-mini-lantai-5',
            'description' => 'Ruangan bioskop mini untuk pemutaran film dan workshop.',
            'floor_level' => 5,
            'capacity' => 60,
            'room_size_sqm' => 120,
            'base_hourly_rate' => 150000,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'supported_layouts' => ['Theater', 'Classroom'],
            'is_active' => true,
        ]);
    }

    // =========================================================================
    // FR-ADM-04: SANKSI & BLACKLIST PEMOHON TESTS
    // =========================================================================

    public function test_guest_and_non_super_admin_cannot_access_sanctions_queue(): void
    {
        $guestResponse = $this->get(route('admin.sanctions.index'));
        $guestResponse->assertRedirect(route('login'));

        $marketingResponse = $this->actingAs($this->marketingUser)->get(route('admin.sanctions.index'));
        $marketingResponse->assertStatus(403);

        $applicantResponse = $this->actingAs($this->applicant)->get(route('admin.sanctions.index'));
        $applicantResponse->assertStatus(403);
    }

    public function test_super_admin_can_view_sanctions_queue_and_metrics(): void
    {
        // Create an abandoned incident booking
        $abandonedBooking = Booking::create([
            'booking_code' => 'MCC-ABANDON-01',
            'user_id' => $this->applicant->id,
            'status' => BookingStatus::ABANDONED_INCIDENT,
            'nominal_amount' => 0,
            'pricing_scheme' => 'free',
            'clearance_status' => 'incident_detected',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.sanctions.index'));

        $response->assertStatus(200);
        $response->assertSee('Penegakan Sanksi &amp; Daftar Hitam (Blacklist) Pemohon', false);
        $response->assertSee('Komunitas Film Malang');
        $response->assertSee('Pelanggar Gedung Blacklist');
        $response->assertSee('Dibekukan (Blacklist)');
    }

    public function test_super_admin_can_filter_sanctions_by_status_and_search(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.sanctions.index', [
            'status' => 'suspended',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Pelanggar Gedung Blacklist');
        $response->assertDontSee('Komunitas Film Malang');

        $searchResponse = $this->actingAs($this->superAdmin)->get(route('admin.sanctions.index', [
            'search' => 'Komunitas Film',
        ]));

        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Komunitas Film Malang');
        $searchResponse->assertDontSee('Pelanggar Gedung Blacklist');
    }

    public function test_super_admin_can_suspend_applicant_with_valid_reason(): void
    {
        $response = $this->actingAs($this->superAdmin)->patch(route('admin.sanctions.suspend', $this->applicant->id), [
            'suspension_reason' => 'Merusak proyektor ruang bioskop dan meninggalkan lokasi tanpa penyelesaian.',
        ]);

        $response->assertRedirect(route('admin.sanctions.index'));
        $response->assertSessionHas('success');

        $this->applicant->refresh();
        $this->assertTrue($this->applicant->is_suspended);
        $this->assertEquals('Merusak proyektor ruang bioskop dan meninggalkan lokasi tanpa penyelesaian.', $this->applicant->suspension_reason);
        $this->assertNotNull($this->applicant->suspended_at);
    }

    public function test_suspend_applicant_validation_requires_minimum_length(): void
    {
        $response = $this->actingAs($this->superAdmin)->patch(route('admin.sanctions.suspend', $this->applicant->id), [
            'suspension_reason' => 'Pendek',
        ]);

        $response->assertSessionHasErrors(['suspension_reason']);
        $this->applicant->refresh();
        $this->assertFalse($this->applicant->is_suspended);
    }

    public function test_suspended_user_is_blocked_from_locking_calendar_slots_and_booking_wizard(): void
    {
        // 1. Attempt slot lock on calendar
        $lockResponse = $this->actingAs($this->suspendedApplicant)->postJson(route('calendar.lock'), [
            'room_id' => $this->room->id,
            'date' => now()->addDays(5)->toDateString(),
            'slot_time' => '10:00 - 11:00',
        ]);

        $lockResponse->assertStatus(403);
        $lockResponse->assertJsonFragment(['success' => false]);

        // 2. Attempt accessing booking wizard
        $wizardResponse = $this->actingAs($this->suspendedApplicant)->get(route('booking.wizard', [
            'room_id' => $this->room->id,
            'date' => now()->addDays(5)->toDateString(),
            'slot' => '10:00 - 11:00',
        ]));

        $wizardResponse->assertRedirect(route('dashboard'));
        $wizardResponse->assertSessionHas('error');
    }

    public function test_super_admin_can_lift_applicant_suspension(): void
    {
        $response = $this->actingAs($this->superAdmin)->patch(route('admin.sanctions.lift', $this->suspendedApplicant->id), [
            'settlement_notes' => 'Telah melunasi biaya perbaikan dan menyampaikan surat permohonan maaf resmi.',
        ]);

        $response->assertRedirect(route('admin.sanctions.index'));
        $response->assertSessionHas('success');

        $this->suspendedApplicant->refresh();
        $this->assertFalse($this->suspendedApplicant->is_suspended);
        $this->assertNull($this->suspendedApplicant->suspension_reason);
        $this->assertNull($this->suspendedApplicant->suspended_at);
    }

    // =========================================================================
    // FR-ADM-05: INTERVENSI & PEMBATALAN PAKSA DARURAT TESTS
    // =========================================================================

    public function test_guest_and_non_super_admin_cannot_access_interventions(): void
    {
        $guestResponse = $this->get(route('admin.interventions.index'));
        $guestResponse->assertRedirect(route('login'));

        $marketingResponse = $this->actingAs($this->marketingUser)->get(route('admin.interventions.index'));
        $marketingResponse->assertStatus(403);
    }

    public function test_super_admin_can_view_intervention_bookings_list(): void
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-TEST-001',
            'user_id' => $this->applicant->id,
            'status' => BookingStatus::CONFIRMED,
            'nominal_amount' => 0,
            'pricing_scheme' => 'free',
            'clearance_status' => 'pending',
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Festival Sinema Malang 2026',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Film',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 50,
            'event_description' => 'Penayangan karya sineas muda kota Malang.',
            'proposal_file_url' => 'proposals/test.pdf',
            'institution_name_snapshot' => 'Komunitas Film Malang',
            'pic_name_snapshot' => 'Budi Sutradara',
            'pic_phone_snapshot' => '081234567890',
            'tos_agreed_at' => now(),
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => now()->addDays(2)->setTime(13, 0),
            'event_end_at' => now()->addDays(2)->setTime(15, 0),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.interventions.index'));

        $response->assertStatus(200);
        $response->assertSee('Intervensi Reservasi &amp; Pembatalan Paksa Darurat', false);
        $response->assertSee('MCC-TEST-001');
        $response->assertSee('Festival Sinema Malang 2026');
        $response->assertSee('Batalkan Paksa');
    }

    public function test_super_admin_can_execute_emergency_override_cancellation(): void
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-EMERGENCY-99',
            'user_id' => $this->applicant->id,
            'status' => BookingStatus::CONFIRMED,
            'nominal_amount' => 0,
            'pricing_scheme' => 'free',
            'clearance_status' => 'pending',
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Workshop Animasi Digital',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Aplikasi',
            'event_type' => 'Internal',
            'estimated_participants' => 30,
            'event_description' => 'Workshop animasi dasar.',
            'proposal_file_url' => 'proposals/test2.pdf',
            'institution_name_snapshot' => 'Studio Animasi Malang',
            'pic_name_snapshot' => 'Andi Designer',
            'pic_phone_snapshot' => '081987654321',
            'tos_agreed_at' => now(),
        ]);

        $slot1 = BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => now()->addDays(1)->setTime(9, 0),
            'event_end_at' => now()->addDays(1)->setTime(10, 0),
            'is_active' => true,
        ]);

        $slot2 = BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => now()->addDays(1)->setTime(10, 0),
            'event_end_at' => now()->addDays(1)->setTime(11, 0),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.interventions.cancel', $booking->id), [
            'emergency_category' => 'agenda_pemerintah_darurat',
            'cancellation_reason' => 'Penggunaan mendadak untuk Rapat Koordinasi Forkopimda bersama Pj Walikota Malang.',
        ]);

        $response->assertRedirect(route('admin.interventions.index'));
        $response->assertSessionHas('success');

        // 1. Status updated to CANCELLED
        $booking->refresh();
        $this->assertEquals(BookingStatus::CANCELLED, $booking->status);

        // 2. Slots immediately deactivated
        $slot1->refresh();
        $slot2->refresh();
        $this->assertFalse($slot1->is_active);
        $this->assertFalse($slot2->is_active);

        // 3. Permanent audit trail created
        $approval = BookingApproval::where('booking_id', $booking->id)->where('decision', 'emergency_cancelled')->first();
        $this->assertNotNull($approval);
        $this->assertEquals($this->superAdmin->id, $approval->action_by);
        $this->assertStringContainsString('SUPER OVERRIDE', $approval->notes);
        $this->assertStringContainsString('Rapat Koordinasi Forkopimda', $approval->notes);
    }

    public function test_emergency_cancellation_validation_requires_reason(): void
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-VAL-001',
            'user_id' => $this->applicant->id,
            'status' => BookingStatus::CONFIRMED,
            'nominal_amount' => 0,
            'pricing_scheme' => 'free',
            'clearance_status' => 'pending',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('admin.interventions.cancel', $booking->id), [
            'emergency_category' => 'agenda_pemerintah_darurat',
            'cancellation_reason' => 'Pendek',
        ]);

        $response->assertSessionHasErrors(['cancellation_reason']);
        $booking->refresh();
        $this->assertEquals(BookingStatus::CONFIRMED, $booking->status);
    }
}
