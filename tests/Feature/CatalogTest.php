<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\RoomFacility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_catalog_with_seeded_rooms(): void
    {
        $room = Room::create([
            'id' => 1,
            'name' => 'Auditorium Utama MCC',
            'slug' => 'auditorium-utama-mcc-1',
            'floor_level' => 7,
            'capacity' => 1000,
            'description' => 'Hall utama terbesar di MCC untuk konser dan wisuda.',
            'is_active' => true,
        ]);

        RoomFacility::create([
            'room_id' => $room->id,
            'facility_name' => 'Sound System Concert Level',
            'quantity' => 1,
            'unit' => 'set',
            'condition' => 'good',
        ]);

        $response = $this->get('/katalog');

        $response->assertStatus(200);
        $response->assertSee('Auditorium Utama MCC');
        $response->assertSee('Lantai 7');
    }

    public function test_can_view_room_detail_page_with_full_description(): void
    {
        $room = Room::create([
            'id' => 7,
            'name' => 'Studio Podcast',
            'slug' => 'studio-podcast-7',
            'floor_level' => 2,
            'capacity' => 14,
            'description' => 'Studio kedap suara berkapasitas 4 orang narasumber dan 10 orang di area tunggu.',
            'is_active' => true,
        ]);

        RoomFacility::create([
            'room_id' => $room->id,
            'facility_name' => 'Ruang Akustik Kedap Suara',
            'quantity' => 1,
            'unit' => 'set',
            'condition' => 'good',
        ]);

        $response = $this->get('/ruangan/' . $room->id);

        $response->assertStatus(200);
        $response->assertSee('Studio Podcast');
        $response->assertSee('Studio kedap suara berkapasitas 4 orang narasumber');
        $response->assertSee('Ruang Akustik Kedap Suara');
    }
}
