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

class UserEpassTest extends TestCase
{
    use RefreshDatabase;

    protected User $applicant;
    protected User $otherApplicant;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->room = Room::create([
            'id' => 1,
            'name' => 'Auditorium Lantai 2',
            'slug' => 'auditorium-lantai-2',
            'floor_level' => 2,
            'capacity' => 150,
            'buffer_minutes' => 30,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 0.00,
            'is_active' => true,
            'description' => 'Auditorium utama MCC.',
        ]);

        $this->applicant = User::create([
            'full_name' => 'Faris Pratama',
            'email' => 'faris@komunitas.id',
            'phone_number' => '+6281234560001',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Desain Grafis Malang',
            'address' => 'Klojen, Malang',
        ]);

        $this->otherApplicant = User::create([
            'full_name' => 'Budi Santoso',
            'email' => 'budi@other.id',
            'phone_number' => '+6281234560002',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Film Malang',
            'address' => 'Lowokwaru, Malang',
        ]);
    }

    protected function createBooking(User $owner, BookingStatus $status = BookingStatus::CONFIRMED): Booking
    {
        $booking = Booking::create([
            'booking_code' => 'MCC-2026-' . strtoupper(\Illuminate\Support\Str::random(6)),
            'user_id' => $owner->id,
            'status' => $status,
            'clearance_status' => $status === BookingStatus::CONFIRMED ? 'approved' : 'pending',
            'pricing_scheme' => 'subsidy',
            'nominal_amount' => 0.00,
            'checkin_token' => $status === BookingStatus::CONFIRMED ? \Illuminate\Support\Str::random(64) : null,
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => Carbon::now()->addDays(3)->setTime(9, 0),
            'event_end_at' => Carbon::now()->addDays(3)->setTime(13, 0),
            'operational_range' => '["2026-09-30 09:00:00", "2026-09-30 13:30:00")',
            'is_active' => true,
        ]);

        EventDetail::create([
            'booking_id' => $booking->id,
            'event_name' => 'Festival Animasi dan Game Kota Malang 2026',
            'event_category' => 'Komunitas',
            'ekraf_subsector' => 'Game',
            'event_type' => 'Terbuka Untuk Umum',
            'estimated_participants' => 120,
            'event_description' => 'Festival pameran karya animasi dan game developer lokal.',
            'proposal_file_url' => '/storage/proposals/fest_game.pdf',
            'pic_name_snapshot' => $owner->full_name,
            'pic_phone_snapshot' => $owner->phone_number,
            'pic_emergency_phone' => '+6281299998888',
            'official_letter_number' => '009/FEST-GAME/2026',
            'institution_name_snapshot' => $owner->institution_name,
            'tos_agreed_at' => Carbon::now(),
        ]);

        return $booking;
    }

    public function test_applicant_can_view_epass_for_confirmed_booking(): void
    {
        $booking = $this->createBooking($this->applicant, BookingStatus::CONFIRMED);

        $response = $this->actingAs($this->applicant)->get(
            route('bookings.epass', ['booking' => $booking->id])
        );

        $response->assertStatus(200);
        $response->assertSee('E-PASS AKSES RESMI GEDUNG MCC');
        $response->assertSee($booking->booking_code);
        $response->assertSee('Festival Animasi dan Game Kota Malang 2026');
        $response->assertSee('Auditorium Lantai 2');
        $response->assertSee('Lantai 2');
        $response->assertSee('Faris Pratama');
        $response->assertSee('+6281234560001');
        // Exact mandatory instruction from REQ-USR-06
        $response->assertSee('Harap datang 30 menit sebelum acara dimulai dan titipkan fisik KTP asli penanggung jawab acara di meja Front Office Lantai Dasar untuk ditukar dengan akses ruangan.');
        // Dynamic QR code SVG
        $response->assertSee('<svg', false);
    }

    public function test_applicant_cannot_view_epass_for_unconfirmed_booking(): void
    {
        $booking = $this->createBooking($this->applicant, BookingStatus::AWAITING_PAYMENT);

        $response = $this->actingAs($this->applicant)->get(
            route('bookings.epass', ['booking' => $booking->id])
        );

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_other_user_cannot_view_another_users_epass(): void
    {
        $booking = $this->createBooking($this->applicant, BookingStatus::CONFIRMED);

        $response = $this->actingAs($this->otherApplicant)->get(
            route('bookings.epass', ['booking' => $booking->id])
        );

        $response->assertStatus(403);
    }

    public function test_user_dashboard_renders_epass_button_for_confirmed_booking(): void
    {
        $booking = $this->createBooking($this->applicant, BookingStatus::CONFIRMED);

        $response = $this->actingAs($this->applicant)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Buka E-Pass Digital');
        $response->assertSee(route('bookings.epass', ['booking' => $booking->id]));
    }

    public function test_applicant_can_download_epass_pdf(): void
    {
        $booking = $this->createBooking($this->applicant, BookingStatus::CONFIRMED);

        $response = $this->actingAs($this->applicant)->get(
            route('bookings.epass.download', ['booking' => $booking->id])
        );

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
