<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingApproval;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserBookingRevisionTest extends TestCase
{
    use RefreshDatabase;

    protected User $applicant;
    protected User $otherApplicant;
    protected User $marketing;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->room = Room::create([
            'id' => 1,
            'name' => 'Coworking Space Lantai 2',
            'slug' => 'coworking-space-lantai-2',
            'floor_level' => 2,
            'capacity' => 30,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Ruang kerja bersama komunitas kreatif.',
        ]);

        $this->applicant = User::create([
            'full_name' => 'Faris Pratama',
            'email' => 'faris@komunitasdesain.id',
            'phone_number' => '+6281234560001',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Desain Grafis Malang',
            'address' => 'Klojen, Malang',
        ]);

        $this->otherApplicant = User::create([
            'full_name' => 'Rina Wijaya',
            'email' => 'rina@other.id',
            'phone_number' => '+6281234560002',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Lain',
            'address' => 'Malang',
        ]);

        $this->marketing = User::create([
            'full_name' => 'Tim Marketing MCC',
            'email' => 'marketing@mcc.id',
            'phone_number' => '+6281234560003',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::MARKETING,
            'account_type' => AccountType::INSTANSI,
            'institution_name' => 'Malang Creative Center',
            'address' => 'Malang',
        ]);
    }

    protected function createRevisedBooking(User $owner, ?Carbon $deadline = null): Booking
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'user_id' => $owner->id,
            'status' => BookingStatus::REVISED,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
            'revision_deadline' => $deadline ?? Carbon::now()->addHours(24),
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => Carbon::now()->addDays(5)->setTime(10, 0),
            'event_end_at' => Carbon::now()->addDays(5)->setTime(14, 0),
            'operational_range' => '["2026-09-30 10:00:00", "2026-09-30 14:30:00")',
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Workshop UI/UX Design System',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'DKV',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 25,
            'event_description' => 'Workshop intensif perancangan design system digital.',
            'proposal_file_url' => '/storage/proposals/old_proposal.pdf',
            'pic_name_snapshot' => $owner->full_name,
            'pic_phone_snapshot' => $owner->phone_number,
            'institution_name_snapshot' => $owner->institution_name,
            'tos_agreed_at' => Carbon::now(),
        ]);

        BookingApproval::create([
            'booking_id' => $booking->id,
            'action_by' => $this->marketing->id,
            'decision' => 'revised',
            'notes' => 'Mohon tambahkan susunan kepanitiaan dan rincian rundown per jam di proposal.',
            'created_at' => Carbon::now(),
        ]);

        return $booking;
    }

    public function test_user_dashboard_renders_revised_booking_with_marketing_notes_and_timer(): void
    {
        $booking = $this->createRevisedBooking($this->applicant);

        $response = $this->actingAs($this->applicant)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee($booking->booking_code);
        $response->assertSee('Perlu Revisi');
        $response->assertSee('Mohon tambahkan susunan kepanitiaan dan rincian rundown per jam di proposal.');
        $response->assertSee('Perbaiki Permohonan');
        $response->assertSee('Slot jadwal tetap aman terikat');
    }

    public function test_applicant_can_access_revision_wizard_page(): void
    {
        $booking = $this->createRevisedBooking($this->applicant);

        $response = $this->actingAs($this->applicant)->get(
            route('bookings.revise.show', ['booking' => $booking->id])
        );

        $response->assertStatus(200);
        $response->assertSee($booking->booking_code);
        $response->assertSee('Perbaikan Berkas Permohonan');
        $response->assertSee('Workshop UI/UX Design System');
        $response->assertSee('Mohon tambahkan susunan kepanitiaan dan rincian rundown per jam di proposal.');
        $response->assertSee('Coworking Space Lantai 2');
        $response->assertSee('Lihat Proposal Sebelumnya (PDF)');
    }

    public function test_applicant_cannot_access_revision_wizard_if_not_revised_status(): void
    {
        $booking = $this->createRevisedBooking($this->applicant);
        $booking->update(['status' => BookingStatus::AWAITING_MARKETING]);

        $response = $this->actingAs($this->applicant)->get(
            route('bookings.revise.show', ['booking' => $booking->id])
        );

        $response->assertStatus(403);
    }

    public function test_applicant_can_submit_revision_wizard_with_full_details_and_new_proposal(): void
    {
        $booking = $this->createRevisedBooking($this->applicant);

        $fakePdf = UploadedFile::fake()->create('proposal_perbaikan_final.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->applicant)->post(
            route('bookings.revise.submit', ['booking' => $booking->id]),
            [
                'pic_name' => 'Faris Pratama Updated',
                'pic_phone' => '+6281234567899',
                'pic_emergency_phone' => '+6281234567888',
                'official_letter_number' => '001/KOM-DSG/IX/2026',
                'event_name' => 'Workshop UI/UX Design System 2026',
                'event_category' => 'Komunitas',
                'ekraf_subsector' => 'DKV',
                'event_type' => 'Terbuka Untuk Umum',
                'estimated_participants' => 28,
                'event_description' => 'Workshop intensif design system dengan pemateri profesional.',
                'equipment_needed' => 'Proyektor HDMI, Sound System',
                'proposal_file' => $fakePdf,
                'notes' => 'Telah diperbaiki dengan melampirkan rundown lengkap dan susunan panitia.',
                'tos_agreed' => '1',
            ]
        );

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $booking->refresh();

        // 1. Status berpindah kembali ke awaiting_marketing
        $this->assertEquals(BookingStatus::AWAITING_MARKETING, $booking->status);

        // 2. Proposal file URL terupdate
        $this->assertNotEquals('/storage/proposals/old_proposal.pdf', $booking->eventDetail->proposal_file_url);
        $this->assertEquals('Workshop UI/UX Design System 2026', $booking->eventDetail->event_name);
        $this->assertEquals('Faris Pratama Updated', $booking->eventDetail->pic_name_snapshot);
        $this->assertEquals(28, $booking->eventDetail->estimated_participants);

        // 3. Slot jadwal tetap aktif terikat
        $this->assertTrue($booking->slots()->first()->is_active);

        // 4. Log approval tercatat
        $this->assertDatabaseHas('booking_approvals', [
            'booking_id' => $booking->id,
            'action_by' => $this->applicant->id,
            'decision' => 'revision_submitted',
        ]);
    }

    public function test_applicant_can_submit_revision_wizard_without_replacing_proposal_file(): void
    {
        $booking = $this->createRevisedBooking($this->applicant);

        $response = $this->actingAs($this->applicant)->post(
            route('bookings.revise.submit', ['booking' => $booking->id]),
            [
                'pic_name' => 'Faris Pratama',
                'pic_phone' => '+6281234560001',
                'event_name' => 'Workshop UI/UX Design System Revisi Judul',
                'event_category' => 'Komunitas',
                'ekraf_subsector' => 'DKV',
                'event_type' => 'Terbuka Untuk Umum',
                'estimated_participants' => 25,
                'event_description' => 'Workshop intensif perancangan design system digital.',
                'notes' => 'Hanya mengubah judul kegiatan.',
                'tos_agreed' => '1',
            ]
        );

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $booking->refresh();

        $this->assertEquals(BookingStatus::AWAITING_MARKETING, $booking->status);
        // Proposal file URL tetap yang lama
        $this->assertEquals('/storage/proposals/old_proposal.pdf', $booking->eventDetail->proposal_file_url);
        $this->assertEquals('Workshop UI/UX Design System Revisi Judul', $booking->eventDetail->event_name);
    }

    public function test_applicant_cannot_submit_non_pdf_file(): void
    {
        $booking = $this->createRevisedBooking($this->applicant);

        $fakeDocx = UploadedFile::fake()->create('proposal.docx', 300, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($this->applicant)->post(
            route('bookings.revise.submit', ['booking' => $booking->id]),
            [
                'pic_name' => 'Faris Pratama',
                'pic_phone' => '+6281234560001',
                'event_name' => 'Workshop UI/UX',
                'event_category' => 'Komunitas',
                'ekraf_subsector' => 'DKV',
                'event_type' => 'Terbuka Untuk Umum',
                'estimated_participants' => 25,
                'event_description' => 'Deskripsi event.',
                'proposal_file' => $fakeDocx,
                'tos_agreed' => '1',
            ]
        );

        $response->assertSessionHasErrors(['proposal_file']);
        $booking->refresh();
        $this->assertEquals(BookingStatus::REVISED, $booking->status);
    }

    public function test_applicant_cannot_submit_revision_if_grace_period_expired(): void
    {
        // Tenggat waktu telah terlewat 10 menit yang lalu
        $booking = $this->createRevisedBooking($this->applicant, Carbon::now()->subMinutes(10));

        $fakePdf = UploadedFile::fake()->create('late_proposal.pdf', 300, 'application/pdf');

        $this->expectException(\InvalidArgumentException::class);

        $action = app(\App\Actions\Booking\SubmitBookingRevisionAction::class);
        $action->execute(new \App\DataTransferObjects\Booking\SubmitBookingRevisionData(
            bookingId: $booking->id,
            userId: $this->applicant->id,
            picName: 'Faris Pratama',
            picPhone: '+6281234560001',
            eventName: 'Workshop UI/UX',
            eventCategory: 'Komunitas',
            ekrafSubsector: 'DKV',
            eventType: 'Terbuka Untuk Umum',
            estimatedParticipants: 25,
            eventDescription: 'Deskripsi',
            proposalFile: $fakePdf
        ));
    }

    public function test_other_user_cannot_access_or_submit_revision_for_another_users_booking(): void
    {
        $booking = $this->createRevisedBooking($this->applicant);

        // Akses GET
        $getResponse = $this->actingAs($this->otherApplicant)->get(
            route('bookings.revise.show', ['booking' => $booking->id])
        );
        $getResponse->assertStatus(403);

        // Akses POST
        $fakePdf = UploadedFile::fake()->create('proposal.pdf', 300, 'application/pdf');

        $postResponse = $this->actingAs($this->otherApplicant)->post(
            route('bookings.revise.submit', ['booking' => $booking->id]),
            [
                'pic_name' => 'Hacker',
                'pic_phone' => '081234567890',
                'event_name' => 'Hack',
                'event_category' => 'Komunitas',
                'ekraf_subsector' => 'DKV',
                'event_type' => 'Terbuka Untuk Umum',
                'estimated_participants' => 10,
                'event_description' => 'Hacked',
                'proposal_file' => $fakePdf,
                'tos_agreed' => '1',
            ]
        );

        $postResponse->assertStatus(403);
    }

    public function test_cancel_expired_revisions_artisan_command(): void
    {
        // 1. Booking yang kedaluwarsa (> 24 jam)
        $expiredBooking = $this->createRevisedBooking($this->applicant, Carbon::now()->subHour());

        // 2. Booking yang masih dalam masa tenggang aktif
        $activeBooking = $this->createRevisedBooking($this->otherApplicant, Carbon::now()->addHours(12));

        $this->assertTrue($expiredBooking->slots()->first()->is_active);
        $this->assertTrue($activeBooking->slots()->first()->is_active);

        // Jalankan artisan command
        Artisan::call('app:cancel-expired-revisions');

        $expiredBooking->refresh();
        $activeBooking->refresh();

        // 1. Booking kedaluwarsa otomatis berubah menjadi cancelled & slot jadwal dibuka (is_active = false)
        $this->assertEquals(BookingStatus::CANCELLED, $expiredBooking->status);
        $this->assertFalse($expiredBooking->slots()->first()->is_active);
        $this->assertDatabaseHas('booking_approvals', [
            'booking_id' => $expiredBooking->id,
            'decision' => 'cancelled',
        ]);

        // 2. Booking aktif tetap berstatus revised & slot jadwal tetap aman
        $this->assertEquals(BookingStatus::REVISED, $activeBooking->status);
        $this->assertTrue($activeBooking->slots()->first()->is_active);
    }
}
