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

class MarketingAuthAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic rooms across floors for relation & filter testing
        Room::create([
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

        Room::create([
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
    }

    public function test_guest_is_redirected_to_login_when_accessing_marketing_dashboard(): void
    {
        $response = $this->get(route('marketing.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_is_forbidden_from_accessing_marketing_dashboard(): void
    {
        $user = User::create([
            'full_name' => 'Public User',
            'email' => 'user@mcc.id',
            'phone_number' => '+6281111111111',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Kreatif',
            'address' => 'Malang',
        ]);

        $response = $this->actingAs($user)->get(route('marketing.dashboard'));
        $response->assertStatus(403);
    }

    public function test_marketing_user_is_redirected_to_marketing_dashboard_on_login(): void
    {
        $marketing = User::create([
            'full_name' => 'Tim Marketing MCC',
            'email' => 'marketing@mcc.id',
            'phone_number' => '+6281234567890',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::MARKETING,
            'account_type' => AccountType::INSTANSI,
            'institution_name' => 'Malang Creative Center',
            'address' => 'Malang',
        ]);

        $response = $this->post('/login', [
            'email' => 'marketing@mcc.id',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('marketing.dashboard'));
        $this->assertAuthenticatedAs($marketing);
    }

    public function test_marketing_user_can_view_dashboard_with_kpi_counters(): void
    {
        $marketing = User::create([
            'full_name' => 'Tim Marketing MCC',
            'email' => 'marketing@mcc.id',
            'phone_number' => '+6281234567890',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::MARKETING,
            'account_type' => AccountType::INSTANSI,
            'institution_name' => 'Malang Creative Center',
            'address' => 'Malang',
        ]);

        $publicUser = User::create([
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone_number' => '+6281298765432',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Animasi Malang',
            'address' => 'Klojen, Malang',
        ]);

        // 1. Awaiting Marketing
        $booking1 = Booking::create([
            'booking_code' => 'MCC-20260924-AWAITING',
            'user_id' => $publicUser->id,
            'status' => BookingStatus::AWAITING_MARKETING,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
        ]);

        BookingSlot::create([
            'booking_id' => $booking1->id,
            'room_id' => 1,
            'event_start_at' => Carbon::now()->addDays(2)->setTime(9, 0),
            'event_end_at' => Carbon::now()->addDays(2)->setTime(12, 0),
            'operational_range' => '["2026-09-26 09:00:00", "2026-09-26 12:30:00")',
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $booking1->id,
            'event_name' => 'Workshop Animasi 3D Digital',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Animasi & Konten Digital',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 8,
            'event_description' => 'Pelatihan teknik rigging dan modeling animasi untuk pemuda kreatif.',
            'proposal_file_url' => 'proposals/sample_proposal.pdf',
            'pic_name_snapshot' => 'Budi Santoso',
            'pic_phone_snapshot' => '+6281298765432',
            'institution_name_snapshot' => 'Komunitas Animasi Malang',
            'tos_agreed_at' => Carbon::now(),
        ]);

        // 2. Awaiting Payment
        $booking2 = Booking::create([
            'booking_code' => 'MCC-20260924-PAYMENT',
            'user_id' => $publicUser->id,
            'status' => BookingStatus::AWAITING_PAYMENT,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'commercial',
            'nominal_amount' => 500000.00,
        ]);

        BookingSlot::create([
            'booking_id' => $booking2->id,
            'room_id' => 2,
            'event_start_at' => Carbon::now()->addDays(10)->setTime(13, 0),
            'event_end_at' => Carbon::now()->addDays(10)->setTime(17, 0),
            'operational_range' => '["2026-10-04 13:00:00", "2026-10-04 17:30:00")',
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $booking2->id,
            'event_name' => 'Konser Musik Jazz Indie',
            'event_category' => 'Komersial',
            'ekraf_subsector' => 'Musik',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 150,
            'event_description' => 'Konser musik komersial.',
            'proposal_file_url' => 'proposals/sample_proposal2.pdf',
            'pic_name_snapshot' => 'Budi Santoso',
            'pic_phone_snapshot' => '+6281298765432',
            'institution_name_snapshot' => 'Indie Music Malang',
            'tos_agreed_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($marketing)->get(route('marketing.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Permohonan');
        $response->assertSee('Verifikasi Bayar');
        $response->assertSee('MCC-20260924-AWAITING');
        $response->assertSee('Mendesak'); // Urgent H-2 badge
        $response->assertSee('Workshop Animasi 3D Digital');
        $response->assertSee('Komunitas Animasi Malang');
    }

    public function test_triage_sorting_orders_closest_lead_time_first(): void
    {
        $marketing = User::create([
            'full_name' => 'Tim Marketing MCC',
            'email' => 'marketing@mcc.id',
            'phone_number' => '+6281234567890',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::MARKETING,
            'account_type' => AccountType::INSTANSI,
            'institution_name' => 'Malang Creative Center',
            'address' => 'Malang',
        ]);

        $publicUser = User::create([
            'full_name' => 'Farhan',
            'email' => 'farhan@example.com',
            'phone_number' => '+6281298765434',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Creative Hub',
            'address' => 'Malang',
        ]);

        // Event far in future (H+20) submitted earlier
        $bookingFar = Booking::create([
            'booking_code' => 'MCC-FAR-FUTURE',
            'user_id' => $publicUser->id,
            'status' => BookingStatus::AWAITING_MARKETING,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'created_at' => Carbon::now()->subDays(5),
        ]);
        BookingSlot::create([
            'booking_id' => $bookingFar->id,
            'room_id' => 1,
            'event_start_at' => Carbon::now()->addDays(20)->setTime(10, 0),
            'event_end_at' => Carbon::now()->addDays(20)->setTime(12, 0),
            'operational_range' => '["2026-10-14 10:00:00", "2026-10-14 12:30:00")',
            'is_active' => true,
        ]);
        EventDetail::create([
            'booking_id' => $bookingFar->id,
            'event_name' => 'Event Far Away',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'DKV',
            'estimated_participants' => 10,
            'event_description' => 'Event description',
            'proposal_file_url' => 'proposal.pdf',
            'pic_name_snapshot' => 'Farhan',
            'pic_phone_snapshot' => '+6281298765434',
            'institution_name_snapshot' => 'Creative Hub',
            'tos_agreed_at' => Carbon::now(),
        ]);

        // Event near in future (H+1 / Urgent) submitted later
        $bookingNear = Booking::create([
            'booking_code' => 'MCC-NEAR-URGENT',
            'user_id' => $publicUser->id,
            'status' => BookingStatus::AWAITING_MARKETING,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'created_at' => Carbon::now()->subMinutes(10),
        ]);
        BookingSlot::create([
            'booking_id' => $bookingNear->id,
            'room_id' => 1,
            'event_start_at' => Carbon::now()->addDays(1)->setTime(9, 0),
            'event_end_at' => Carbon::now()->addDays(1)->setTime(11, 0),
            'operational_range' => '["2026-09-25 09:00:00", "2026-09-25 11:30:00")',
            'is_active' => true,
        ]);
        EventDetail::create([
            'booking_id' => $bookingNear->id,
            'event_name' => 'Urgent Workshop Tomorrow',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Film',
            'estimated_participants' => 10,
            'event_description' => 'Urgent event description',
            'proposal_file_url' => 'proposal.pdf',
            'pic_name_snapshot' => 'Farhan',
            'pic_phone_snapshot' => '+6281298765434',
            'institution_name_snapshot' => 'Creative Hub',
            'tos_agreed_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($marketing)->get(route('marketing.dashboard'));
        $response->assertStatus(200);

        // Content of near event should appear BEFORE far event in HTML
        $content = $response->getContent();
        $posNear = strpos($content, 'MCC-NEAR-URGENT');
        $posFar = strpos($content, 'MCC-FAR-FUTURE');

        $this->assertTrue($posNear !== false && $posFar !== false);
        $this->assertTrue($posNear < $posFar, 'Closest event lead time must appear first in triage order.');
    }

    public function test_multi_criteria_filters_work_properly(): void
    {
        $marketing = User::create([
            'full_name' => 'Tim Marketing MCC',
            'email' => 'marketing@mcc.id',
            'phone_number' => '+6281234567890',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::MARKETING,
            'account_type' => AccountType::INSTANSI,
            'institution_name' => 'Malang Creative Center',
            'address' => 'Malang',
        ]);

        $publicUser = User::create([
            'full_name' => 'Siti Rahma',
            'email' => 'siti@example.com',
            'phone_number' => '+6281298765433',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::INSTANSI,
            'institution_name' => 'Dinas Koperasi Malang',
            'address' => 'Malang',
        ]);

        $booking = Booking::create([
            'booking_code' => 'MCC-FLOOR5-CULINARY',
            'user_id' => $publicUser->id,
            'status' => BookingStatus::AWAITING_MARKETING,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => 2, // Auditorium Floor 5
            'event_start_at' => Carbon::now()->addDays(5)->setTime(10, 0),
            'event_end_at' => Carbon::now()->addDays(5)->setTime(14, 0),
            'operational_range' => '["2026-09-29 10:00:00", "2026-09-29 14:30:00")',
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Festival Kuliner Nusantara',
            'event_category' => 'Dinas',
            'ekraf_subsector' => 'Kuliner',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 100,
            'event_description' => 'Festival kuliner.',
            'proposal_file_url' => 'proposal.pdf',
            'pic_name_snapshot' => 'Siti Rahma',
            'pic_phone_snapshot' => '+6281298765433',
            'institution_name_snapshot' => 'Dinas Koperasi Malang',
            'tos_agreed_at' => Carbon::now(),
        ]);

        // Filter by Floor 5 -> should see it
        $resFloor5 = $this->actingAs($marketing)->get(route('marketing.dashboard', ['floor_level' => 5]));
        $resFloor5->assertStatus(200);
        $resFloor5->assertSee('MCC-FLOOR5-CULINARY');

        // Filter by Floor 2 -> should NOT see it
        $resFloor2 = $this->actingAs($marketing)->get(route('marketing.dashboard', ['floor_level' => 2]));
        $resFloor2->assertStatus(200);
        $resFloor2->assertDontSee('MCC-FLOOR5-CULINARY');

        // Filter by Sub-Sector Kuliner -> should see it
        $resEkraf = $this->actingAs($marketing)->get(route('marketing.dashboard', ['sub_sector' => 'Kuliner']));
        $resEkraf->assertStatus(200);
        $resEkraf->assertSee('MCC-FLOOR5-CULINARY');

        // Filter by Sub-Sector Musik -> should NOT see it
        $resEkrafMusic = $this->actingAs($marketing)->get(route('marketing.dashboard', ['sub_sector' => 'Musik']));
        $resEkrafMusic->assertStatus(200);
        $resEkrafMusic->assertDontSee('MCC-FLOOR5-CULINARY');
    }

    public function test_marketing_can_access_proposal_preview_with_signed_url(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        \Illuminate\Support\Facades\Storage::disk('public')->put('proposals/test_proposal.pdf', '%PDF-1.4 test content');

        $marketing = User::create([
            'full_name' => 'Tim Marketing MCC',
            'email' => 'marketing@mcc.id',
            'phone_number' => '+6281234567890',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::MARKETING,
            'account_type' => AccountType::INSTANSI,
            'institution_name' => 'Malang Creative Center',
            'address' => 'Malang',
        ]);

        $booking = Booking::create([
            'booking_code' => 'MCC-20260925-TESTPDF',
            'user_id' => $marketing->id,
            'status' => BookingStatus::AWAITING_MARKETING,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Pameran Desain Komunikasi Visual',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Desain Komunikasi Visual',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 50,
            'event_description' => 'Pameran karya mahasiswa DKV se-Jawa Timur.',
            'proposal_file_url' => '/storage/proposals/test_proposal.pdf',
            'pic_name_snapshot' => 'Andi Wijaya',
            'pic_phone_snapshot' => '+6281234567890',
            'institution_name_snapshot' => 'Asosiasi DKV Malang',
            'tos_agreed_at' => Carbon::now(),
        ]);

        $previewUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'marketing.proposal.preview',
            now()->addHour(),
            ['booking' => $booking->id]
        );

        $response = $this->actingAs($marketing)->get($previewUrl);
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
