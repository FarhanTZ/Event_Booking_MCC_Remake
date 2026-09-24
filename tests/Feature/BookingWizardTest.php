<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookingWizardTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->user = User::factory()->create([
            'account_type' => AccountType::KOMUNITAS,
            'full_name' => 'Farhan Test PIC',
            'phone_number' => '08123456789',
            'institution_name' => 'Komunitas Kreatif Malang',
        ]);

        $this->room = Room::query()->create([
            'id' => 1,
            'name' => 'Stage Outdoor',
            'slug' => 'stage-outdoor-1',
            'floor_level' => 1,
            'capacity' => 100,
            'description' => 'Area panggung terbuka outdoor.',
            'is_active' => true,
            'buffer_minutes' => 30,
            'base_hourly_rate' => 0.00,
        ]);

        RoomFacility::query()->create([
            'room_id' => $this->room->id,
            'facility_name' => 'Hanya Area',
            'quantity' => 1,
            'unit' => 'area',
            'condition' => 'good',
        ]);
    }

    public function test_authenticated_user_can_view_booking_wizard_page(): void
    {
        $response = $this->actingAs($this->user)->get('/booking/wizard?room_id=' . $this->room->id . '&date=2026-09-26&slot=08:00 - 09:00');

        $response->assertStatus(200);
        $response->assertSee('Stage Outdoor');
        $response->assertSee('Lantai 1');
        $response->assertSee('Seksi 1: Ringkasan Ruangan & Jadwal', false);
        $response->assertSee('Seksi 2: Data Penyelenggara & PIC', false);
        $response->assertSee('Seksi 3: Rincian Kegiatan (Data Event)', false);
        $response->assertSee('Seksi 4: Berkas Pendukung & Pakta Integritas Legalitas', false);
        $response->assertSee('Saya menyetujui syarat dan ketentuan pemakaian gedung MCC serta bersedia menyerahkan fisik KTP asli penanggung jawab acara di meja Front Office saat registrasi hari-H.', false);
    }

    public function test_user_can_submit_valid_booking_proposal_with_all_sections(): void
    {
        // 500 KB proposal PDF (valid up to 2 MB)
        $proposalFile = UploadedFile::fake()->create('proposal_kegiatan.pdf', 500, 'application/pdf');
        $posterFile = UploadedFile::fake()->image('poster_acara.jpg', 800, 600)->size(500);

        $response = $this->actingAs($this->user)->post('/booking/wizard', [
            'room_id' => $this->room->id,
            'date' => now()->addDays(3)->toDateString(),
            'slots' => ['08:00 - 09:00', '09:00 - 10:00'],
            'pic_name' => 'Farhan Test PIC',
            'pic_phone' => '+628123456789',
            'pic_emergency_phone' => '+628129998887',
            'event_name' => 'Workshop Musik & Audio Production',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Musik',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 50,
            'event_description' => 'Pelatihan teknik mixing dan recording dasar.',
            'equipment_needed' => '2 mic wireless, 1 mixer audio',
            'proposal_file' => $proposalFile,
            'poster_file' => $posterFile,
            'tos_agreed' => '1',
        ]);

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('success');

        // Verify Booking in database
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'status' => 'awaiting_marketing',
            'pricing_scheme' => 'subsidy',
        ]);

        $booking = Booking::query()->first();
        $this->assertNotNull($booking);

        // Verify Event Details in database
        $this->assertDatabaseHas('event_details', [
            'booking_id' => $booking->id,
            'event_name' => 'Workshop Musik & Audio Production',
            'ekraf_subsector' => 'Musik',
            'estimated_participants' => 50,
            'pic_emergency_phone' => '+628129998887',
        ]);
    }

    public function test_booking_submission_fails_if_proposal_is_invalid_file_or_over_2mb(): void
    {
        // 3 MB file (over 2 MB limit)
        $oversizedFile = UploadedFile::fake()->create('proposal_besar.pdf', 3072, 'application/pdf');

        $response = $this->actingAs($this->user)->post('/booking/wizard', [
            'room_id' => $this->room->id,
            'date' => now()->addDays(3)->toDateString(),
            'slots' => ['08:00 - 09:00'],
            'pic_name' => 'Farhan Test PIC',
            'pic_phone' => '+628123456789',
            'event_name' => 'Workshop Animasi',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Film',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 20,
            'event_description' => 'Pelatihan animasi 2D',
            'proposal_file' => $oversizedFile,
            'tos_agreed' => '1',
        ]);

        $response->assertSessionHasErrors('proposal_file');
    }

    public function test_booking_submission_fails_if_participants_exceed_room_capacity(): void
    {
        $proposalFile = UploadedFile::fake()->create('proposal_kegiatan.pdf', 300, 'application/pdf');

        // Room capacity is 100, submitting 150 should fail
        $response = $this->actingAs($this->user)->post('/booking/wizard', [
            'room_id' => $this->room->id,
            'date' => now()->addDays(3)->toDateString(),
            'slots' => ['08:00 - 09:00'],
            'pic_name' => 'Farhan Test PIC',
            'pic_phone' => '+628123456789',
            'event_name' => 'Konser Terbuka',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Musik',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 150, // Exceeds capacity of 100
            'event_description' => 'Konser musik komunitas',
            'proposal_file' => $proposalFile,
            'tos_agreed' => '1',
        ]);

        $response->assertSessionHasErrors('estimated_participants');
    }
}
