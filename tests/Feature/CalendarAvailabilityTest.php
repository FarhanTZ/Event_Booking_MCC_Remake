<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CalendarAvailabilityTest extends TestCase
{
    use RefreshDatabase;

    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->room = Room::query()->create([
            'id' => 1,
            'name' => 'Auditorium Utama MCC',
            'slug' => 'auditorium-utama-mcc-1',
            'floor_level' => 7,
            'capacity' => 1000,
            'description' => 'Hall utama terbesar di MCC untuk konser dan wisuda.',
            'is_active' => true,
            'buffer_minutes' => 30,
        ]);

        RoomFacility::query()->create([
            'room_id' => $this->room->id,
            'facility_name' => 'Sound System Concert Level',
            'quantity' => 1,
            'unit' => 'set',
            'condition' => 'good',
        ]);
    }

    public function test_calendar_page_can_be_rendered(): void
    {
        $response = $this->get('/jadwal');

        $response->assertStatus(200);
        $response->assertSee('Kalender Ketersediaan Slot Ruangan');
        $response->assertSee('Zero Double-Booking Engine');
        $response->assertSee('Auditorium Utama MCC');
    }

    public function test_room_availability_api_returns_correct_hourly_matrix(): void
    {
        $date = now()->addDays(5)->toDateString();

        $response = $this->getJson("/api/rooms/{$this->room->id}/availability?date={$date}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'room' => ['id', 'name', 'floor', 'capacity', 'buffer_minutes'],
            'date',
            'slots' => [
                '*' => ['time', 'start_time', 'end_time', 'status', 'label', 'is_selectable'],
            ],
        ]);

        $this->assertCount(14, $response->json('slots'));
    }

    public function test_user_can_acquire_slot_lock(): void
    {
        $date = now()->addDays(6)->toDateString();
        $slotTime = '09:00 - 10:00';

        $response = $this->postJson('/api/booking/lock-slot', [
            'room_id' => $this->room->id,
            'date' => $date,
            'slot_time' => $slotTime,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    public function test_user_can_release_slot_lock_instantly_upon_cancellation(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $date = now()->addDays(7)->toDateString();
        $slotTime = '13:00 - 14:00';

        $this->actingAs($user);

        // 1. Lock slot
        $lockRes = $this->postJson('/api/booking/lock-slot', [
            'room_id' => $this->room->id,
            'date' => $date,
            'slot_time' => $slotTime,
        ]);
        $lockRes->assertStatus(200);

        // 2. Unlock slot
        $unlockRes = $this->postJson('/api/booking/unlock-slot', [
            'room_id' => $this->room->id,
            'date' => $date,
            'slot_time' => $slotTime,
        ]);
        $unlockRes->assertStatus(200);
        $unlockRes->assertJson([
            'success' => true,
        ]);

        // 3. Verify slot is immediately available
        $availRes = $this->getJson("/api/rooms/{$this->room->id}/availability?date={$date}");
        $availRes->assertStatus(200);
        $slots = collect($availRes->json('slots'));
        $targetSlot = $slots->firstWhere('time', $slotTime);
        $this->assertEquals('available', $targetSlot['status']);
    }

    public function test_booked_slots_and_buffer_are_rendered_correctly(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'full_name' => 'Budi Pemohon',
            'phone_number' => '08123456789',
            'institution_name' => 'Komunitas Animasi',
        ]);
        $date = now()->addDays(8)->toDateString();

        /** @var Booking $booking */
        $booking = Booking::query()->create([
            'id' => Str::uuid()->toString(),
            'booking_code' => 'BOOK-TEST-001',
            'user_id' => $user->id,
            'status' => 'confirmed',
        ]);

        EventDetail::query()->create([
            'booking_id' => (string) $booking->id,
            'event_name' => 'Seminar Animasi Digital',
            'event_category' => 'Seminar',
            'estimated_participants' => 100,
            'event_description' => 'Seminar industri kreatif',
            'proposal_file_url' => '/proposals/test.pdf',
            'pic_name_snapshot' => (string) $user->full_name,
            'pic_phone_snapshot' => (string) $user->phone_number,
            'institution_name_snapshot' => (string) $user->institution_name,
            'tos_agreed_at' => now(),
        ]);

        $eventStart = Carbon::parse("{$date} 10:00:00");
        $eventEnd = Carbon::parse("{$date} 12:00:00");

        BookingSlot::query()->create([
            'booking_id' => (string) $booking->id,
            'room_id' => $this->room->id,
            'event_start_at' => $eventStart,
            'event_end_at' => $eventEnd,
            'is_active' => true,
        ]);

        $response = $this->getJson("/api/rooms/{$this->room->id}/availability?date={$date}");
        $response->assertStatus(200);

        $slots = collect($response->json('slots'));

        // Jam 10:00 - 11:00 -> booked
        $slot10 = $slots->firstWhere('time', '10:00 - 11:00');
        $this->assertEquals('booked', $slot10['status']);
        $this->assertStringContainsString('Seminar Animasi Digital', $slot10['label']);

        // Jam 11:00 - 12:00 -> booked
        $slot11 = $slots->firstWhere('time', '11:00 - 12:00');
        $this->assertEquals('booked', $slot11['status']);

        // Jam 12:00 - 13:00 -> buffer (30m reset & pembersihan)
        $slot12 = $slots->firstWhere('time', '12:00 - 13:00');
        $this->assertEquals('buffer', $slot12['status']);

        // Jam 13:00 - 14:00 -> available
        $slot13 = $slots->firstWhere('time', '13:00 - 14:00');
        $this->assertEquals('available', $slot13['status']);
    }

    public function test_inactive_room_returns_maintenance_status(): void
    {
        $inactiveRoom = Room::query()->create([
            'id' => 99,
            'name' => 'Studio Pemeliharaan',
            'slug' => 'studio-pemeliharaan-99',
            'floor_level' => 3,
            'capacity' => 20,
            'description' => 'Sedang renovasi tata suara.',
            'is_active' => false,
        ]);

        $date = now()->addDays(1)->toDateString();
        $response = $this->getJson("/api/rooms/{$inactiveRoom->id}/availability?date={$date}");

        $response->assertStatus(200);
        $slots = $response->json('slots');
        $this->assertCount(14, $slots);
        $this->assertEquals('maintenance', $slots[0]['status']);
    }
}
