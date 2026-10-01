<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\EmergencyCancelBookingAction;
use App\Actions\Admin\GetInterventionBookingListAction;
use App\DataTransferObjects\Admin\InterventionFilterData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmergencyCancelBookingRequest;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmergencyInterventionController extends Controller
{
    /**
     * Display listing of active/upcoming bookings for emergency intervention (FR-ADM-05).
     */
    public function index(Request $request, GetInterventionBookingListAction $action): View
    {
        $dto = InterventionFilterData::fromArray($request->all());
        $data = $action->execute($dto);

        return view('admin.interventions.index', [
            'bookings' => $data['bookings'],
            'metrics' => $data['metrics'],
            'filter' => $data['filter'],
        ]);
    }

    /**
     * Execute emergency Super Override cancellation for a booking (FR-ADM-05).
     */
    public function cancel(
        EmergencyCancelBookingRequest $request,
        Booking $booking,
        EmergencyCancelBookingAction $action
    ): RedirectResponse {
        $dto = $request->toDto((string) $booking->id, (string) auth()->id());
        $cancelledBooking = $action->execute($booking, $dto);

        return redirect()->route('admin.interventions.index')
            ->with('success', "Reservasi #{$cancelledBooking->booking_code} ('{$cancelledBooking->eventDetail?->event_name}') berhasil dibatalkan paksa (Super Override). Seluruh slot jadwal telah seketika dilepas.");
    }
}
