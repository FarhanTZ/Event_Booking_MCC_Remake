<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingWizardController extends Controller
{
    public function index(Request $request): View
    {
        $roomId = (int) $request->query('room_id', 1);
        $date = (string) $request->query('date', now()->toDateString());
        $slotTime = (string) $request->query('slot', '08:00 - 09:00');

        $room = Room::with(['facilities'])->find($roomId) ?? Room::first();

        return view('user.wizard', [
            'room' => $room,
            'date' => $date,
            'slotTime' => $slotTime,
        ]);
    }

    public function store(Request $request)
    {
        return redirect('/dashboard')->with('success', 'Permohonan reservasi berhasil diajukan! Menunggu kurasi tim MCC.');
    }
}
