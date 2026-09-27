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

class FrontOfficeUnclaimedKtpTest extends TestCase
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
            'email' => 'fo_unclaimed@mcc.id',
            'phone_number' => '081234567890',
            'password_hash' => bcrypt('Password123!'),
        ]);

        $this->applicantUser = User::create([
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'full_name' => 'Pemilik KTP Tertinggal',
            'institution_name' => 'Komunitas Film Indie',
            'address' => 'Jl. Ijen No. 12 Malang',
            'email' => 'tertinggal@mcc.id',
            'phone_number' => '085566778899',
            'password_hash' => bcrypt('Password123!'),
        ]);

        $this->room = Room::create([
            'name' => 'Bioskop Mini Lt 5',
            'slug' => 'bioskop-mini-lt-5',
            'floor_level' => 5,
            'capacity' => 60,
            'is_active' => true,
            'description' => 'Bioskop mini lantai 5 MCC',
        ]);
    }

    private function createUnclaimedLog(bool $inBox = true, bool $withIncident = false): KtpVaultLog
    {
        $status = $withIncident ? BookingStatus::ABANDONED_INCIDENT : BookingStatus::ABANDONED_CLEAN;
        if ($withIncident) {
            $this->applicantUser->update([
                'is_suspended' => true,
                'suspension_reason' => 'Meninggalkan gedung tanpa lapor dengan insiden.',
                'suspended_at' => Carbon::now()->subDays(1),
            ]);
        }

        $booking = Booking::create([
            'booking_code' => 'MCC-' . strtoupper(bin2hex(random_bytes(4))),
            'user_id' => $this->applicantUser->id,
            'status' => $status,
            'clearance_status' => $withIncident ? 'incident_detected' : 'cleared',
            'pricing_scheme' => 'subsidy',
            'completed_at' => Carbon::now()->subDays(1),
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Pemutaran Film Pendek',
            'event_description' => 'Pemutaran dan apresiasi film pendek indie Malang.',
            'ekraf_subsector' => 'Film, Animasi dan Video',
            'pic_name_snapshot' => 'Pemilik KTP Tertinggal',
            'pic_phone_snapshot' => '085566778899',
            'institution_name_snapshot' => 'Komunitas Film Indie',
            'estimated_participants' => 40,
            'event_category' => 'Screening',
            'proposal_file_url' => '/storage/proposals/test.pdf',
            'tos_agreed_at' => now(),
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => Carbon::yesterday()->setTime(13, 0),
            'event_end_at' => Carbon::yesterday()->setTime(17, 0),
            'operational_range' => "[".Carbon::yesterday()->setTime(13, 0)->toIso8601String().", ".Carbon::yesterday()->setTime(17, 30)->toIso8601String().")",
            'is_active' => true,
        ]);

        if ($withIncident) {
            RoomIncident::create([
                'booking_id' => $booking->id,
                'reported_by_do' => $this->frontOfficeUser->id,
                'category' => 'cleanliness',
                'item_name' => 'Sampah Popcorn & Minuman Berceceran',
                'description' => 'Karpet bioskop kotor berantakan.',
                'is_resolved' => false,
            ]);
        }

        return KtpVaultLog::create([
            'booking_id' => $booking->id,
            'ktp_holder_name' => 'Pemilik KTP Tertinggal',
            'pic_whatsapp_number' => '085566778899',
            'storage_drawer_number' => 'LACI-08',
            'unclaimed_box_id' => $inBox ? 'BOX-ARSIP-02' : null,
            'nik_number' => '3573029988776655',
            'received_by_fo' => $this->frontOfficeUser->id,
            'retained_at' => Carbon::yesterday()->setTime(12, 50),
        ]);
    }

    public function test_fo_can_access_unclaimed_ktp_log_page(): void
    {
        $this->createUnclaimedLog(inBox: true, withIncident: false);
        $this->createUnclaimedLog(inBox: false, withIncident: true);

        $response = $this->actingAs($this->frontOfficeUser)
            ->get(route('front_office.unclaimed_ktp'));

        $response->assertOk();
        $response->assertViewIs('front_office.unclaimed_ktp.index');
        $response->assertViewHas('metrics');
        $response->assertViewHas('unclaimedLogs');
        $response->assertSee('Buku Inventaris KTP Tertinggal');
        $response->assertSee('Pemilik KTP Tertinggal');
    }

    public function test_fo_can_perform_late_retrieval_handover_without_unsuspend(): void
    {
        $log = $this->createUnclaimedLog(inBox: true, withIncident: false);

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.unclaimed_ktp.handover'), [
                'ktp_vault_log_id' => $log->id,
                'retrieved_by_name' => 'Budi Utomo (Kuasa Pengambil)',
                'retrieved_by_nik' => '3573011122334455',
                'notes' => 'Pengambilan dilakukan dengan surat kuasa dan KTP asli pemohon.',
                'unsuspend_user' => false,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'was_unsuspended' => false,
        ]);

        $log->refresh();
        $this->assertNotNull($log->returned_at);
        $this->assertEquals($this->frontOfficeUser->id, $log->returned_by_fo);
        $this->assertStringContainsString('Budi Utomo', $log->notes);
    }

    public function test_fo_can_perform_late_retrieval_handover_with_user_unsuspension(): void
    {
        $log = $this->createUnclaimedLog(inBox: true, withIncident: true);
        $this->assertTrue($this->applicantUser->fresh()->isSuspended());

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.unclaimed_ktp.handover'), [
                'ktp_vault_log_id' => $log->id,
                'retrieved_by_name' => 'Pemilik KTP Tertinggal (Langsung)',
                'retrieved_by_nik' => '3573029988776655',
                'notes' => 'Panitia telah menyelesaikan denda kebersihan dan mengambil KTP.',
                'unsuspend_user' => true,
            ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'was_unsuspended' => true,
        ]);

        $log->refresh();
        $this->assertNotNull($log->returned_at);

        // User is unsuspended!
        $this->applicantUser->refresh();
        $this->assertFalse($this->applicantUser->isSuspended());
        $this->assertNull($this->applicantUser->suspension_reason);
        $this->assertNull($this->applicantUser->suspended_at);
    }

    public function test_handover_fails_if_ktp_already_returned(): void
    {
        $log = $this->createUnclaimedLog();
        $log->update([
            'returned_by_fo' => $this->frontOfficeUser->id,
            'returned_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->frontOfficeUser)
            ->postJson(route('front_office.unclaimed_ktp.handover'), [
                'ktp_vault_log_id' => $log->id,
                'retrieved_by_name' => 'Budi',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['returned_at']);
    }

    public function test_regular_user_cannot_access_unclaimed_ktp_inventory(): void
    {
        $response = $this->actingAs($this->applicantUser)
            ->get(route('front_office.unclaimed_ktp'));

        $response->assertForbidden();
    }
}
