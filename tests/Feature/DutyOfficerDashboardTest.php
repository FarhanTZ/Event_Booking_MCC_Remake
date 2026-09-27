<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\KtpVaultLog;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DutyOfficerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $dutyOfficer;
    private User $otherDutyOfficer;
    private User $publicUser;
    private Room $roomFloor3;
    private Room $roomFloor7;
    private Booking $assignedBooking;
    private Booking $otherBooking;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Duty Officers
        $this->dutyOfficer = User::create([
            'full_name' => 'Bambang Pamungkas',
            'email' => 'bambang.do@mcc.id',
            'phone_number' => '+6281234567801',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::DUTY_OFFICER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'MCC Operations DO',
            'address' => 'Malang',
        ]);

        $this->otherDutyOfficer = User::create([
            'full_name' => 'Siti Nurhaliza',
            'email' => 'siti.do@mcc.id',
            'phone_number' => '+6281234567802',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::DUTY_OFFICER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'MCC Operations DO',
            'address' => 'Malang',
        ]);

        // 2. Create Public User
        $this->publicUser = User::create([
            'full_name' => 'Rahmat Hidayat',
            'email' => 'rahmat.applicant@gmail.com',
            'phone_number' => '+6289999999999',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Kreatif Malang',
            'address' => 'Malang',
        ]);

        // 3. Create Rooms on different floors
        $this->roomFloor3 = Room::create([
            'id' => 101,
            'name' => 'Food Lab & Kitchen',
            'slug' => 'food-lab-kitchen',
            'floor_level' => 3,
            'capacity' => 30,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Ruang Food Lab di Lantai 3.',
        ]);

        $this->roomFloor7 = Room::create([
            'id' => 102,
            'name' => 'Grand Auditorium',
            'slug' => 'grand-auditorium',
            'floor_level' => 7,
            'capacity' => 500,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Auditorium utama di Lantai 7.',
        ]);

        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // 4. Create Booking assigned to $this->dutyOfficer (Lantai 7, Checked-In)
        $this->assignedBooking = Booking::create([
            'booking_code' => 'MCC-20260927-DO001',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CHECKED_IN,
            'clearance_status' => 'pending',
            'duty_officer_id' => $this->dutyOfficer->id,
            'checked_in_at' => Carbon::now('Asia/Jakarta')->subHours(1),
        ]);

        BookingSlot::create([
            'booking_id' => $this->assignedBooking->id,
            'room_id' => $this->roomFloor7->id,
            'duty_officer_id' => $this->dutyOfficer->id,
            'event_start_at' => Carbon::parse("{$today} 09:00:00", 'Asia/Jakarta'),
            'event_end_at' => Carbon::parse("{$today} 13:00:00", 'Asia/Jakarta'),
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $this->assignedBooking->id,
            'event_name' => 'Malang Game Developer Showcase 2026',
            'event_category' => 'Game & Aplikasi',
            'ekraf_subsector' => 'Game Developer',
            'event_type' => 'Non-Komersial',
            'estimated_participants' => 200,
            'event_description' => 'Showcase karya game developer lokal.',
            'equipment_needed' => "Sound Mixer Eksternal 1 set\nLighting RGB 4 unit\nStand Banner 2 pcs",
            'proposal_file_url' => '/storage/proposals/test.pdf',
            'pic_name_snapshot' => 'Budi Setiawan',
            'pic_phone_snapshot' => '081234567890',
            'institution_name_snapshot' => 'Game Dev Malang',
            'tos_agreed_at' => now(),
        ]);

        KtpVaultLog::create([
            'booking_id' => $this->assignedBooking->id,
            'ktp_holder_name' => 'Budi Setiawan',
            'pic_whatsapp_number' => '081234567890',
            'storage_drawer_number' => 'LACI-A07',
            'received_by_fo' => $this->dutyOfficer->id,
            'retained_at' => now(),
        ]);

        // 5. Create Booking assigned to $this->otherDutyOfficer (Lantai 3, Confirmed / Upcoming)
        $this->otherBooking = Booking::create([
            'booking_code' => 'MCC-20260927-DO002',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CONFIRMED,
            'clearance_status' => 'pending',
            'duty_officer_id' => $this->otherDutyOfficer->id,
        ]);

        BookingSlot::create([
            'booking_id' => $this->otherBooking->id,
            'room_id' => $this->roomFloor3->id,
            'duty_officer_id' => $this->otherDutyOfficer->id,
            'event_start_at' => Carbon::parse("{$today} 17:00:00", 'Asia/Jakarta'),
            'event_end_at' => Carbon::parse("{$today} 21:00:00", 'Asia/Jakarta'),
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $this->otherBooking->id,
            'event_name' => 'Workshop Kuliner Nusantara',
            'event_category' => 'Kuliner',
            'ekraf_subsector' => 'Kuliner',
            'event_type' => 'Non-Komersial',
            'estimated_participants' => 25,
            'event_description' => 'Pelatihan memasak inovasi.',
            'equipment_needed' => 'Kompor Induksi Portabel 2 unit',
            'proposal_file_url' => '/storage/proposals/test2.pdf',
            'pic_name_snapshot' => 'Dewi Anggraini',
            'pic_phone_snapshot' => '089876543210',
            'institution_name_snapshot' => 'Komunitas Kuliner Hebat',
            'tos_agreed_at' => now(),
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_do_dashboard(): void
    {
        $response = $this->get(route('duty_officer.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_is_forbidden_from_accessing_do_dashboard(): void
    {
        $response = $this->actingAs($this->publicUser)->get(route('duty_officer.dashboard'));
        $response->assertStatus(403);
    }

    public function test_duty_officer_user_is_redirected_to_do_dashboard_on_login(): void
    {
        $response = $this->post('/login', [
            'email' => $this->dutyOfficer->email,
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('duty_officer.dashboard'));
    }

    public function test_duty_officer_can_view_dashboard_with_assigned_tasks(): void
    {
        $response = $this->actingAs($this->dutyOfficer)->get(route('duty_officer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Grand Auditorium');
        $response->assertSee('Lantai 7');
        $response->assertSee('Malang Game Developer Showcase 2026');
        $response->assertSee('Game Dev Malang');
        $response->assertSee('Budi Setiawan');
        $response->assertSee('081234567890');
        $response->assertSee('Sound Mixer Eksternal 1 set');
        $response->assertSee('Lighting RGB 4 unit');
    }

    public function test_duty_officer_default_scope_only_shows_assigned_tasks(): void
    {
        // Default scope is 'assigned'
        $response = $this->actingAs($this->dutyOfficer)->get(route('duty_officer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('MCC-20260927-DO001');
        // otherDutyOfficer's task should not appear in 'assigned' scope for this DO
        $response->assertDontSee('MCC-20260927-DO002');
    }

    public function test_duty_officer_can_switch_scope_to_view_all_floor_tasks(): void
    {
        $response = $this->actingAs($this->dutyOfficer)->get(route('duty_officer.dashboard', [
            'scope' => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertSee('MCC-20260927-DO001');
        $response->assertSee('MCC-20260927-DO002');
    }

    public function test_duty_officer_can_filter_tasks_by_floor(): void
    {
        // Filter Floor 3 on scope 'all'
        $response = $this->actingAs($this->dutyOfficer)->get(route('duty_officer.dashboard', [
            'scope' => 'all',
            'floor' => 3,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Food Lab & Kitchen');
        $response->assertDontSee('Grand Auditorium');
    }

    public function test_duty_officer_can_filter_tasks_by_shift(): void
    {
        // Morning shift (< 16:00) should contain DO001 (09:00 - 13:00)
        $response = $this->actingAs($this->dutyOfficer)->get(route('duty_officer.dashboard', [
            'scope' => 'all',
            'shift' => 'morning',
        ]));

        $response->assertStatus(200);
        $response->assertSee('MCC-20260927-DO001');
        $response->assertDontSee('MCC-20260927-DO002');

        // Evening shift (>= 16:00) should contain DO002 (17:00 - 21:00)
        $response = $this->actingAs($this->dutyOfficer)->get(route('duty_officer.dashboard', [
            'scope' => 'all',
            'shift' => 'evening',
        ]));

        $response->assertStatus(200);
        $response->assertSee('MCC-20260927-DO002');
        $response->assertDontSee('MCC-20260927-DO001');
    }

    public function test_duty_officer_dashboard_renders_operational_whatsapp_link(): void
    {
        $response = $this->actingAs($this->dutyOfficer)->get(route('duty_officer.dashboard'));

        $response->assertStatus(200);
        // Direct WhatsApp Click-to-Chat URL
        $expectedWaUrl = $this->assignedBooking->getPicWhatsAppUrl($this->dutyOfficer->full_name);
        $this->assertNotEmpty($expectedWaUrl);
        $response->assertSee($expectedWaUrl, false);
    }

    public function test_duty_officer_dashboard_supports_json_request(): void
    {
        $response = $this->actingAs($this->dutyOfficer)->json('GET', route('duty_officer.dashboard'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'tasks',
            'metrics' => [
                'total',
                'checked_in',
                'upcoming',
                'completed',
            ],
            'floors',
            'filter',
            'target_date',
        ]);
    }
}
