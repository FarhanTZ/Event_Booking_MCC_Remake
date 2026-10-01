<?php

namespace App\Actions\Admin;

use App\Models\Room;
use App\Services\SecurityAuditLogger;

class ToggleRoomStatusAction
{
    /**
     * Toggle or explicitly set room active/maintenance status (FR-ADM-01).
     *
     * @param Room $room
     * @param bool|null $forceStatus
     * @param string|null $adminId
     * @return Room
     */
    public function execute(Room $room, ?bool $forceStatus = null, ?string $adminId = null): Room
    {
        $newStatus = $forceStatus !== null ? $forceStatus : ! $room->is_active;
        $room->is_active = $newStatus;
        $room->save();

        $statusText = $newStatus ? 'Diaktifkan (Tersedia)' : 'Dinonaktifkan (Maintenance)';

        SecurityAuditLogger::logAction(
                action: 'room.status_toggled',
            description: "Super Admin mengubah status operasional ruangan '{$room->name}' menjadi {$statusText}.",
            userId: $adminId,
            modelType: 'Room',
            modelId: (string) $room->id,
            metadata: [
                'name' => $room->name,
                'floor_level' => $room->floor_level,
                'is_active' => $newStatus,
            ]
        );

        return $room;
    }
}
