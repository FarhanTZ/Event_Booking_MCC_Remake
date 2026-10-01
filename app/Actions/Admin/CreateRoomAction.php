<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\RoomData;
use App\Models\Room;
use App\Models\RoomImage;
use App\Services\FileUploadSecurityService;
use App\Services\SecurityAuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateRoomAction
{
    /**
     * Create master room and store associated images (FR-ADM-01).
     *
     * @param RoomData $dto
     * @param string|null $adminId
     * @return Room
     */
    public function execute(RoomData $dto, ?string $adminId = null): Room
    {
        return DB::transaction(function () use ($dto, $adminId) {
            $room = Room::create([
                'name' => $dto->name,
                'slug' => $dto->slug,
                'floor_level' => $dto->floorLevel,
                'capacity' => $dto->capacity,
                'room_size_sqm' => $dto->roomSizeSqm,
                'buffer_minutes' => $dto->bufferMinutes,
                'is_commercial_allowed' => $dto->isCommercialAllowed,
                'base_hourly_rate' => $dto->baseHourlyRate,
                'is_active' => $dto->isActive,
                'description' => $dto->description,
                'supported_layouts' => $dto->supportedLayouts,
            ]);

            // Save uploaded room photos
            $isFirst = true;
            foreach ($dto->photos as $index => $photoFile) {
                $storedPath = FileUploadSecurityService::storeImage($photoFile, 'rooms');
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_url' => $storedPath,
                    'caption' => "Foto Ruangan {$room->name}",
                    'is_primary' => $isFirst,
                    'sort_order' => $index,
                ]);
                $isFirst = false;
            }

            SecurityAuditLogger::logAction(
                action: 'room.created',
                description: "Super Admin membuat master ruangan baru: '{$room->name}' (Lantai {$room->floor_level}).",
                userId: $adminId,
                modelType: 'Room',
                modelId: (string) $room->id,
                metadata: [
                    'name' => $room->name,
                    'floor_level' => $room->floor_level,
                    'capacity' => $room->capacity,
                    'room_size_sqm' => $room->room_size_sqm,
                    'buffer_minutes' => $room->buffer_minutes,
                    'base_hourly_rate' => $room->base_hourly_rate,
                    'is_commercial_allowed' => $room->is_commercial_allowed,
                    'supported_layouts' => $room->supportedLayouts,
                ]
            );

            Log::channel('single')->info("Admin Room Created: {$room->name} (ID: {$room->id})");

            return $room->fresh(['images', 'facilities']);
        });
    }
}
