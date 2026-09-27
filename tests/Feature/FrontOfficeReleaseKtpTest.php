<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\IncidentPhoto;
use App\Models\KtpVaultLog;
use App\Models\Room;
use App\Models\RoomIncident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FrontOfficeReleaseKtpTest extends TestCase
{
    use RefreshDatabase;

    private User $frontOfficeUser;
    private User $publicUser;
    private User $dutyOfficer;
    private Room $room;
    private Booking $booking;
    private BookingSlot $slot;
    private KtpVaultLog $vaultLog;

    protected function setUp(): void
    {
        parent::setUp();

        $this->room = Room::create([
            'id' => 1,
            'name' => 'Studio Animasi Lantai 4',
            'slug' => 'studio-animasi-lantai-4',
            'floor_level' => 4,
            'capacity' => 30,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Studio animasi lantai 4 MCC.',
        ]);

        $this->frontOfficeUser = User::create([
            'full_name' => 'FO Staff Resepsionis',
            'email' => 'fo_release@mcc.id',
            'phone_number' => '+6281111111111',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::FRONT_OFFICE,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Front Office MCC',
            'address' => 'Malang',
        ]);

        $this->dutyOfficer = User::create([
            'full_name' => 'Bambang Sudiro',
            'email' => 'bambang.do@mcc.id',
            'phone_number' => '081234567899',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::DUTY_OFFICER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'DO MCC',
            'address' => 'Malang',
        ]);

        $this->publicUser = User::create([
            'full_name' => 'Budi Utomo',
            'email' => 'budi.applicant@gmail.com',
            'phone_number' => '081234567890',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Animasi',
            'address' => 'Kota Malang',
        ]);

        $this->booking = Booking::create([
            'booking_code' => 'MCC-2026-RELEASE-KTP',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CHECKED_IN,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
            'duty_officer_id' => $this->dutyOfficer->id,
            'assigned_checkout_do_id' => $this->dutyOfficer->id,
            'checked_in_at' => Carbon::today()->setTime(9, 0),
            'checked_in_by' => $this->frontOfficeUser->id,
        ]);

        EventDetail::create([
            'booking_id' => $this->booking->id,
            'event_name' => 'Workshop 3D Blender',
            'event_category' => 'Workshop',
            'estimated_participants' => 25,
            'event_description' => 'Pelatihan 3D Blender intensif.',
            'pic_name_snapshot' => 'Budi Utomo',
            'pic_phone_snapshot' => '081234567890',
            'institution_name_snapshot' => 'Komunitas Animasi',
            'proposal_file_url' => '/storage/proposals/test.pdf',
            'tos_agreed_at' => now(),
        ]);

        $this->slot = BookingSlot::create([
            'booking_id' => $this->booking->id,
            'room_id' => $this->room->id,
            'duty_officer_id' => $this->dutyOfficer->id,
            'assigned_checkout_do_id' => $this->dutyOfficer->id,
            'event_start_at' => Carbon::today()->setTime(9, 0),
            'event_end_at' => Carbon::today()->setTime(12, 0),
            'operational_range' => "[".Carbon::today()->setTime(9, 0)->toIso8601String().", ".Carbon::today()->setTime(12, 30)->toIso8601String().")",
            'is_active' => true,
        ]);

        $this->vaultLog = KtpVaultLog::create([
            'booking_id' => $this->booking->id,
            'ktp_holder_name' => 'Budi Utomo',
            'pic_whatsapp_number' => '081234567890',
            'storage_drawer_number' => 'A-12',
            'received_by_fo' => $this->frontOfficeUser->id,
            'retained_at' => Carbon::today()->setTime(9, 0),
        ]);
    }

    public function test_release_ktp_succeeds_when_clearance_is_cleared(): void
    {
        // Set clearance_status = 'cleared' (Hijau / Aman)
        $this->booking->update(['clearance_status' => 'cleared']);

        $payload = [
            'booking_id' => $this->booking->id,
            'slot_id' => $this->slot->id,
            'notes' => 'KTP diserahkan dalam kondisi baik',
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.release_ktp'), $payload);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'ktp_holder_name' => 'Budi Utomo',
            'storage_drawer_number' => 'A-12',
        ]);

        $this->booking->refresh();
        $this->vaultLog->refresh();

        $this->assertEquals(BookingStatus::COMPLETED, $this->booking->status);
        $this->assertNotNull($this->booking->completed_at);
        $this->assertEquals($this->frontOfficeUser->id, $this->booking->completed_by);

        $this->assertEquals($this->frontOfficeUser->id, $this->vaultLog->returned_by_fo);
        $this->assertNotNull($this->vaultLog->returned_at);
        $this->assertStringContainsString('KTP diserahkan dalam kondisi baik', $this->vaultLog->notes);
    }

    public function test_release_ktp_succeeds_when_incident_is_resolved(): void
    {
        // Sinyal Biru / resolved setelah sanksi beres
        $this->booking->update(['clearance_status' => 'resolved']);

        $incident = RoomIncident::create([
            'booking_id' => $this->booking->id,
            'reported_by_do' => $this->dutyOfficer->id,
            'category' => 'Kebersihan',
            'description' => 'Sampah botol berserakan di ruangan.',
            'is_resolved' => true,
            'resolution_notes' => 'Panitia telah membersihkan seluruh ruangan.',
            'resolved_at' => now(),
        ]);

        $payload = [
            'booking_id' => $this->booking->id,
            'slot_id' => $this->slot->id,
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.release_ktp'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $this->booking->refresh();
        $this->assertEquals(BookingStatus::COMPLETED, $this->booking->status);
    }

    public function test_release_ktp_is_blocked_when_incident_detected(): void
    {
        // Sinyal Merah / incident_detected
        $this->booking->update(['clearance_status' => 'incident_detected']);

        $incident = RoomIncident::create([
            'booking_id' => $this->booking->id,
            'reported_by_do' => $this->dutyOfficer->id,
            'category' => 'Kerusakan Fasilitas',
            'item_name' => 'Mic Wireless',
            'description' => 'Mic wireless retak dan tombol switch patah.',
            'is_resolved' => false,
        ]);

        IncidentPhoto::create([
            'incident_id' => $incident->id,
            'photo_url' => '/storage/incidents/mic_broken.jpg',
            'caption' => 'Foto mic rusak',
        ]);

        $payload = [
            'booking_id' => $this->booking->id,
            'slot_id' => $this->slot->id,
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.release_ktp'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['clearance']);

        $this->booking->refresh();
        $this->assertEquals(BookingStatus::CHECKED_IN, $this->booking->status);
        $this->assertNull($this->booking->completed_at);
    }

    public function test_release_ktp_is_blocked_when_clearance_is_still_pending(): void
    {
        // Sinyal Masih Pending (Belum diinspeksi DO)
        $this->booking->update(['clearance_status' => 'pending']);

        $payload = [
            'booking_id' => $this->booking->id,
            'slot_id' => $this->slot->id,
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.release_ktp'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['clearance']);
    }

    public function test_release_ktp_fails_if_booking_is_not_checked_in(): void
    {
        $this->booking->update([
            'status' => BookingStatus::CONFIRMED,
            'clearance_status' => 'cleared',
        ]);

        $payload = [
            'booking_id' => $this->booking->id,
        ];

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.checkout.release_ktp'), $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['clearance']);
    }

    public function test_regular_user_cannot_release_ktp(): void
    {
        $this->booking->update(['clearance_status' => 'cleared']);

        $payload = [
            'booking_id' => $this->booking->id,
        ];

        $response = $this->actingAs($this->publicUser)
            ->postJson(route('front_office.checkout.release_ktp'), $payload);

        $response->assertForbidden();
    }
}
