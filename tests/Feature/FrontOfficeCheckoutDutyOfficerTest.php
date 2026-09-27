<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FrontOfficeCheckoutDutyOfficerTest extends TestCase
{
    use RefreshDatabase;

    private User $frontOfficeUser;
    private User $publicUser;
    private User $initialDutyOfficer;
    private User $shiftDutyOfficer;
    private Room $room;
    private Booking $checkedInBooking;
    private BookingSlot $slot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->room = Room::create([
            'id' => 1,
            'name' => 'Ruang Workshop Kreatif',
            'slug' => 'ruang-workshop-kreatif',
            'floor_level' => 3,
            'capacity' => 50,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Ruang workshop kreatif lantai 3.',
        ]);

        $this->frontOfficeUser = User::create([
            'full_name' => 'FO Staf Resepsionis',
            'email' => 'fo_checkout@mcc.id',
            'phone_number' => '+6281111111111',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::FRONT_OFFICE,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Front Office MCC',
            'address' => 'Malang',
        ]);

        $this->initialDutyOfficer = User::create([
            'full_name' => 'Ahmad Subarjo',
            'email' => 'ahmad.do@mcc.id',
            'phone_number' => '081234567890',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::DUTY_OFFICER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Operasional Gedung MCC',
            'address' => 'Malang',
        ]);

        $this->shiftDutyOfficer = User::create([
            'full_name' => 'Bambang Shift Sore',
            'email' => 'bambang.do@mcc.id',
            'phone_number' => '089876543210',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::DUTY_OFFICER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Operasional Gedung MCC',
            'address' => 'Malang',
        ]);

        $this->publicUser = User::create([
            'full_name' => 'Siti Nurhaliza',
            'email' => 'siti@kreasimalang.org',
            'phone_number' => '085555555555',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Desain Grafis Malang',
            'address' => 'Malang',
        ]);

        $this->checkedInBooking = Booking::create([
            'booking_code' => 'MCC-2026-CH01',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CHECKED_IN,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
            'checkin_token' => 'token-checkout-001',
            'checked_in_at' => Carbon::now()->subHours(2),
            'checked_in_by' => $this->frontOfficeUser->id,
            'duty_officer_id' => $this->initialDutyOfficer->id,
        ]);

        $this->slot = BookingSlot::create([
            'booking_id' => $this->checkedInBooking->id,
            'room_id' => $this->room->id,
            'event_start_at' => Carbon::today()->setTime(10, 0),
            'event_end_at' => Carbon::today()->setTime(13, 0),
            'operational_range' => '["' . Carbon::today()->setTime(10, 0)->toIso8601String() . '", "' . Carbon::today()->setTime(13, 30)->toIso8601String() . '")',
            'is_active' => true,
            'duty_officer_id' => $this->initialDutyOfficer->id,
        ]);

        EventDetail::create([
            'booking_id' => $this->checkedInBooking->id,
            'event_name' => 'Workshop Tipografi Nusantara',
            'event_category' => 'Workshop',
            'ekraf_subsector' => 'Desain Komunikasi Visual',
            'estimated_participants' => 35,
            'event_description' => 'Pelatihan tipografi aksara nusantara untuk desainer muda.',
            'proposal_file_url' => 'proposals/sample.pdf',
            'pic_name_snapshot' => 'Siti Nurhaliza',
            'pic_phone_snapshot' => '085555555555',
            'institution_name_snapshot' => 'Komunitas Desain Grafis Malang',
            'tos_agreed_at' => Carbon::now()->subDays(2),
        ]);
    }

    public function test_fo_can_initiate_checkout_and_assign_same_do(): void
    {
        $payload = [
            'booking_id' => $this->checkedInBooking->id,
            'slot_id' => $this->slot->id,
            'assigned_checkout_do_id' => $this->initialDutyOfficer->id,
            'notes' => 'Panitia telah menyelesaikan workshop. Periksa kebersihan dan kabel proyektor.',
            'send_whatsapp' => true,
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.initiate_do'), $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'duty_officer' => [
                'id' => $this->initialDutyOfficer->id,
                'name' => 'Ahmad Subarjo',
            ],
        ]);

        $this->checkedInBooking->refresh();
        $this->slot->refresh();

        $this->assertEquals($this->initialDutyOfficer->id, $this->checkedInBooking->assigned_checkout_do_id);
        $this->assertEquals($this->initialDutyOfficer->id, $this->slot->assigned_checkout_do_id);
        $this->assertNotNull($this->checkedInBooking->checkout_do_assigned_at);
        $this->assertNotNull($this->checkedInBooking->checkout_do_notified_at);
        $this->assertEquals('Panitia telah menyelesaikan workshop. Periksa kebersihan dan kabel proyektor.', $this->checkedInBooking->checkout_do_notes);
    }

    public function test_fo_can_handle_shift_handoff_and_assign_replacement_do(): void
    {
        $payload = [
            'booking_id' => $this->checkedInBooking->id,
            'slot_id' => $this->slot->id,
            'assigned_checkout_do_id' => $this->shiftDutyOfficer->id,
            'notes' => 'Pergantian shift jam 13:00. Bambang menggantikan Ahmad untuk inspeksi fisik ruangan.',
            'send_whatsapp' => true,
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.initiate_do'), $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'duty_officer' => [
                'id' => $this->shiftDutyOfficer->id,
                'name' => 'Bambang Shift Sore',
            ],
        ]);

        $this->checkedInBooking->refresh();
        $this->slot->refresh();

        // Initial check-in DO remains unchanged
        $this->assertEquals($this->initialDutyOfficer->id, $this->checkedInBooking->duty_officer_id);
        // Checkout DO is set to the replacement shift DO
        $this->assertEquals($this->shiftDutyOfficer->id, $this->checkedInBooking->assigned_checkout_do_id);
        $this->assertEquals($this->shiftDutyOfficer->id, $this->slot->assigned_checkout_do_id);
    }

    public function test_whatsapp_message_matches_fr_fo_06_exact_template(): void
    {
        $payload = [
            'booking_id' => $this->checkedInBooking->id,
            'slot_id' => $this->slot->id,
            'assigned_checkout_do_id' => $this->shiftDutyOfficer->id,
            'send_whatsapp' => true,
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.initiate_do'), $payload);

        $response->assertOk();
        $data = $response->json();

        // Template FR-FO-06:
        // "Halo [Nama DO], acara [Nama Event] di [Nama Ruangan - Lantai X] telah selesai dan panitia mengajukan checkout. Mohon segera inspeksi fisik ruangan (kebersihan & inventaris fasilitas) serta input sinyal clearance di sistem."
        $expectedMessage = "Halo Bambang Shift Sore, acara Workshop Tipografi Nusantara di Ruang Workshop Kreatif - Lantai 3 telah selesai dan panitia mengajukan checkout. Mohon segera inspeksi fisik ruangan (kebersihan & inventaris fasilitas) serta input sinyal clearance di sistem.";

        $this->assertEquals($expectedMessage, $data['message_text']);
        $this->assertStringContainsString('https://api.whatsapp.com/send?phone=6289876543210', $data['whatsapp_url']);
        $this->assertStringContainsString(urlencode('Workshop Tipografi Nusantara'), $data['whatsapp_url']);
    }

    public function test_notify_checkout_do_can_resend_notification(): void
    {
        // First initiate checkout
        $this->checkedInBooking->update([
            'assigned_checkout_do_id' => $this->shiftDutyOfficer->id,
            'checkout_do_assigned_at' => Carbon::now(),
        ]);

        $payload = [
            'booking_id' => $this->checkedInBooking->id,
            'slot_id' => $this->slot->id,
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.notify_do'), $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'duty_officer' => [
                'id' => $this->shiftDutyOfficer->id,
                'name' => 'Bambang Shift Sore',
            ],
        ]);

        $this->checkedInBooking->refresh();
        $this->assertNotNull($this->checkedInBooking->checkout_do_notified_at);
    }

    public function test_initiate_checkout_fails_if_assigned_user_is_not_duty_officer(): void
    {
        $payload = [
            'booking_id' => $this->checkedInBooking->id,
            'assigned_checkout_do_id' => $this->publicUser->id, // UserRole::USER
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.initiate_do'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['assigned_checkout_do_id']);
    }

    public function test_initiate_checkout_fails_if_booking_is_not_checked_in(): void
    {
        $unconfirmedBooking = Booking::create([
            'booking_code' => 'MCC-2026-UNCONF',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::AWAITING_MARKETING,
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
        ]);

        $payload = [
            'booking_id' => $unconfirmedBooking->id,
            'assigned_checkout_do_id' => $this->shiftDutyOfficer->id,
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.initiate_do'), $payload);

        $response->assertStatus(422);
    }

    public function test_regular_user_cannot_initiate_checkout(): void
    {
        $payload = [
            'booking_id' => $this->checkedInBooking->id,
            'assigned_checkout_do_id' => $this->shiftDutyOfficer->id,
        ];

        $response = $this->actingAs($this->publicUser)
            ->postJson(route('front_office.checkout.initiate_do'), $payload);

        $response->assertForbidden();
    }
}
