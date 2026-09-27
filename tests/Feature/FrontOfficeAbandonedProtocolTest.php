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
use App\Models\RoomIncident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontOfficeAbandonedProtocolTest extends TestCase
{
    use RefreshDatabase;

    protected User $frontOfficeUser;
    protected User $applicantUser;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->frontOfficeUser = User::create([
            'role' => UserRole::FRONT_OFFICE,
            'account_type' => AccountType::INSTANSI,
            'full_name' => 'Petugas Front Office',
            'institution_name' => 'Kantor Pengelola MCC',
            'address' => 'Jl. A. Yani No. 289 Malang',
            'email' => 'fo_abandoned@mcc.id',
            'phone_number' => '081234567890',
            'password_hash' => bcrypt('Password123!'),
        ]);

        $this->applicantUser = User::create([
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'full_name' => 'Ketua Panitia Komunitas',
            'institution_name' => 'Komunitas Kreatif Malang',
            'address' => 'Jl. Soekarno Hatta Malang',
            'email' => 'panitia_abandoned@mcc.id',
            'phone_number' => '089876543210',
            'password_hash' => bcrypt('Password123!'),
        ]);

        $this->room = Room::create([
            'name' => 'Auditorium Lt 2',
            'slug' => 'auditorium-lt-2',
            'floor_level' => 2,
            'capacity' => 100,
            'is_active' => true,
            'description' => 'Auditorium lantai 2 MCC',
        ]);
    }

    private function createCheckedInBooking(string $clearanceStatus = 'pending'): Booking
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-' . strtoupper(bin2hex(random_bytes(4))),
            'user_id' => $this->applicantUser->id,
            'status' => BookingStatus::CHECKED_IN,
            'clearance_status' => $clearanceStatus,
            'pricing_scheme' => 'subsidy',
            'checked_in_at' => Carbon::now()->subHours(3),
            'checked_in_by' => $this->frontOfficeUser->id,
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Workshop Desain Grafis',
            'event_description' => 'Workshop desain grafis dan kreatif.',
            'ekraf_subsector' => 'Desain Komunikasi Visual',
            'pic_name_snapshot' => 'Ketua Panitia Komunitas',
            'pic_phone_snapshot' => '089876543210',
            'institution_name_snapshot' => 'Komunitas Kreatif Malang',
            'estimated_participants' => 50,
            'event_category' => 'Workshop',
            'proposal_file_url' => '/storage/proposals/test.pdf',
            'tos_agreed_at' => now(),
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => Carbon::today()->setTime(9, 0),
            'event_end_at' => Carbon::today()->setTime(12, 0),
            'operational_range' => "[".Carbon::today()->setTime(9, 0)->toIso8601String().", ".Carbon::today()->setTime(12, 30)->toIso8601String().")",
            'is_active' => true,
        ]);

        KtpVaultLog::create([
            'booking_id' => $booking->id,
            'ktp_holder_name' => 'Ketua Panitia Komunitas',
            'pic_whatsapp_number' => '089876543210',
            'storage_drawer_number' => 'LACI-04',
            'nik_number' => '3573012345678901',
            'received_by_fo' => $this->frontOfficeUser->id,
            'retained_at' => Carbon::now()->subHours(3),
        ]);

        return $booking;
    }

    public function test_fo_can_mark_clean_booking_as_abandoned_clean(): void
    {
        $booking = $this->createCheckedInBooking(clearanceStatus: 'cleared');

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.abandoned.mark'), [
                'booking_id' => $booking->id,
                'unclaimed_box_id' => 'BOX-ARSIP-01',
                'notes' => 'Panitia tidak kembali setelah acara berakhir, ruangan diperiksa bersih.',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'user_suspended' => false,
            'unclaimed_box_id' => 'BOX-ARSIP-01',
        ]);

        $booking->refresh();
        $this->assertEquals(BookingStatus::ABANDONED_CLEAN, $booking->status);
        $this->assertNotNull($booking->completed_at);
        $this->assertEquals($this->frontOfficeUser->id, $booking->completed_by);

        // KTP Vault log has unclaimed_box_id updated
        $ktpLog = $booking->latestKtpVaultLog;
        $this->assertEquals('BOX-ARSIP-01', $ktpLog->unclaimed_box_id);
        $this->assertStringContainsString('Panitia tidak kembali', $ktpLog->notes);

        // User is NOT suspended because room was clean
        $this->applicantUser->refresh();
        $this->assertFalse($this->applicantUser->isSuspended());
    }

    public function test_fo_marking_incident_booking_as_abandoned_automatically_suspends_user(): void
    {
        $booking = $this->createCheckedInBooking(clearanceStatus: 'incident_detected');

        RoomIncident::create([
            'booking_id' => $booking->id,
            'reported_by_do' => $this->frontOfficeUser->id,
            'category' => 'damage',
            'item_name' => 'Kabel Proyektor HDMI & Remote AC Hilang',
            'description' => 'Kabel HDMI putus dan remote AC tidak ada di meja ruangan.',
            'is_resolved' => false,
        ]);

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.abandoned.mark'), [
                'booking_id' => $booking->id,
                'unclaimed_box_id' => 'BOX-INCIDENT-99',
                'notes' => 'Panitia kabur tanpa ganti rugi kabel HDMI.',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'user_suspended' => true,
            'unclaimed_box_id' => 'BOX-INCIDENT-99',
        ]);

        $booking->refresh();
        $this->assertEquals(BookingStatus::ABANDONED_INCIDENT, $booking->status);

        // User IS automatically suspended
        $this->applicantUser->refresh();
        $this->assertTrue($this->applicantUser->isSuspended());
        $this->assertNotNull($this->applicantUser->suspended_at);
        $this->assertStringContainsString($booking->booking_code, $this->applicantUser->suspension_reason);
    }

    public function test_mark_abandoned_fails_if_booking_is_not_checked_in(): void
    {
        $booking = $this->createCheckedInBooking();
        $booking->update(['status' => BookingStatus::CONFIRMED]);

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.abandoned.mark'), [
                'booking_id' => $booking->id,
                'unclaimed_box_id' => 'BOX-01',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_mark_abandoned_fails_if_unclaimed_box_id_missing(): void
    {
        $booking = $this->createCheckedInBooking();

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.abandoned.mark'), [
                'booking_id' => $booking->id,
                'unclaimed_box_id' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['unclaimed_box_id']);
    }

    public function test_regular_user_cannot_mark_abandoned(): void
    {
        $booking = $this->createCheckedInBooking();

        $response = $this->actingAs($this->applicantUser)
            ->postJson(route('front_office.abandoned.mark'), [
                'booking_id' => $booking->id,
                'unclaimed_box_id' => 'BOX-01',
            ]);

        $response->assertForbidden();
    }

    public function test_fo_can_get_overdue_pic_whatsapp_url(): void
    {
        $booking = $this->createCheckedInBooking();

        $response = $this->actingAs($this->frontOfficeUser)
            ->getJson(route('front_office.overdue.notify_pic', $booking->id));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'whatsapp_url',
            'message',
            'phone',
        ]);
        $this->assertStringContainsString('api.whatsapp.com', $response->json('whatsapp_url'));
        $this->assertStringContainsString('6289876543210', $response->json('whatsapp_url'));
    }

    public function test_booking_is_overdue_for_checkout_helper(): void
    {
        $booking = $this->createCheckedInBooking();
        
        // At 12:20 (20 mins past 12:00 end) -> not overdue (tolerance is 30 mins)
        $simulatedTime1 = Carbon::today()->setTime(12, 20);
        $this->assertFalse($booking->isOverdueForCheckout($simulatedTime1));

        // At 12:35 (35 mins past 12:00 end) -> overdue!
        $simulatedTime2 = Carbon::today()->setTime(12, 35);
        $this->assertTrue($booking->isOverdueForCheckout($simulatedTime2));
    }
}
