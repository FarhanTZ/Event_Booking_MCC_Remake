<?php

namespace App\Http\Controllers\User;

use App\Actions\Booking\CreateBookingProposalAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingProposalRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingWizardController extends Controller
{
    /**
     * Render the booking wizard form.
     */
    public function index(Request $request): View
    {
        $roomId = (int) $request->query('room_id', 1);
        $date = (string) $request->query('date', now()->toDateString());
        $slotTime = (string) $request->query('slot', '08:00 - 09:00');

        $room = Room::with(['facilities', 'images'])->find($roomId) ?? Room::firstOrFail();

        return view('user.wizard', [
            'room' => $room,
            'date' => $date,
            'slotTime' => $slotTime,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Store submitted booking proposal.
     */
    public function store(
        CreateBookingProposalRequest $request,
        CreateBookingProposalAction $action
    ): RedirectResponse {
        $booking = $action->execute($request->toDto());

        return redirect()->route('dashboard')->with(
            'success',
            "Permohonan reservasi ruangan berhasil diajukan dengan kode {$booking->booking_code}! Tim kurasi MCC akan meninjau proposal Anda."
        );
    }
}
