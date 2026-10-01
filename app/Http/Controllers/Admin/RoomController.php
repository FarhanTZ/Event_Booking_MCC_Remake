<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateRoomAction;
use App\Actions\Admin\DeleteRoomAction;
use App\Actions\Admin\GetAdminRoomsAction;
use App\Actions\Admin\ToggleRoomStatusAction;
use App\Actions\Admin\UpdateRoomAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoomFilterRequest;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\ToggleRoomStatusRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display listing of rooms and facilities (FR-ADM-01).
     */
    public function index(RoomFilterRequest $request, GetAdminRoomsAction $action): View|JsonResponse
    {
        $dto = $request->toDTO();
        $data = $action->execute($dto);

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        return view('admin.rooms.index', $data);
    }

    /**
     * Show create room form.
     */
    public function create(): View
    {
        return view('admin.rooms.create');
    }

    /**
     * Store newly created room.
     */
    public function store(StoreRoomRequest $request, CreateRoomAction $action): RedirectResponse|JsonResponse
    {
        $adminId = (string) $request->user()?->id;
        $dto = $request->toDTO();
        $room = $action->execute($dto, $adminId);

        $message = "Master ruangan '{$room->name}' (Lantai {$room->floor_level}) berhasil dibuat!";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'room' => $room,
            ], 201);
        }

        return redirect()->route('admin.rooms.show', $room)->with('success', $message);
    }

    /**
     * Display detailed room view and facility inventory manager (FR-ADM-01, FR-ADM-02).
     */
    public function show(Room $room): View|JsonResponse
    {
        $room->load(['facilities', 'images', 'primaryImage']);

        if (request()->wantsJson()) {
            return response()->json([
                'room' => $room,
                'facilities' => $room->facilities,
                'images' => $room->images,
            ]);
        }

        return view('admin.rooms.show', compact('room'));
    }

    /**
     * Show edit room form.
     */
    public function edit(Room $room): View
    {
        $room->load(['facilities', 'images']);
        return view('admin.rooms.edit', compact('room'));
    }

    /**
     * Update room details.
     */
    public function update(Room $room, UpdateRoomRequest $request, UpdateRoomAction $action): RedirectResponse|JsonResponse
    {
        $adminId = (string) $request->user()?->id;
        $dto = $request->toDTO();
        $updatedRoom = $action->execute($room, $dto, $adminId);

        $message = "Data master ruangan '{$updatedRoom->name}' berhasil diperbarui!";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'room' => $updatedRoom,
            ]);
        }

        return redirect()->route('admin.rooms.show', $updatedRoom)->with('success', $message);
    }

    /**
     * Delete room if safe.
     */
    public function destroy(Room $room, DeleteRoomAction $action): RedirectResponse|JsonResponse
    {
        $adminId = (string) auth()->id();
        $name = $room->name;
        $action->execute($room, $adminId);

        $message = "Ruangan '{$name}' berhasil dihapus dari sistem.";

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.rooms.index')->with('success', $message);
    }

    /**
     * Toggle room operational active / maintenance status.
     */
    public function toggleStatus(Room $room, ToggleRoomStatusRequest $request, ToggleRoomStatusAction $action): RedirectResponse|JsonResponse
    {
        $adminId = (string) $request->user()?->id;
        $forceStatus = $request->has('is_active') ? $request->boolean('is_active') : null;
        $toggledRoom = $action->execute($room, $forceStatus, $adminId);

        $statusLabel = $toggledRoom->is_active ? 'Aktif (Tersedia)' : 'Nonaktif (Maintenance)';
        $message = "Status ruangan '{$toggledRoom->name}' berhasil diubah menjadi {$statusLabel}.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => $toggledRoom->is_active,
                'room' => $toggledRoom,
            ]);
        }

        return back()->with('success', $message);
    }
}
