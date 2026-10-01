<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\RoomFacilityData;
use App\Models\Room;
use App\Models\RoomFacility;
use App\Services\SecurityAuditLogger;

class SaveRoomFacilityAction
{
    /**
     * Create or update master room facility asset (FR-ADM-02).
     *
     * @param RoomFacilityData $dto
     * @param RoomFacility|null $facility
     * @param string|null $adminId
     * @return RoomFacility
     */
    public function execute(RoomFacilityData $dto, ?RoomFacility $facility = null, ?string $adminId = null): RoomFacility
    {
        $room = Room::findOrFail($dto->roomId);

        if ($facility && $facility->exists) {
            $facility->update([
                'facility_name' => $dto->facilityName,
                'quantity' => $dto->quantity,
                'unit' => $dto->unit,
                'condition' => $dto->condition,
            ]);
            $actionName = 'room_facility.updated';
            $desc = "Super Admin memperbarui inventaris fasilitas '{$dto->facilityName}' pada ruangan {$room->name}.";
        } else {
            $facility = RoomFacility::create([
                'room_id' => $room->id,
                'facility_name' => $dto->facilityName,
                'quantity' => $dto->quantity,
                'unit' => $dto->unit,
                'condition' => $dto->condition,
            ]);
            $actionName = 'room_facility.created';
            $desc = "Super Admin menambahkan inventaris fasilitas '{$dto->facilityName}' pada ruangan {$room->name}.";
        }

        SecurityAuditLogger::logAction(
            action: $actionName,
            description: $desc,
            userId: $adminId,
            modelType: 'RoomFacility',
            modelId: (string) $facility->id,
            metadata: [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'facility_name' => $facility->facility_name,
                'quantity' => $facility->quantity,
                'unit' => $facility->unit,
                'condition' => $facility->condition,
            ]
        );

        return $facility;
    }
}
