<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\DeleteRoomFacilityAction;
use App\Actions\Admin\SaveRoomFacilityAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomFacilityRequest;
use App\Http\Requests\Admin\UpdateRoomFacilityRequest;
use App\Models\Room;
use App\Models\RoomFacility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class RoomFacilityController extends Controller
{
    /**
     * Store new facility asset for room (FR-ADM-02).
     */
    public function store(
        Room $room,
        StoreRoomFacilityRequest $request,
        SaveRoomFacilityAction $action
    ): RedirectResponse|JsonResponse {
        $adminId = (string) $request->user()?->id;
        $dto = $request->toDTO($room->id);
        $facility = $action->execute($dto, null, $adminId);

        $message = "Fasilitas '{$facility->facility_name}' ({$facility->quantity} {$facility->unit}) berhasil ditambahkan ke inventaris {$room->name}.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'facility' => $facility,
            ], 201);
        }

        return redirect()->route('admin.rooms.show', $room)->with('success', $message);
    }

    /**
     * Update existing facility asset.
     */
    public function update(
        Room $room,
        RoomFacility $facility,
        UpdateRoomFacilityRequest $request,
        SaveRoomFacilityAction $action
    ): RedirectResponse|JsonResponse {
        $adminId = (string) $request->user()?->id;
        $dto = $request->toDTO($room->id);
        $updatedFacility = $action->execute($dto, $facility, $adminId);

        $message = "Inventaris fasilitas '{$updatedFacility->facility_name}' berhasil diperbarui.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'facility' => $updatedFacility,
            ]);
        }

        return redirect()->route('admin.rooms.show', $room)->with('success', $message);
    }

    /**
     * Delete facility asset from room inventory.
     */
    public function destroy(
        Room $room,
        RoomFacility $facility,
        DeleteRoomFacilityAction $action
    ): RedirectResponse|JsonResponse {
        $adminId = (string) auth()->id();
        $facilityName = $facility->facility_name;
        $action->execute($facility, $adminId);

        $message = "Fasilitas '{$facilityName}' berhasil dihapus dari inventaris {$room->name}.";

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()->route('admin.rooms.show', $room)->with('success', $message);
    }
}
