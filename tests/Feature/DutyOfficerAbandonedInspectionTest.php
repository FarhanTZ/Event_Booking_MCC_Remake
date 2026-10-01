<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\BookingStatus;
use App\Enums\RoomIncidentCategory;
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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DutyOfficerAbandonedInspectionTest extends TestCase
{
    use RefreshDatabase;

    private User $dutyOfficer;
    private User $applicant;
    private Room $roomWorkshop;
    private Booking $abandonedBooking;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

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

        // 2. Create Public User (Applicant)
        $this->applicant = User::create([
            'full_name' => 'Doni Saputra',
            'email' => 'doni.applicant@gmail.com',
            'phone_number' => '+6289999999999',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Seni Visual',
            'address' => 'Malang',
            'is_suspended' => false,
        ]);

        // 3. Create Room
        $this->roomWorkshop = Room::create([
            'id' => 202,
            'name' => 'Workshop Kreatif 1',
            'slug' => 'workshop-kreatif-1',
            'floor_level' => 2,
            'capacity' => 30,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Ruang Workshop Lantai 2 MCC.',
        ]);

        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // 4. Create Checked-In Booking
        $this->abandonedBooking = Booking::create([
            'booking_code' => 'MCC-20261001-ABN01',
            'user_id' => $this->applicant->id,
            'status' => BookingStatus::CHECKED_IN,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'regular',
            'nominal_amount' => 0.00,
            'paid_at' => Carbon::now('Asia/Jakarta')->subHours(4),
            'checked_in_at' => Carbon::now('Asia/Jakarta')->subHours(3),
            'duty_officer_id' => $this->dutyOfficer->id,
            'assigned_checkout_do_id' => $this->dutyOfficer->id,
            'room_opened_at' => Carbon::now('Asia/Jakarta')->subHours(3),
        ]);

        BookingSlot::create([
            'booking_id' => $this->abandonedBooking->id,
            'room_id' => $this->roomWorkshop->id,
            'event_start_at' => Carbon::parse("{$today} 09:00:00", 'Asia/Jakarta')->subHours(3),
            'event_end_at' => Carbon::parse("{$today} 12:00:00", 'Asia/Jakarta')->subHours(1),
            'price' => 0.00,
            'duty_officer_id' => $this->dutyOfficer->id,
        ]);

        EventDetail::create([
            'booking_id' => $this->abandonedBooking->id,
            'event_name' => 'Workshop Sablon & Cukil Kayu',
            'event_category' => 'Seni Rupa',
            'ekraf_subsector' => 'Seni Rupa',
            'event_type' => 'Non-Komersial',
            'estimated_participants' => 30,
            'institution_name_snapshot' => 'Komunitas Seni Visual',
            'pic_name_snapshot' => 'Doni Saputra',
            'pic_phone_snapshot' => '+6289999999999',
            'event_description' => 'Pelatihan teknik cetak seni manual untuk pelajar.',
            'proposal_file_url' => '/storage/proposals/workshop.pdf',
            'tos_agreed_at' => now(),
        ]);

        KtpVaultLog::create([
            'booking_id' => $this->abandonedBooking->id,
            'ktp_holder_name' => 'Doni Saputra',
            'pic_whatsapp_number' => '+6289999999999',
            'storage_drawer_number' => 'D-05',
            'received_by_fo' => $this->dutyOfficer->id,
            'received_at' => Carbon::now('Asia/Jakarta')->subHours(3),
        ]);
    }

    public function test_duty_officer_can_confirm_abandoned_room_clean_opsi_a(): void
    {
        $response = $this->actingAs($this->dutyOfficer)
            ->post(route('duty_officer.abandoned.clean', $this->abandonedBooking), [
                'notes' => 'Ruangan ditinggal dalam kondisi bersih dan pintu telah dikunci.',
                'cleanliness_checklist' => [
                    'trash' => '1',
                    'tape_posters' => '1',
                ],
                'inventory_checklist' => [
                    'intact' => '1',
                ],
            ]);

        $response->assertRedirect(route('duty_officer.dashboard'));
        $response->assertSessionHas('success');

        $this->abandonedBooking->refresh();
        $this->assertEquals(BookingStatus::ABANDONED_CLEAN, $this->abandonedBooking->status);
        $this->assertEquals('cleared', $this->abandonedBooking->clearance_status);
        $this->assertNotNull($this->abandonedBooking->completed_at);
        $this->assertEquals($this->dutyOfficer->id, $this->abandonedBooking->completed_by);
        $this->assertStringContainsString('Ditinggal Bersih', $this->abandonedBooking->checkout_do_notes);

        // Applicant account should NOT be suspended in clean case
        $this->applicant->refresh();
        $this->assertFalse((bool) $this->applicant->is_suspended);
    }

    public function test_duty_officer_can_report_abandoned_room_incident_and_suspends_user_opsi_b(): void
    {
        $dummyPhoto = UploadedFile::fake()->image('incident_abandoned.jpg', 800, 600);

        $response = $this->actingAs($this->dutyOfficer)
            ->post(route('duty_officer.abandoned.incident', $this->abandonedBooking), [
                'category' => RoomIncidentCategory::PROPERTY_DAMAGE->value,
                'item_name' => 'Meja Workshop Retak & Dinding Tercoret Cat',
                'description' => 'Panitia pulang tanpa checkout. Meja kayu utama retak dan terdapat cipratan cat sablon pada dinding sisi timur.',
                'photos' => [$dummyPhoto],
            ]);

        $response->assertRedirect(route('duty_officer.dashboard'));
        $response->assertSessionHas('success');

        $this->abandonedBooking->refresh();
        $this->assertEquals(BookingStatus::ABANDONED_INCIDENT, $this->abandonedBooking->status);
        $this->assertEquals('incident_detected', $this->abandonedBooking->clearance_status);
        $this->assertNotNull($this->abandonedBooking->completed_at);
        $this->assertEquals($this->dutyOfficer->id, $this->abandonedBooking->completed_by);

        // Verify RoomIncident record
        $this->assertDatabaseHas('room_incidents', [
            'booking_id' => $this->abandonedBooking->id,
            'reported_by_do' => $this->dutyOfficer->id,
            'category' => RoomIncidentCategory::PROPERTY_DAMAGE->value,
            'item_name' => 'Meja Workshop Retak & Dinding Tercoret Cat',
            'is_resolved' => false,
        ]);

        $incident = RoomIncident::where('booking_id', $this->abandonedBooking->id)->first();
        $this->assertCount(1, $incident->photos);

        // CRITICAL INVARIANT: Applicant user account must be automatically SUSPENDED
        $this->applicant->refresh();
        $this->assertTrue((bool) $this->applicant->is_suspended);
        $this->assertNotNull($this->applicant->suspended_at);
        $this->assertStringContainsString('Meninggalkan gedung tanpa lapor', $this->applicant->suspension_reason);
    }

    public function test_report_abandoned_incident_fails_without_photo(): void
    {
        $response = $this->actingAs($this->dutyOfficer)
            ->post(route('duty_officer.abandoned.incident', $this->abandonedBooking), [
                'category' => RoomIncidentCategory::CLEANLINESS->value,
                'item_name' => 'Sampah Sisa Konsumsi',
                'description' => 'Kotak makanan berserakan ditinggal panitia.',
                'photos' => [],
            ]);

        $response->assertSessionHasErrors(['photos']);
        $this->abandonedBooking->refresh();
        $this->assertEquals(BookingStatus::CHECKED_IN, $this->abandonedBooking->status);
    }

    public function test_confirm_abandoned_clean_supports_json_response(): void
    {
        $response = $this->actingAs($this->dutyOfficer)
            ->postJson(route('duty_officer.abandoned.clean', $this->abandonedBooking), [
                'notes' => 'Ruangan telah diperiksa aman.',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonPath('data.booking.status', BookingStatus::ABANDONED_CLEAN->value);
    }

    public function test_report_abandoned_incident_supports_json_response(): void
    {
        $dummyPhoto = UploadedFile::fake()->image('trash.jpg', 600, 400);

        $response = $this->actingAs($this->dutyOfficer)
            ->postJson(route('duty_officer.abandoned.incident', $this->abandonedBooking), [
                'category' => RoomIncidentCategory::CLEANLINESS->value,
                'item_name' => 'Sampah Makanan Ditinggal',
                'description' => 'Sampah menumpuk di pojok ruangan tanpa dibuang ke tempat sampah.',
                'photos' => [$dummyPhoto],
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                'user_suspended' => true,
            ],
        ]);
    }
}
