<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DutyOfficerRoomOpeningTest extends TestCase
{
    use RefreshDatabase;

    private User $dutyOfficer;
    private User $publicUser;
    private Room $roomAuditorium;
    private Booking $checkedInBooking;
    private Booking $confirmedBooking;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Duty Officer
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

        // 3. Create Room & Room Facilities
        $this->roomAuditorium = Room::create([
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

        RoomFacility::create([
            'room_id' => $this->roomAuditorium->id,
            'facility_name' => 'AC Split Duct 5 PK',
            'quantity' => 4,
            'unit' => 'unit',
        ]);

        RoomFacility::create([
            'room_id' => $this->roomAuditorium->id,
            'facility_name' => 'Proyektor Laser 10.000 Lumens',
            'quantity' => 1,
            'unit' => 'set',
        ]);

        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // 4. Create Checked-In Booking (Needs room opening)
        $this->checkedInBooking = Booking::create([
            'booking_code' => 'MCC-20260927-OPEN01',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CHECKED_IN,
            'clearance_status' => 'pending',
            'duty_officer_id' => $this->dutyOfficer->id,
            'checked_in_at' => Carbon::now('Asia/Jakarta')->subMinutes(15),
            'room_opened_at' => null,
            'room_opened_by' => null,
        ]);

        BookingSlot::create([
            'booking_id' => $this->checkedInBooking->id,
            'room_id' => $this->roomAuditorium->id,
            'duty_officer_id' => $this->dutyOfficer->id,
            'event_start_at' => Carbon::parse("{$today} 09:00:00", 'Asia/Jakarta'),
            'event_end_at' => Carbon::parse("{$today} 13:00:00", 'Asia/Jakarta'),
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $this->checkedInBooking->id,
            'event_name' => 'Seminar Teknologi Nasional',
            'event_category' => 'Teknologi',
            'ekraf_subsector' => 'Aplikasi',
            'event_type' => 'Non-Komersial',
            'estimated_participants' => 300,
            'event_description' => 'Seminar teknologi tingkat nasional.',
            'equipment_needed' => "Kamera Mirrorless 2 set\nAudio Wireless 4 mic",
            'proposal_file_url' => '/storage/proposals/seminar.pdf',
            'pic_name_snapshot' => 'Hendra Gunawan',
            'pic_phone_snapshot' => '081234567890',
            'institution_name_snapshot' => 'Tech Malang',
            'tos_agreed_at' => now(),
        ]);

        // 5. Create Confirmed Booking (Not yet checked-in)
        $this->confirmedBooking = Booking::create([
            'booking_code' => 'MCC-20260927-OPEN02',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CONFIRMED,
            'clearance_status' => 'pending',
            'duty_officer_id' => $this->dutyOfficer->id,
        ]);

        BookingSlot::create([
            'booking_id' => $this->confirmedBooking->id,
            'room_id' => $this->roomAuditorium->id,
            'duty_officer_id' => $this->dutyOfficer->id,
            'event_start_at' => Carbon::parse("{$today} 14:00:00", 'Asia/Jakarta'),
            'event_end_at' => Carbon::parse("{$today} 18:00:00", 'Asia/Jakarta'),
            'is_active' => true,
        ]);
    }

    public function test_duty_officer_sees_checked_in_task_in_needs_opening_tab(): void
    {
        $response = $this->actingAs($this->dutyOfficer)->get(route('duty_officer.dashboard', [
            'status' => 'needs_opening',
        ]));

        $response->assertStatus(200);
        $response->assertSee('MCC-20260927-OPEN01');
        $response->assertSee('Buka Akses Ruangan');
        $response->assertSee('Seminar Teknologi Nasional');
        $response->assertDontSee('MCC-20260927-OPEN02');
    }

    public function test_duty_officer_can_confirm_room_opening_with_facility_checklist(): void
    {
        $response = $this->actingAs($this->dutyOfficer)->post(
            route('duty_officer.confirm_opening', $this->checkedInBooking),
            [
                'facility_checklist' => [
                    'ac' => '1',
                    'electricity' => '1',
                    'projector' => '1',
                    'furniture_layout' => '1',
                    'cleanliness' => '1',
                ],
                'notes' => 'Kunci dan AC 22C telah diserahterimakan kepada PIC Hendra.',
            ]
        );

        $response->assertRedirect(route('duty_officer.dashboard'));
        $response->assertSessionHas('success');

        // Verify database updates
        $this->checkedInBooking->refresh();
        $this->assertNotNull($this->checkedInBooking->room_opened_at);
        $this->assertEquals($this->dutyOfficer->id, $this->checkedInBooking->room_opened_by);
        $this->assertEquals('Kunci dan AC 22C telah diserahterimakan kepada PIC Hendra.', $this->checkedInBooking->room_open_notes);
        $this->assertTrue($this->checkedInBooking->isRoomOpened());
        $this->assertFalse($this->checkedInBooking->needsRoomOpening());

        // Verify slot update
        $slot = $this->checkedInBooking->slots->first();
        $this->assertNotNull($slot->room_opened_at);
        $this->assertEquals($this->dutyOfficer->id, $slot->room_opened_by);
    }

    public function test_unauthenticated_or_unauthorized_user_cannot_confirm_room_opening(): void
    {
        // 1. Guest
        $guestResponse = $this->post(
            route('duty_officer.confirm_opening', $this->checkedInBooking),
            ['facility_checklist' => ['ac' => '1']]
        );
        $guestResponse->assertRedirect(route('login'));

        // 2. Regular Public User
        $userResponse = $this->actingAs($this->publicUser)->post(
            route('duty_officer.confirm_opening', $this->checkedInBooking),
            ['facility_checklist' => ['ac' => '1']]
        );
        $userResponse->assertStatus(403);
    }

    public function test_cannot_confirm_room_opening_for_booking_that_is_not_checked_in(): void
    {
        // 1. Web request returns redirect back with session error
        $response = $this->actingAs($this->dutyOfficer)->post(
            route('duty_officer.confirm_opening', $this->confirmedBooking),
            [
                'facility_checklist' => ['ac' => '1'],
                'notes' => 'Mencoba buka sebelum check-in',
            ]
        );

        $response->assertSessionHasErrors('booking');

        // 2. JSON request returns 422 Unprocessable Entity
        $jsonResponse = $this->actingAs($this->dutyOfficer)->json(
            'POST',
            route('duty_officer.confirm_opening', $this->confirmedBooking),
            [
                'facility_checklist' => ['ac' => '1'],
                'notes' => 'Mencoba buka sebelum check-in',
            ]
        );

        $jsonResponse->assertStatus(422);

        $this->confirmedBooking->refresh();
        $this->assertNull($this->confirmedBooking->room_opened_at);
    }

    public function test_confirm_room_opening_supports_json_response(): void
    {
        $response = $this->actingAs($this->dutyOfficer)->json(
            'POST',
            route('duty_officer.confirm_opening', $this->checkedInBooking),
            [
                'facility_checklist' => [
                    'ac' => '1',
                    'electricity' => '1',
                ],
                'notes' => 'API confirmation test',
            ]
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'booking' => [
                    'id' => $this->checkedInBooking->id,
                    'booking_code' => 'MCC-20260927-OPEN01',
                ],
            ],
        ]);
    }
}
