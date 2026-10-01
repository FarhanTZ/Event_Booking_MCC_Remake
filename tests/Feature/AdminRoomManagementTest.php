<?php

namespace Tests\Feature;

use App\Enums\AccountType;
use App\Enums\BookingStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminRoomManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $regularUser;
    private Room $testRoom;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // 1. Create Super Admin User
        $this->superAdmin = User::create([
            'full_name' => 'Super Administrator',
            'email' => 'admin.master@mcc.id',
            'phone_number' => '+6281111111111',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::SUPER_ADMIN,
            'account_type' => AccountType::INSTANSI,
            'institution_name' => 'Disporapar Kota Malang',
            'address' => 'Malang',
        ]);

        // 2. Create Regular User
        $this->regularUser = User::create([
            'full_name' => 'Budi Santoso',
            'email' => 'budi.applicant@gmail.com',
            'phone_number' => '+6289999999999',
            'password_hash' => Hash::make('Password123!'),
            'role' => UserRole::USER,
            'account_type' => AccountType::KOMUNITAS,
            'institution_name' => 'Komunitas Kreatif',
            'address' => 'Malang',
        ]);

        // 3. Create Sample Room
        $this->testRoom = Room::create([
            'name' => 'Auditorium Utama Lantai 7',
            'slug' => 'auditorium-utama-lantai-7',
            'floor_level' => 7,
            'capacity' => 450,
            'room_size_sqm' => 520.50,
            'buffer_minutes' => 45,
            'is_commercial_allowed' => true,
            'base_hourly_rate' => 1500000.00,
            'is_active' => true,
            'description' => 'Auditorium utama MCC berkapasitas besar untuk konser dan seminar.',
            'supported_layouts' => ['theater', 'classroom', 'standing'],
        ]);

        // 4. Create Sample Facilities
        RoomFacility::create([
            'room_id' => $this->testRoom->id,
            'facility_name' => 'Mikrofon Wireless Shure',
            'quantity' => 4,
            'unit' => 'unit',
            'condition' => 'good',
        ]);

        RoomFacility::create([
            'room_id' => $this->testRoom->id,
            'facility_name' => 'Proyektor Laser 10.000 Lumens',
            'quantity' => 2,
            'unit' => 'unit',
            'condition' => 'fair',
        ]);
    }

    public function test_super_admin_can_access_rooms_index(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('admin.rooms.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Data Master Ruangan');
        $response->assertSee('Auditorium Utama Lantai 7');
        $response->assertSee('Lantai 7');
    }

    public function test_regular_user_is_forbidden_from_accessing_admin_rooms(): void
    {
        $response = $this->actingAs($this->regularUser)
            ->get(route('admin.rooms.index'));

        $response->assertForbidden();
    }

    public function test_super_admin_can_create_room_with_photos_and_layouts(): void
    {
        $dummyPhoto = UploadedFile::fake()->image('studio_podcast.jpg', 1200, 800);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.rooms.store'), [
                'name' => 'Studio Podcast & Broadcast 2',
                'slug' => 'studio-podcast-broadcast-2',
                'floor_level' => 2,
                'capacity' => 10,
                'room_size_sqm' => 35.0,
                'buffer_minutes' => 30,
                'is_commercial_allowed' => true,
                'base_hourly_rate' => 250000.00,
                'is_active' => true,
                'description' => 'Studio rekaman audio visual berisolasi suara kedap.',
                'supported_layouts' => ['roundtable', 'boardroom'],
                'photos' => [$dummyPhoto],
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rooms', [
            'name' => 'Studio Podcast & Broadcast 2',
            'slug' => 'studio-podcast-broadcast-2',
            'floor_level' => 2,
            'capacity' => 10,
            'buffer_minutes' => 30,
        ]);

        $createdRoom = Room::where('slug', 'studio-podcast-broadcast-2')->first();
        $this->assertNotNull($createdRoom);
        $this->assertCount(1, $createdRoom->images);
        $this->assertTrue($createdRoom->is_active);
    }

    public function test_create_room_validation_fails_on_invalid_floor_or_empty_name(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.rooms.store'), [
                'name' => '',
                'floor_level' => 99, // invalid floor (must be 1-8)
                'capacity' => 0,
                'room_size_sqm' => 0,
                'buffer_minutes' => 30,
                'base_hourly_rate' => -100,
                'description' => 'Short',
                'supported_layouts' => [],
            ]);

        $response->assertSessionHasErrors(['name', 'floor_level', 'capacity', 'room_size_sqm', 'base_hourly_rate', 'description', 'supported_layouts']);
    }

    public function test_super_admin_can_update_room(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.rooms.update', $this->testRoom), [
                'name' => 'Auditorium Utama Revamped',
                'slug' => 'auditorium-utama-revamped',
                'floor_level' => 7,
                'capacity' => 500,
                'room_size_sqm' => 550.0,
                'buffer_minutes' => 60,
                'is_commercial_allowed' => true,
                'base_hourly_rate' => 2000000.00,
                'is_active' => true,
                'description' => 'Auditorium termutakhir MCC di lantai 7.',
                'supported_layouts' => ['theater', 'standing'],
            ]);

        $response->assertRedirect(route('admin.rooms.show', $this->testRoom));
        $this->testRoom->refresh();
        $this->assertEquals('Auditorium Utama Revamped', $this->testRoom->name);
        $this->assertEquals(500, $this->testRoom->capacity);
        $this->assertEquals(60, $this->testRoom->buffer_minutes);
    }

    public function test_super_admin_can_toggle_room_active_status(): void
    {
        $this->assertTrue($this->testRoom->is_active);

        $response = $this->actingAs($this->superAdmin)
            ->patch(route('admin.rooms.toggle_status', $this->testRoom), [
                'is_active' => false,
            ]);

        $response->assertRedirect();
        $this->testRoom->refresh();
        $this->assertFalse($this->testRoom->is_active);
    }

    public function test_super_admin_can_delete_room_without_active_bookings(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.rooms.destroy', $this->testRoom));

        $response->assertRedirect(route('admin.rooms.index'));
        $this->assertDatabaseMissing('rooms', ['id' => $this->testRoom->id]);
    }

    public function test_super_admin_cannot_delete_room_with_active_upcoming_bookings(): void
    {
        // Create active confirmed booking on testRoom
        $booking = Booking::create([
            'booking_code' => 'MCC-20261001-TEST01',
            'user_id' => $this->regularUser->id,
            'status' => BookingStatus::CONFIRMED,
            'clearance_status' => 'pending',
            'pricing_scheme' => 'commercial',
            'nominal_amount' => 1500000.00,
        ]);

        BookingSlot::create([
            'booking_id' => $booking->id,
            'room_id' => $this->testRoom->id,
            'event_start_at' => Carbon::now()->addDays(2),
            'event_end_at' => Carbon::now()->addDays(2)->addHours(3),
            'price' => 1500000.00,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.rooms.destroy', $this->testRoom));

        $response->assertSessionHasErrors(['room']);
        $this->assertDatabaseHas('rooms', ['id' => $this->testRoom->id]);
    }

    public function test_super_admin_can_add_room_facility_fr_adm_02(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->post(route('admin.rooms.facilities.store', $this->testRoom), [
                'facility_name' => 'Sound System Yamaha StagePas 1K',
                'quantity' => 2,
                'unit' => 'set',
                'condition' => 'good',
            ]);

        $response->assertRedirect(route('admin.rooms.show', $this->testRoom));
        $this->assertDatabaseHas('room_facilities', [
            'room_id' => $this->testRoom->id,
            'facility_name' => 'Sound System Yamaha StagePas 1K',
            'quantity' => 2,
            'unit' => 'set',
            'condition' => 'good',
        ]);
    }

    public function test_super_admin_can_update_room_facility_condition_fr_adm_02(): void
    {
        $facility = $this->testRoom->facilities->first();

        $response = $this->actingAs($this->superAdmin)
            ->put(route('admin.rooms.facilities.update', [$this->testRoom, $facility]), [
                'facility_name' => 'Mikrofon Wireless Shure Beta 58A',
                'quantity' => 4,
                'unit' => 'unit',
                'condition' => 'needs_repair',
            ]);

        $response->assertRedirect(route('admin.rooms.show', $this->testRoom));
        $facility->refresh();
        $this->assertEquals('needs_repair', $facility->condition);
        $this->assertEquals('Mikrofon Wireless Shure Beta 58A', $facility->facility_name);
    }

    public function test_super_admin_can_delete_room_facility(): void
    {
        $facility = $this->testRoom->facilities->first();

        $response = $this->actingAs($this->superAdmin)
            ->delete(route('admin.rooms.facilities.destroy', [$this->testRoom, $facility]));

        $response->assertRedirect(route('admin.rooms.show', $this->testRoom));
        $this->assertDatabaseMissing('room_facilities', ['id' => $facility->id]);
    }
}
