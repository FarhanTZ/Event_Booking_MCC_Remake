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
use App\Models\RoomFacility;
use App\Models\RoomIncident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DutyOfficerRoomCheckoutInspectionTest extends TestCase
{
    use RefreshDatabase;

    private User $dutyOfficer;
    private User $frontOfficeUser;
    private User $publicUser;
    private Room $roomStudio;
    private Booking $checkoutBooking;

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

        // 2. Create Front Office Staf
        $this->frontOfficeUser = User::create([
            'full_name' => 'Siti Nurhaliza',
            'email' => 'siti.fo@mcc.id',
            'phone_number' => '+6281234567802',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::FRONT_OFFICE,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Front Office MCC',
            'address' => 'Malang',
        ]);

        // 3. Create Public User
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

        // 4. Create Room
        $this->roomStudio = Room::create([
            'id' => 105,
            'name' => 'Studio Animasi & Desain',
            'slug' => 'studio-animasi-desain',
            'floor_level' => 4,
            'capacity' => 40,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Studio di Lantai 4 MCC.',
        ]);

        RoomFacility::create([
            'room_id' => $this->roomStudio->id,
            'facility_name' => 'Kabel HDMI 4K',
            'quantity' => 2,
            'unit' => 'unit',
        ]);

        $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

        // 5. Create Checked-In Booking with Checkout initiated
        $this->checkoutBooking = Booking::create([
            'booking_code' => 'MCC-20261001-CHK01',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CHECKED_IN,
            'clearance_status' => 'pending',
            'duty_officer_id' => $this->dutyOfficer->id,
            'assigned_checkout_do_id' => $this->dutyOfficer->id,
            'checkout_duty_officer_id' => $this->dutyOfficer->id,
            'checkout_initiated_at' => Carbon::now('Asia/Jakarta')->subMinutes(10),
            'checkout_initiated_by' => $this->frontOfficeUser->id,
            'checked_in_at' => Carbon::now('Asia/Jakarta')->subHours(3),
            'room_opened_at' => Carbon::now('Asia/Jakarta')->subHours(3),
            'room_opened_by' => $this->dutyOfficer->id,
        ]);

        BookingSlot::create([
            'booking_id' => $this->checkoutBooking->id,
            'room_id' => $this->roomStudio->id,
            'duty_officer_id' => $this->dutyOfficer->id,
            'assigned_checkout_do_id' => $this->dutyOfficer->id,
            'checkout_duty_officer_id' => $this->dutyOfficer->id,
            'checkout_initiated_at' => Carbon::now('Asia/Jakarta')->subMinutes(10),
            'event_start_at' => Carbon::parse("{$today} 09:00:00", 'Asia/Jakarta'),
            'event_end_at' => Carbon::parse("{$today} 12:00:00", 'Asia/Jakarta'),
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $this->checkoutBooking->id,
            'event_name' => 'Workshop 3D Blender Malang',
            'event_category' => 'Animasi',
            'ekraf_subsector' => 'Animasi',
            'event_type' => 'Non-Komersial',
            'estimated_participants' => 35,
            'event_description' => 'Workshop 3D Blender untuk pemula.',
            'equipment_needed' => "Drawing Tablet 10 unit",
            'proposal_file_url' => '/storage/proposals/blender.pdf',
            'pic_name_snapshot' => 'Hendra Gunawan',
            'pic_phone_snapshot' => '081234567890',
            'institution_name_snapshot' => 'Blender Malang Guild',
            'tos_agreed_at' => now(),
        ]);

        KtpVaultLog::create([
            'booking_id' => $this->checkoutBooking->id,
            'ktp_holder_name' => 'Hendra Gunawan',
            'nik_number' => '3573010101900001',
            'storage_drawer_number' => 'B-07',
            'received_by_fo' => $this->frontOfficeUser->id,
            'retained_at' => Carbon::now('Asia/Jakarta')->subHours(3),
        ]);
    }

    /**
     * FR-DO-03: DO sees awaiting checkout inspection ticket and banner in dashboard
     */
    public function test_duty_officer_sees_awaiting_checkout_task_in_dashboard(): void
    {
        $response = $this->actingAs($this->dutyOfficer)->get(route('duty_officer.dashboard', [
            'status' => 'awaiting_checkout',
        ]));

        $response->assertStatus(200);
        $response->assertSee('MCC-20261001-CHK01');
        $response->assertSee('Inspeksi Checkout');
        $response->assertSee('Workshop 3D Blender Malang');
        $response->assertSee('Inspeksi Cek Kelayakan Ruangan (Checkout)');
    }

    /**
     * FR-DO-04: DO confirms room clearance (green signal) and system sets clearance_status = 'cleared'
     */
    public function test_duty_officer_can_submit_green_clearance_signal(): void
    {
        $response = $this->actingAs($this->dutyOfficer)->post(
            route('duty_officer.clearance', $this->checkoutBooking),
            [
                'cleanliness_checklist' => [
                    'trash' => '1',
                    'tape' => '1',
                    'posters' => '1',
                ],
                'inventory_checklist' => [
                    '105' => '1',
                ],
                'notes' => 'Ruangan telah dibersihkan oleh panitia, kabel HDMI utuh dan berfungsi baik.',
            ]
        );

        $response->assertRedirect(route('duty_officer.dashboard'));
        $response->assertSessionHas('success');

        // Check database
        $this->checkoutBooking->refresh();
        $this->assertEquals('cleared', $this->checkoutBooking->clearance_status);
        $this->assertTrue($this->checkoutBooking->isCleared());
        $this->assertTrue($this->checkoutBooking->canReleaseKtp());

        // Front Office can now release physical KTP
        $foResponse = $this->actingAs($this->frontOfficeUser)->post(
            route('front_office.checkout.release_ktp'),
            [
                'booking_id' => $this->checkoutBooking->id,
            ]
        );

        $foResponse->assertRedirect(route('front_office.manifest'));
        $this->checkoutBooking->refresh();
        $this->assertEquals(BookingStatus::COMPLETED, $this->checkoutBooking->status);
    }

    /**
     * FR-DO-04: Submit clearance fails if mandatory cleanliness checklist items are unchecked
     */
    public function test_submit_clearance_fails_when_mandatory_cleanliness_is_missing(): void
    {
        $response = $this->actingAs($this->dutyOfficer)->post(
            route('duty_officer.clearance', $this->checkoutBooking),
            [
                'cleanliness_checklist' => [
                    'trash' => '1',
                    // 'tape' and 'posters' missing!
                ],
            ]
        );

        $response->assertSessionHasErrors([
            'cleanliness_checklist.tape',
            'cleanliness_checklist.posters',
        ]);

        $this->checkoutBooking->refresh();
        $this->assertEquals('pending', $this->checkoutBooking->clearance_status);
    }

    /**
     * FR-DO-05: DO reports incident with category, description, and camera photo, setting clearance_status = 'incident_detected' and blocking FO KTP release
     */
    public function test_duty_officer_can_report_room_incident_with_photo_and_transmits_red_signal(): void
    {
        $fakePhoto = UploadedFile::fake()->image('incident_hdmi_broken.jpg', 800, 600);

        $response = $this->actingAs($this->dutyOfficer)->post(
            route('duty_officer.incident', $this->checkoutBooking),
            [
                'category' => 'property_damage',
                'item_name' => 'Kabel HDMI Putus & Konektor Patah',
                'description' => 'Kabel HDMI 4K di meja proyektor ditemukan patah pada ujung konektor gold-plated.',
                'photos' => [$fakePhoto],
            ]
        );

        $response->assertRedirect(route('duty_officer.dashboard'));
        $response->assertSessionHas('success');

        // Check database clearance_status
        $this->checkoutBooking->refresh();
        $this->assertEquals('incident_detected', $this->checkoutBooking->clearance_status);
        $this->assertTrue($this->checkoutBooking->hasIncident());
        $this->assertFalse($this->checkoutBooking->canReleaseKtp());

        // Verify room_incidents table record
        $incident = RoomIncident::where('booking_id', $this->checkoutBooking->id)->first();
        $this->assertNotNull($incident);
        $this->assertEquals('property_damage', $incident->category);
        $this->assertEquals('Kabel HDMI Putus & Konektor Patah', $incident->item_name);
        $this->assertEquals($this->dutyOfficer->id, $incident->reported_by_do);

        // Verify incident photo record
        $this->assertCount(1, $incident->photos);
        Storage::disk('public')->assertExists($incident->photos->first()->photo_url);

        // Verify Front Office KTP release is strictly blocked by red signal
        $foResponse = $this->actingAs($this->frontOfficeUser)->post(
            route('front_office.checkout.release_ktp'),
            [
                'booking_id' => $this->checkoutBooking->id,
            ]
        );

        $foResponse->assertSessionHasErrors('clearance');
        $this->checkoutBooking->refresh();
        $this->assertEquals(BookingStatus::CHECKED_IN, $this->checkoutBooking->status);
    }

    /**
     * FR-DO-06: DO re-inspects room after committee fixes issue, resolves incident, setting clearance_status = 'resolved', unlocking FO KTP release
     */
    public function test_duty_officer_can_resolve_incident_and_unlocks_front_office_ktp_release(): void
    {
        // 1. Setup existing active incident
        $incident = RoomIncident::create([
            'booking_id' => $this->checkoutBooking->id,
            'reported_by_do' => $this->dutyOfficer->id,
            'category' => 'cleanliness',
            'item_name' => 'Sampah Makanan Berserakan di Lantai',
            'description' => 'Sisa nasi kotak dan gelas plastik belum dibuang.',
            'is_resolved' => false,
            'created_at' => Carbon::now()->subMinutes(20),
        ]);

        $this->checkoutBooking->update([
            'clearance_status' => 'incident_detected',
        ]);

        // 2. DO inspects and resolves incident
        $response = $this->actingAs($this->dutyOfficer)->post(
            route('duty_officer.incident.resolve', $this->checkoutBooking),
            [
                'incident_id' => $incident->id,
                'resolution_notes' => 'Panitia telah memungut seluruh sampah dan menyapu bersih area ruangan.',
            ]
        );

        $response->assertRedirect(route('duty_officer.dashboard'));
        $response->assertSessionHas('success');

        // 3. Verify database updates
        $incident->refresh();
        $this->assertTrue($incident->is_resolved);
        $this->assertNotNull($incident->resolved_at);
        $this->assertEquals('Panitia telah memungut seluruh sampah dan menyapu bersih area ruangan.', $incident->resolution_notes);

        $this->checkoutBooking->refresh();
        $this->assertEquals('resolved', $this->checkoutBooking->clearance_status);
        $this->assertTrue($this->checkoutBooking->isIncidentResolved());
        $this->assertTrue($this->checkoutBooking->canReleaseKtp());

        // 4. Verify Front Office can now successfully release physical KTP
        $foResponse = $this->actingAs($this->frontOfficeUser)->post(
            route('front_office.checkout.release_ktp'),
            [
                'booking_id' => $this->checkoutBooking->id,
            ]
        );

        $foResponse->assertRedirect(route('front_office.manifest'));
        $this->checkoutBooking->refresh();
        $this->assertEquals(BookingStatus::COMPLETED, $this->checkoutBooking->status);
    }
}
