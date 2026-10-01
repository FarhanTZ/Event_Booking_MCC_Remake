<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\RoomData;
use App\Models\Room;
use App\Models\RoomImage;
use App\Services\FileUploadSecurityService;
use App\Services\SecurityAuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateRoomAction
{
    /**
     * Update master room attributes and optional photos (FR-ADM-01).
     *
     * @param Room $room
     * @param RoomData $dto
     * @param string|null $adminId
     * @return Room
     */
    public function execute(Room $room, RoomData $dto, ?string $adminId = null): Room
    {
        return DB::transaction(function () use ($room, $dto, $adminId) {
            $room->update([
                'name' => $dto->name,
                'slug' => $dto->slug ?? $room->slug,
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

            // Save new uploaded room photos if provided
            if (! empty($dto->photos)) {
                $hasExistingPrimary = $room->images()->where('is_primary', true)->exists();
                $lastSortOrder = (int) $room->images()->max('sort_order');

                foreach ($dto->photos as $index => $photoFile) {
                    $storedPath = FileUploadSecurityService::storeImage($photoFile, 'rooms');
                    RoomImage::create([
                        'room_id' => $room->id,
                        'image_url' => $storedPath,
                        'caption' => "Foto Ruangan {$room->name}",
                        'is_primary' => ! $hasExistingPrimary && $index === 0,
                        'sort_order' => $lastSortOrder + $index + 1,
                    ]);
                }
            }

            SecurityAuditLogger::logAction(
                action: 'room.updated',
                description: "Super Admin memperbarui data master ruangan: '{$room->name}' (Lantai {$room->floor_level}).",
                userId: $adminId,
                modelType: 'Room',
                modelId: (string) $room->id,
                metadata: [
                    'name' => $room->name,
                    'floor_level' => $room->floor_level,
                    'capacity' => $room->capacity,
                    'is_active' => $room->is_active,
                ]
            );

            Log::channel('single')->info("Admin Room Updated: {$room->name} (ID: {$room->id})");

            return $room->fresh(['images', 'facilities']);
        });
    }
}
