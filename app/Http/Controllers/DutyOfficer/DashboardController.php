<?php

namespace App\Http\Controllers\DutyOfficer;

use App\Actions\DutyOfficer\ConfirmRoomOpeningAction;
use App\Actions\DutyOfficer\GetDutyOfficerTasksAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\DutyOfficer\ConfirmRoomOpeningRequest;
use App\Http\Requests\DutyOfficer\DutyOfficerFilterRequest;
use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the mobile-first Duty Officer Field Dashboard (FR-DO-01).
     */
    public function index(DutyOfficerFilterRequest $request, GetDutyOfficerTasksAction $action): View|JsonResponse
    {
        $userId = (string) Auth::id();
        $dto = $request->toDTO($userId);
        $data = $action->execute($dto);

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('duty_officer.dashboard.index', $data);
    }

    /**
     * Confirm physical room opening & initial facility handover (FR-DO-02).
     */
    public function confirmRoomOpening(
        Booking $booking,
        \App\Http\Requests\DutyOfficer\ConfirmRoomOpeningRequest $request,
        \App\Actions\DutyOfficer\ConfirmRoomOpeningAction $action
    ): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse {
        $userId = (string) Auth::id();
        $dto = $request->toDTO($userId, $booking->id);
        $result = $action->execute($dto);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Akses ruangan {$booking->booking_code} berhasil dibuka dan serah terima fasilitas tercatat.",
                'data' => $result,
            ]);
        }

        return redirect()->route('duty_officer.dashboard')->with('success', "Akses fisik ruangan untuk acara '{$booking->eventDetail?->event_name}' ({$booking->booking_code}) berhasil dibuka dan serah terima fasilitas tercatat.");
    }
}
