<?php

namespace App\DataTransferObjects\Admin;

use Illuminate\Http\UploadedFile;

class RoomData
{
    /**
     * @param string $name
     * @param string|null $slug
     * @param int $floorLevel
     * @param int $capacity
     * @param float $roomSizeSqm
     * @param int $bufferMinutes
     * @param bool $isCommercialAllowed
     * @param float $baseHourlyRate
     * @param bool $isActive
     * @param string $description
     * @param array<int, string> $supportedLayouts
     * @param array<int, UploadedFile> $photos
     * @param string|null $primaryPhotoUrl
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $slug,
        public readonly int $floorLevel,
        public readonly int $capacity,
        public readonly float $roomSizeSqm,
        public readonly int $bufferMinutes,
        public readonly bool $isCommercialAllowed,
        public readonly float $baseHourlyRate,
        public readonly bool $isActive,
        public readonly string $description,
        public readonly array $supportedLayouts,
        public readonly array $photos = [],
        public readonly ?string $primaryPhotoUrl = null,
    ) {}
}
