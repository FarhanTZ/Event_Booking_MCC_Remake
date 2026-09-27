<?php

namespace App\Http\Controllers\FrontOffice;

use App\Actions\FrontOffice\ValidateAndProcessCheckinAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\FrontOffice\ValidateCheckinRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Queries\FrontOffice\GetDailyManifestQuery;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManifestController extends Controller
{
    /**
     * Display the Front Office daily event manifest (FR-FO-01).
     *
     * @param Request $request
     * @param GetDailyManifestQuery $query
     * @return View
     */
    public function index(Request $request, GetDailyManifestQuery $query): View
    {
        // Mendukung tanggal hari ini (CURRENT_DATE) atau simulasi tanggal operasional
        $targetDateInput = $request->query('date');
        $targetDate = $targetDateInput ? Carbon::parse($targetDateInput) : Carbon::today();

        $metrics = $query->getMetrics($targetDate);
        $manifestSlots = $query->getDailyManifestSlots($request, $targetDate);
        $rooms = Room::query()->where('is_active', true)->orderBy('floor_level')->orderBy('name')->get();
        $dutyOfficers = $query->getActiveDutyOfficers();

        return view('front_office.manifest.index', [
            'metrics' => $metrics,
            'manifestSlots' => $manifestSlots,
            'rooms' => $rooms,
            'dutyOfficers' => $dutyOfficers,
            'targetDate' => $targetDate,
            'currentStatus' => $request->query('status', 'all'),
            'currentSearch' => $request->query('q', ''),
            'currentFloor' => $request->query('floor_level', 'all'),
            'currentRoom' => $request->query('room_id', 'all'),
        ]);
    }

    /**
     * Display the dedicated full-page check-in validation interface (FR-FO-02).
     */
    public function showCheckinPage(Request $request, GetDailyManifestQuery $query): View
    {
        $targetDate = Carbon::today();
        $metrics = $query->getMetrics($targetDate);
        $dutyOfficers = $query->getActiveDutyOfficers();

        return view('front_office.checkin.index', [
            'metrics' => $metrics,
            'dutyOfficers' => $dutyOfficers,
            'currentStatus' => 'all',
            'initialBookingCode' => $request->query('code', ''),
        ]);
    }

    /**
     * Inspect and verify check-in eligibility for dual-method scanner/manual (FR-FO-02).
     */
    public function inspectCheckin(ValidateCheckinRequest $request, ValidateAndProcessCheckinAction $action): JsonResponse
    {
        $dto = $request->toDTO((string) $request->user()->id);
        $result = $action->inspect($dto);
        $booking = $result['booking'];
        $earliestSlot = $result['earliest_slot'];

        return response()->json([
            'success' => true,
            'is_early' => $result['is_early'],
            'minutes_until_start' => $result['minutes_until_start'],
            'booking' => [
                'id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'status' => $booking->status->value,
                'status_label' => $booking->status->label(),
                'status_badge' => $booking->status->badgeClasses(),
                'event_name' => $booking->eventDetail->event_name ?? 'Acara MCC',
                'event_category' => $booking->eventDetail->event_category ?? '-',
                'institution_name' => $booking->eventDetail->institution_name_snapshot ?? ($booking->user->institution_name ?? '-'),
                'pic_name' => $booking->eventDetail->pic_name_snapshot ?? ($booking->user->full_name ?? '-'),
                'pic_phone' => $booking->eventDetail->pic_phone_snapshot ?? ($booking->user->phone_number ?? '-'),
                'room_name' => $earliestSlot->room->name ?? 'Ruangan MCC',
                'floor_level' => $earliestSlot->room->floor_level ?? 1,
                'slot_id' => $earliestSlot->id,
                'event_start_at' => $earliestSlot->event_start_at->translatedFormat('d M Y, H:i'),
                'event_end_at' => $earliestSlot->event_end_at->translatedFormat('H:i') . ' WIB',
            ],
            'warning_message' => $result['is_early']
                ? "Sesi acara baru dimulai pada {$earliestSlot->event_start_at->format('H:i')} WIB ({$result['minutes_until_start']} menit lagi). Check-in lebih awal dari batas toleransi 60 menit membutuhkan konfirmasi khusus resepsionis."
                : null,
        ]);
    }

    /**
     * Process and finalize check-in execution (FR-FO-02 & FR-FO-03).
     */
    public function processCheckin(ValidateCheckinRequest $request, ValidateAndProcessCheckinAction $action): RedirectResponse|JsonResponse
    {
        $dto = $request->toDTO((string) $request->user()->id);
        $booking = $action->execute($dto);
        $earliestSlot = $booking->slots->sortBy('event_start_at')->first();

        $message = "Check-in berhasil! Permohonan {$booking->booking_code} ({$booking->eventDetail->event_name}) telah resmi berstatus Aktif (Checked-In).";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'booking' => [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'status' => $booking->status->value,
                    'status_label' => $booking->status->label(),
                    'event_name' => $booking->eventDetail->event_name ?? 'Acara MCC',
                    'institution_name' => $booking->eventDetail->institution_name_snapshot ?? ($booking->user->institution_name ?? '-'),
                    'room_name' => $earliestSlot?->room?->name ?? 'Ruangan MCC',
                    'floor_level' => $earliestSlot?->room?->floor_level ?? 1,
                    'slot_id' => $earliestSlot?->id,
                    'event_start_at' => $earliestSlot?->event_start_at ? $earliestSlot->event_start_at->translatedFormat('d M Y, H:i') : '-',
                    'event_end_at' => $earliestSlot?->event_end_at ? $earliestSlot->event_end_at->translatedFormat('H:i') . ' WIB' : '-',
                ],
            ]);
        }

        return redirect()->route('front_office.manifest')->with('success', $message);
    }

    /**
     * Assign and dispatch Duty Officer to room session (FR-FO-04).
     */
    public function assignDutyOfficer(\App\Http\Requests\FrontOffice\AssignDutyOfficerRequest $request, \App\Actions\FrontOffice\AssignDutyOfficerAction $action): JsonResponse|RedirectResponse
    {
        $dto = $request->toDTO((string) $request->user()->id);
        $result = $action->execute($dto);

        $message = "Disposisi Berhasil! {$result['duty_officer']->full_name} resmi ditugaskan sebagai Pengawas Ruangan (DO) untuk {$result['booking']->booking_code}.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'duty_officer' => [
                    'id' => $result['duty_officer']->id,
                    'name' => $result['duty_officer']->full_name,
                    'phone' => $result['duty_officer']->phone_number,
                ],
                'booking' => $result['booking'],
            ]);
        }

        return redirect()->route('front_office.manifest')->with('success', $message);
    }
}
