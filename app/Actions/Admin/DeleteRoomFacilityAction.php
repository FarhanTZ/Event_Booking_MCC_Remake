<?php

namespace App\Actions\Admin;

use App\Models\RoomFacility;
use App\Services\SecurityAuditLogger;

class DeleteRoomFacilityAction
{
    /**
     * Delete room facility asset from inventory (FR-ADM-02).
     *
     * @param RoomFacility $facility
     * @param string|null $adminId
     * @return bool
     */
    public function execute(RoomFacility $facility, ?string $adminId = null): bool
    {
        $facilityName = $facility->facility_name;
        $facilityId = $facility->id;
        $room = $facility->room;

        SecurityAuditLogger::logAction(
            action: 'room_facility.deleted',
            description: "Super Admin menghapus inventaris fasilitas '{$facilityName}' dari ruangan {$room?->name}.",
            userId: $adminId,
            modelType: 'RoomFacility',
            modelId: (string) $facilityId,
            metadata: [
                'room_id' => $facility->room_id,
                'facility_name' => $facilityName,
            ]
        );

        return (bool) $facility->delete();
    }
}
