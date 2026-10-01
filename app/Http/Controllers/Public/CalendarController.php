<?php

namespace App\Http\Controllers\Public;

use App\Actions\Availability\BatchLockSlotsAction;
use App\Actions\Availability\BatchUnlockSlotsAction;
use App\Actions\Availability\GetRoomAvailabilityAction;
use App\Actions\Availability\LockRoomSlotAction;
use App\Actions\Availability\UnlockRoomSlotAction;
use App\DataTransferObjects\Availability\RoomAvailabilityQueryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Availability\BatchLockSlotRequest;
use App\Http\Requests\Availability\BatchUnlockSlotRequest;
use App\Http\Requests\Availability\GetRoomAvailabilityRequest;
use App\Http\Requests\Availability\LockSlotRequest;
use App\Http\Requests\Availability\UnlockSlotRequest;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    /**
     * Display the real-time slot calendar page.
     */
    public function index(Request $request, GetRoomAvailabilityAction $action): View
    {
        $rooms = Room::with(['facilities'])
            ->where('is_active', true)
            ->orderBy('floor_level')
            ->orderBy('name')
            ->get();

        $selectedRoomId = (int) $request->query('room_id', $rooms->first()?->id ?? 1);
        $selectedDate = (string) $request->query('date', now()->toDateString());

        $queryData = RoomAvailabilityQueryData::fromArray([
            'room_id' => $selectedRoomId,
            'date' => $selectedDate,
        ], auth()->id());

        $result = $action->execute($queryData);

        return view('public.calendar', [
            'rooms' => $rooms,
            'selectedRoomId' => $selectedRoomId,
            'selectedRoom' => $result['room'],
            'selectedDate' => $selectedDate,
            'initialSlots' => $result['slots'],
        ]);
    }

    /**
     * API endpoint to get real-time availability slots as JSON.
     */
    public function availability(
        GetRoomAvailabilityRequest $request,
        int $id,
        GetRoomAvailabilityAction $action
    ): JsonResponse {
        $queryData = $request->toDto($id);
        $result = $action->execute($queryData);

        return response()->json([
            'success' => true,
            'room' => [
                'id' => $result['room']->id,
                'name' => $result['room']->name,
                'floor' => $result['room']->floor_level,
                'capacity' => $result['room']->capacity,
                'buffer_minutes' => $result['room']->buffer_minutes ?? 30,
            ],
            'date' => $result['date'],
            'slots' => $result['slots'],
        ]);
    }

    /**
     * API endpoint to acquire a single temporary 15-minute slot lock.
     */
    public function lockSlot(
        LockSlotRequest $request,
        LockRoomSlotAction $action
    ): JsonResponse {
        if (auth()->check() && auth()->user()->isSuspended()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda sedang dibekukan / masuk daftar sanksi (Blacklist) MCC: ' . (auth()->user()->suspension_reason ?? 'Pelanggaran aturan gedung.') . '. Pemilihan jadwal diblokir.',
            ], 403);
        }

        $result = $action->execute($request->toDto());

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * API endpoint to release a single temporary slot lock upon cancellation.
     */
    public function unlockSlot(
        UnlockSlotRequest $request,
        UnlockRoomSlotAction $action
    ): JsonResponse {
        $success = $action->execute($request->toDto());

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Kunci slot berhasil dilepas.' : 'Gagal melepas kunci slot.',
        ]);
    }

    /**
     * API endpoint for atomic multi-slot batch locking (REQ-USR-07.B).
     */
    public function batchLock(
        BatchLockSlotRequest $request,
        BatchLockSlotsAction $action
    ): JsonResponse {
        if (auth()->check() && auth()->user()->isSuspended()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda sedang dibekukan / masuk daftar sanksi (Blacklist) MCC: ' . (auth()->user()->suspension_reason ?? 'Pelanggaran aturan gedung.') . '. Pemilihan jadwal diblokir.',
            ], 403);
        }

        $userId = auth()->id() ? (string) auth()->id() : 'guest_' . session()->getId();
        $slotDtos = $request->toSlotDtos($userId);
        $result = $action->execute($slotDtos, $userId);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * API endpoint for releasing atomic multi-slot batch locks.
     */
    public function batchUnlock(
        BatchUnlockSlotRequest $request,
        BatchUnlockSlotsAction $action
    ): JsonResponse {
        $userId = auth()->id() ? (string) auth()->id() : 'guest_' . session()->getId();
        $slotDtos = $request->toSlotDtos($userId);
        $success = $action->execute($slotDtos, $userId);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Seluruh kunci slot berhasil dilepas.' : 'Gagal melepas kunci slot.',
        ]);
    }
}
