<?php

namespace App\Http\Controllers\User;

use App\Actions\Booking\SubmitBookingRevisionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\SubmitBookingRevisionRequest;
use App\Models\Booking;
use App\Services\EPassService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the authenticated user's dashboard with live metrics and real booking requests.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Query real bookings belonging to the authenticated user with relations eager-loaded
        $bookings = $user->bookings()
            ->with(['eventDetail', 'slots.room', 'approvals.actionBy'])
            ->latest('created_at')
            ->get();

        $metrics = [
            'awaiting' => $bookings->filter(fn($b) => in_array($b->status instanceof \BackedEnum ? $b->status->value : $b->status, ['draft', 'awaiting_marketing', 'awaiting_payment']))->count(),
            'revised' => $bookings->filter(fn($b) => ($b->status instanceof \BackedEnum ? $b->status->value : $b->status) === 'revised')->count(),
            'confirmed' => $bookings->filter(fn($b) => in_array($b->status instanceof \BackedEnum ? $b->status->value : $b->status, ['confirmed', 'checked_in', 'completed']))->count(),
            'total' => $bookings->count(),
        ];

        return view('user.dashboard', [
            'user' => $user,
            'bookings' => $bookings,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Show the revision wizard page with prefilled event & slot data (REQ-USR-05).
     */
    public function showRevision(Request $request, Booking $booking): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->cannot('update', $booking)) {
            abort(403, 'Anda tidak memiliki akses ke permohonan ini.');
        }

        $currentStatus = $booking->status instanceof \BackedEnum ? $booking->status->value : (string) $booking->status;
        if ($currentStatus !== 'revised') {
            return redirect()->route('dashboard')->with('error', 'Permohonan ini tidak dalam status perlu revisi.');
        }

        if ($booking->revision_deadline && $booking->revision_deadline->isPast()) {
            return redirect()->route('dashboard')->with('error', 'Masa tenggang perbaikan 24 jam untuk permohonan ini telah berakhir.');
        }

        $booking->load(['slots.room.facilities', 'eventDetail', 'approvals.actionBy']);

        $sessions = [];
        $totalHours = 0;
        $minCapacity = 999999;
        $totalFee = 0.00;

        foreach ($booking->slots as $slot) {
            $room = $slot->room;
            $durationHours = (int) ceil($slot->event_start_at->diffInMinutes($slot->event_end_at) / 60);
            $totalHours += $durationHours;
            if ($room) {
                $minCapacity = min($minCapacity, $room->capacity);
                $totalFee += ($room->base_hourly_rate * $durationHours);
            }

            $sessions[] = [
                'room' => $room,
                'room_id' => $room?->id,
                'date' => $slot->event_start_at->toDateString(),
                'date_formatted' => $slot->event_start_at->locale('id')->translatedFormat('l, d F Y'),
                'start_time' => $slot->event_start_at->format('H:i'),
                'end_time' => $slot->event_end_at->format('H:i'),
                'duration_hours' => $durationHours,
            ];
        }

        if ($minCapacity === 999999) {
            $minCapacity = $booking->slots->first()?->room?->capacity ?? 50;
        }

        $latestRevisionApproval = $booking->approvals ? $booking->approvals->where('decision', 'revised')->last() : null;

        return view('user.revision-wizard', [
            'booking' => $booking,
            'sessions' => $sessions,
            'totalHours' => $totalHours,
            'minCapacity' => $minCapacity,
            'totalFee' => $totalFee,
            'latestRevisionApproval' => $latestRevisionApproval,
            'user' => $user,
        ]);
    }

    /**
     * Submit revised event details and proposal document within 24-hour grace period (REQ-USR-05).
     */
    public function submitRevision(
        SubmitBookingRevisionRequest $request,
        Booking $booking,
        SubmitBookingRevisionAction $action
    ): RedirectResponse|JsonResponse {
        $dto = $request->toDTO($booking->id, (string) $request->user()->id);
        $updatedBooking = $action->execute($dto);

        $message = "Perbaikan permohonan {$updatedBooking->booking_code} berhasil dikirimkan. Status permohonan kembali ke antrean kurasi marketing.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'booking' => $updatedBooking,
            ]);
        }

        return redirect()->route('dashboard')->with('success', $message);
    }

    /**
     * Display the official digital E-Pass with dynamic QR code (REQ-USR-06).
     */
    public function showEpass(Request $request, Booking $booking, EPassService $epassService): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->cannot('view', $booking)) {
            abort(403, 'Anda tidak memiliki akses ke E-Pass permohonan ini.');
        }

        $statusVal = $booking->status instanceof \BackedEnum ? $booking->status->value : (string) $booking->status;

        // E-Pass is only available if booking is confirmed, checked in, or completed
        if (! in_array($statusVal, ['confirmed', 'checked_in', 'completed'], true)) {
            return redirect()->route('dashboard')->with(
                'error',
                'E-Pass Digital belum diterbitkan atau masih ditahan hingga permohonan disetujui / pembayaran divalidasi.'
            );
        }

        $booking->load(['slots.room.facilities', 'eventDetail', 'user', 'confirmedBy']);

        $qrCodeSvg = $epassService->generateQrCodeSvg($booking);
        $scheduleData = $epassService->formatScheduleSessions($booking);

        return view('user.epass', [
            'booking' => $booking,
            'qrCodeSvg' => $qrCodeSvg,
            'scheduleData' => $scheduleData,
            'user' => $user,
        ]);
    }

    /**
     * Download the official E-Pass as a standalone PDF document.
     */
    public function downloadEpassPdf(Request $request, Booking $booking, EPassService $epassService)
    {
        $user = $request->user();

        if ($user->cannot('view', $booking)) {
            abort(403, 'Anda tidak memiliki akses ke E-Pass permohonan ini.');
        }

        $statusVal = $booking->status instanceof \BackedEnum ? $booking->status->value : (string) $booking->status;

        if (! in_array($statusVal, ['confirmed', 'checked_in', 'completed'], true)) {
            return redirect()->route('dashboard')->with(
                'error',
                'E-Pass Digital belum diterbitkan atau masih ditahan hingga permohonan disetujui / pembayaran divalidasi.'
            );
        }

        $booking->load(['slots.room.facilities', 'eventDetail', 'user', 'confirmedBy']);

        $qrCodeSvg = $epassService->generateQrCodeSvg($booking, 160);
        $scheduleData = $epassService->formatScheduleSessions($booking);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.epass', [
            'booking' => $booking,
            'qrCodeSvg' => $qrCodeSvg,
            'scheduleData' => $scheduleData,
            'user' => $user,
        ])->setPaper('a4', 'portrait');

        $fileName = "E-Pass-MCC-{$booking->booking_code}.pdf";

        return $pdf->download($fileName);
    }
}
