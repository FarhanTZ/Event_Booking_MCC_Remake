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

class FrontOfficeManifestTest extends TestCase
{
    use RefreshDatabase;

    private User $frontOfficeUser;
    private User $publicUser;
    private Room $roomFloor2;
    private Room $roomFloor5;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roomFloor2 = Room::create([
            'id' => 1,
            'name' => 'Studio Podcast Lantai 2',
            'slug' => 'studio-podcast-lantai-2',
            'floor_level' => 2,
            'capacity' => 10,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Studio podcast kedap suara.',
        ]);

        $this->roomFloor5 = Room::create([
            'id' => 2,
            'name' => 'Auditorium Lantai 5',
            'slug' => 'auditorium-lantai-5',
            'floor_level' => 5,
            'capacity' => 200,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Auditorium pertunjukan teater dan seminar besar.',
        ]);

        $this->frontOfficeUser = User::create([
            'full_name' => 'Petugas Front Office',
            'email' => 'fo@mcc.id',
            'phone_number' => '+6289999999999',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::FRONT_OFFICE,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Front Office MCC',
            'address' => 'Jl. A. Yani No. 53, Malang',
        ]);

        $this->publicUser = User::create([
            'full_name' => 'Budi Santoso',
            'email' => 'budi@komunitas.id',
            'phone_number' => '+6281234567890',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Sinema Malang',
            'address' => 'Kota Malang',
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_manifest(): void
    {
        $response = $this->get(route('front_office.manifest'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_is_forbidden_from_accessing_manifest(): void
    {
        $response = $this->actingAs($this->publicUser)->get(route('front_office.manifest'));
        $response->assertForbidden();
    }

    public function test_front_office_user_can_access_manifest(): void
    {
        $response = $this->actingAs($this->frontOfficeUser)->get(route('front_office.manifest'));
        $response->assertOk();
        $response->assertViewIs('front_office.manifest.index');
        $response->assertSee('Manifes Acara Harian');
        $response->assertSee('FR-FO-01');
    }

    public function test_manifest_displays_only_confirmed_checked_in_and_completed_sessions_for_today(): void
    {
        $today = Carbon::today();

        // 1. Booking Confirmed Hari Ini (Harus Muncul)
        $bookingConfirmed = Booking::create([
            'booking_code' => 'MCC-CONF-01',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CONFIRMED,
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0,
        ]);
        EventDetail::create([
            'booking_id' => $bookingConfirmed->id,
            'event_name' => 'Workshop Film Indie Hari Ini',
            'event_category' => 'Komunitas',
            'estimated_participants' => 30,
            'event_description' => 'Workshop pembuatan film dokumenter.',
            'pic_name_snapshot' => 'Budi Santoso',
            'pic_phone_snapshot' => '+6281234567890',
            'institution_name_snapshot' => 'Komunitas Sinema Malang',
            'proposal_file_url' => '/storage/proposals/test1.pdf',
            'tos_agreed_at' => now(),
        ]);
        BookingSlot::create([
            'booking_id' => $bookingConfirmed->id,
            'room_id' => $this->roomFloor2->id,
            'event_start_at' => $today->copy()->setTime(9, 0),
            'event_end_at' => $today->copy()->setTime(12, 0),
            'operational_range' => "[{$today->copy()->setTime(9, 0)->toIso8601String()}, {$today->copy()->setTime(12, 30)->toIso8601String()})",
            'is_active' => true,
        ]);

        // 2. Booking Checked-In Hari Ini (Harus Muncul)
        $bookingCheckedIn = Booking::create([
            'booking_code' => 'MCC-CHECK-02',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CHECKED_IN,
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0,
        ]);
        EventDetail::create([
            'booking_id' => $bookingCheckedIn->id,
            'event_name' => 'Seminar Desain Grafis Hari Ini',
            'event_category' => 'Kampus',
            'estimated_participants' => 80,
            'event_description' => 'Seminar tren UI/UX 2026.',
            'pic_name_snapshot' => 'Siti Rahma',
            'pic_phone_snapshot' => '+6281234567899',
            'institution_name_snapshot' => 'Universitas Negeri Malang',
            'proposal_file_url' => '/storage/proposals/test2.pdf',
            'tos_agreed_at' => now(),
        ]);
        BookingSlot::create([
            'booking_id' => $bookingCheckedIn->id,
            'room_id' => $this->roomFloor5->id,
            'event_start_at' => $today->copy()->setTime(13, 0),
            'event_end_at' => $today->copy()->setTime(16, 0),
            'operational_range' => "[{$today->copy()->setTime(13, 0)->toIso8601String()}, {$today->copy()->setTime(16, 30)->toIso8601String()})",
            'is_active' => true,
        ]);

        // 3. Booking Awaiting Marketing Hari Ini (TIDAK BOLEH MUNCUL di Manifes Front Office)
        $bookingAwaiting = Booking::create([
            'booking_code' => 'MCC-AWAIT-03',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::AWAITING_MARKETING,
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0,
        ]);
        EventDetail::create([
            'booking_id' => $bookingAwaiting->id,
            'event_name' => 'Acara Menunggu Kurasi',
            'event_category' => 'Komunitas',
            'estimated_participants' => 20,
            'event_description' => 'Belum dikurasi marketing.',
            'pic_name_snapshot' => 'Joko',
            'pic_phone_snapshot' => '+6281234567888',
            'institution_name_snapshot' => 'Komunitas Game',
            'proposal_file_url' => '/storage/proposals/test3.pdf',
            'tos_agreed_at' => now(),
        ]);
        BookingSlot::create([
            'booking_id' => $bookingAwaiting->id,
            'room_id' => $this->roomFloor2->id,
            'event_start_at' => $today->copy()->setTime(14, 0),
            'event_end_at' => $today->copy()->setTime(17, 0),
            'operational_range' => "[{$today->copy()->setTime(14, 0)->toIso8601String()}, {$today->copy()->setTime(17, 30)->toIso8601String()})",
            'is_active' => true,
        ]);

        // 4. Booking Confirmed BESOK (TIDAK BOLEH MUNCUL pada Manifes Hari Ini)
        $tomorrow = $today->copy()->addDay();
        $bookingTomorrow = Booking::create([
            'booking_code' => 'MCC-TOMORROW-04',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CONFIRMED,
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0,
        ]);
        EventDetail::create([
            'booking_id' => $bookingTomorrow->id,
            'event_name' => 'Acara Besok Hari',
            'event_category' => 'Dinas',
            'estimated_participants' => 50,
            'event_description' => 'Acara besok.',
            'pic_name_snapshot' => 'Agus',
            'pic_phone_snapshot' => '+6281234567777',
            'institution_name_snapshot' => 'Dinas Koperasi',
            'proposal_file_url' => '/storage/proposals/test4.pdf',
            'tos_agreed_at' => now(),
        ]);
        BookingSlot::create([
            'booking_id' => $bookingTomorrow->id,
            'room_id' => $this->roomFloor2->id,
            'event_start_at' => $tomorrow->copy()->setTime(9, 0),
            'event_end_at' => $tomorrow->copy()->setTime(12, 0),
            'operational_range' => "[{$tomorrow->copy()->setTime(9, 0)->toIso8601String()}, {$tomorrow->copy()->setTime(12, 30)->toIso8601String()})",
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->frontOfficeUser)->get(route('front_office.manifest'));

        $response->assertOk();
        $response->assertSee('MCC-CONF-01');
        $response->assertSee('Workshop Film Indie Hari Ini');
        $response->assertSee('MCC-CHECK-02');
        $response->assertSee('Seminar Desain Grafis Hari Ini');

        // Pastikan booking awaiting dan booking besok tidak ada
        $response->assertDontSee('MCC-AWAIT-03');
        $response->assertDontSee('Acara Menunggu Kurasi');
        $response->assertDontSee('MCC-TOMORROW-04');
        $response->assertDontSee('Acara Besok Hari');
    }

    public function test_multi_day_booking_only_shows_slots_occurring_today(): void
    {
        $today = Carbon::today();
        $tomorrow = $today->copy()->addDay();

        // Multi-day booking (Hari ini & Besok)
        $multiDayBooking = Booking::create([
            'booking_code' => 'MCC-MULTIDAY-99',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CONFIRMED,
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0,
        ]);
        EventDetail::create([
            'booking_id' => $multiDayBooking->id,
            'event_name' => 'Festival Animasi Multi-Day MCC',
            'event_category' => 'Komunitas',
            'estimated_participants' => 150,
            'event_description' => 'Festival animasi 2 hari berturut-turut.',
            'pic_name_snapshot' => 'Dimas Prakoso',
            'pic_phone_snapshot' => '+628555555555',
            'institution_name_snapshot' => 'Komunitas Animasi Jatim',
            'proposal_file_url' => '/storage/proposals/test_multi.pdf',
            'tos_agreed_at' => now(),
        ]);

        // Slot Hari 1 (Hari ini di Auditorium Lt. 5)
        BookingSlot::create([
            'booking_id' => $multiDayBooking->id,
            'room_id' => $this->roomFloor5->id,
            'event_start_at' => $today->copy()->setTime(10, 0),
            'event_end_at' => $today->copy()->setTime(15, 0),
            'operational_range' => "[{$today->copy()->setTime(10, 0)->toIso8601String()}, {$today->copy()->setTime(15, 30)->toIso8601String()})",
            'is_active' => true,
        ]);

        // Slot Hari 2 (Besok di Studio Podcast Lt. 2)
        BookingSlot::create([
            'booking_id' => $multiDayBooking->id,
            'room_id' => $this->roomFloor2->id,
            'event_start_at' => $tomorrow->copy()->setTime(10, 0),
            'event_end_at' => $tomorrow->copy()->setTime(15, 0),
            'operational_range' => "[{$tomorrow->copy()->setTime(10, 0)->toIso8601String()}, {$tomorrow->copy()->setTime(15, 30)->toIso8601String()})",
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->frontOfficeUser)->get(route('front_office.manifest'));

        $response->assertOk();
        $response->assertSee('MCC-MULTIDAY-99');
        $response->assertSee('Festival Animasi Multi-Day MCC');
        $response->assertSee('Auditorium Lantai 5');
        $response->assertSee('Multi-Slot');

        // Khusus manifes hari ini, slot besok tidak boleh dihitung sebagai baris tersendiri
        $viewSlots = $response->viewData('manifestSlots');
        $this->assertEquals(1, $viewSlots->total());
        $this->assertEquals($this->roomFloor5->id, $viewSlots->first()->room_id);
    }

    public function test_instant_search_filters_manifest_properly(): void
    {
        $today = Carbon::today();

        $booking = Booking::create([
            'booking_code' => 'MCC-XYZ-777',
            'user_id' => $this->publicUser->id,
            'status' => BookingStatus::CONFIRMED,
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0,
        ]);
        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Pameran Lukisan Klasik',
            'event_category' => 'Komunitas',
            'estimated_participants' => 45,
            'event_description' => 'Pameran karya seniman lokal.',
            'pic_name_snapshot' => 'Hendro Kartiko',
            'pic_phone_snapshot' => '+6287777777777',
            'institution_name_snapshot' => 'Sanggar Seni Arema',
            'proposal_file_url' => '/storage/proposals/test_search.pdf',
            'tos_agreed_at' => now(),
        ]);
        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->roomFloor2->id,
            'event_start_at' => $today->copy()->setTime(8, 0),
            'event_end_at' => $today->copy()->setTime(11, 0),
            'operational_range' => "[{$today->copy()->setTime(8, 0)->toIso8601String()}, {$today->copy()->setTime(11, 30)->toIso8601String()})",
            'is_active' => true,
        ]);

        // Search by Booking Code
        $resCode = $this->actingAs($this->frontOfficeUser)->get(route('front_office.manifest', ['q' => 'MCC-XYZ-777']));
        $resCode->assertOk();
        $resCode->assertSee('Pameran Lukisan Klasik');

        // Search by PIC Name
        $resPic = $this->actingAs($this->frontOfficeUser)->get(route('front_office.manifest', ['q' => 'Hendro Kartiko']));
        $resPic->assertOk();
        $resPic->assertSee('MCC-XYZ-777');

        // Search by Event Name
        $resEvent = $this->actingAs($this->frontOfficeUser)->get(route('front_office.manifest', ['q' => 'Lukisan Klasik']));
        $resEvent->assertOk();
        $resEvent->assertSee('Sanggar Seni Arema');

        // Search unmatched query
        $resUnmatched = $this->actingAs($this->frontOfficeUser)->get(route('front_office.manifest', ['q' => 'UnmatchedQuery999']));
        $resUnmatched->assertOk();
        $resUnmatched->assertDontSee('MCC-XYZ-777');
        $resUnmatched->assertSee('Tidak ada sesi acara pada manifes hari ini');
    }
}
