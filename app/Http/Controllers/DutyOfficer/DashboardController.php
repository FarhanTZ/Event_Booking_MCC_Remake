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
        ConfirmRoomOpeningRequest $request,
        ConfirmRoomOpeningAction $action
    ): RedirectResponse|JsonResponse {
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

    /**
     * Submit green clearance signal (Room clean & assets intact) (FR-DO-04).
     */
    public function submitClearance(
        Booking $booking,
        \App\Http\Requests\DutyOfficer\SubmitRoomClearanceRequest $request,
        \App\Actions\DutyOfficer\SubmitRoomClearanceAction $action
    ): RedirectResponse|JsonResponse {
        $userId = (string) Auth::id();
        $dto = $request->toDTO($userId, $booking->id);
        $result = $action->execute($dto);

        $message = "Verifikasi selesai! Sinyal Aman (Clear) berhasil dikirimkan ke Front Office untuk pelepasan KTP ({$booking->booking_code}).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result,
            ]);
        }

        return redirect()->route('duty_officer.dashboard')->with('success', $message);
    }

    /**
     * Report field incident and transmit Red Signal (FR-DO-05).
     */
    public function reportIncident(
        Booking $booking,
        \App\Http\Requests\DutyOfficer\ReportRoomIncidentRequest $request,
        \App\Actions\DutyOfficer\ReportRoomIncidentAction $action
    ): RedirectResponse|JsonResponse {
        $userId = (string) Auth::id();
        $dto = $request->toDTO($userId, $booking->id);
        $result = $action->execute($dto);

        $message = "Laporan insiden berhasil dikirim! Sinyal Merah telah mengunci pelepasan KTP di Front Office ({$booking->booking_code}).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result,
            ]);
        }

        return redirect()->route('duty_officer.dashboard')->with('success', $message);
    }

    /**
     * Resolve field incident and transmit Resolved Signal (FR-DO-06).
     */
    public function resolveIncident(
        Booking $booking,
        \App\Http\Requests\DutyOfficer\ResolveRoomIncidentRequest $request,
        \App\Actions\DutyOfficer\ResolveRoomIncidentAction $action
    ): RedirectResponse|JsonResponse {
        $userId = (string) Auth::id();
        $dto = $request->toDTO($userId, $booking->id);
        $result = $action->execute($dto);

        $message = "Insiden berhasil diselesaikan! Sinyal Resolved berhasil dikirim ke Front Office untuk membuka kembali tombol pelepasan KTP ({$booking->booking_code}).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result,
            ]);
        }

        return redirect()->route('duty_officer.dashboard')->with('success', $message);
    }

    /**
     * Confirm abandoned room is clean & safe (FR-DO-07 Opsi A).
     */
    public function confirmAbandonedClean(
        Booking $booking,
        \App\Http\Requests\DutyOfficer\ConfirmAbandonedCleanRequest $request,
        \App\Actions\DutyOfficer\ConfirmAbandonedCleanAction $action
    ): RedirectResponse|JsonResponse {
        $userId = (string) Auth::id();
        $dto = $request->toDTO($userId, $booking->id);
        $result = $action->execute($dto);

        $message = "Konfirmasi selesai! Ruangan {$booking->booking_code} dinyatakan ditinggal bersih dan aman (abandoned_clean).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result,
            ]);
        }

        return redirect()->route('duty_officer.dashboard')->with('success', $message);
    }

    /**
     * Report abandoned room incident and trigger user suspension (FR-DO-07 Opsi B).
     */
    public function reportAbandonedIncident(
        Booking $booking,
        \App\Http\Requests\DutyOfficer\ReportAbandonedIncidentRequest $request,
        \App\Actions\DutyOfficer\ReportAbandonedIncidentAction $action
    ): RedirectResponse|JsonResponse {
        $userId = (string) Auth::id();
        $dto = $request->toDTO($userId, $booking->id);
        $result = $action->execute($dto);

        $message = "Laporan insiden kasus ditinggal berhasil dikirim! Status acara diubah menjadi abandoned_incident dan akun pemohon otomatis dibekukan.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $result,
            ]);
        }

        return redirect()->route('duty_officer.dashboard')->with('success', $message);
    }
}

