<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Display the room catalog list.
     */
    public function index(Request $request): View
    {
        $rooms = \Illuminate\Support\Facades\Cache::remember('mcc_active_rooms_catalog', 86400, function () {
            return Room::with(['facilities', 'images'])
                ->where('is_active', true)
                ->orderBy('floor_level')
                ->orderBy('name')
                ->get();
        });

        $floors = $rooms->pluck('floor_level')->unique()->sort()->values();

        return view('public.catalog', [
            'rooms' => $rooms,
            'floors' => $floors,
        ]);
    }

    /**
     * Display room detailed information.
     */
    public function show(string $id): View
    {
        $room = Room::with(['facilities', 'images'])
            ->where('is_active', true)
            ->where(function ($query) use ($id) {
                if (is_numeric($id)) {
                    $query->where('id', (int) $id);
                } else {
                    $query->where('slug', $id);
                }
            })
            ->firstOrFail();

        // Rekomendasi ruangan lain di lantai yang sama atau lantai terdekat
        $relatedRooms = Room::with(['facilities', 'images'])
            ->where('is_active', true)
            ->where('id', '!=', $room->id)
            ->where('floor_level', $room->floor_level)
            ->take(3)
            ->get();

        if ($relatedRooms->isEmpty()) {
            $relatedRooms = Room::with(['facilities', 'images'])
                ->where('is_active', true)
                ->where('id', '!=', $room->id)
                ->take(3)
                ->get();
        }

        return view('public.room-detail', [
            'room' => $room,
            'relatedRooms' => $relatedRooms,
        ]);
    }
}
