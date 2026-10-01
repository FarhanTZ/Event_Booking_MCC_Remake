<?php

namespace App\DataTransferObjects\DutyOfficer;

use App\Enums\RoomIncidentCategory;
use Illuminate\Http\UploadedFile;

class ReportAbandonedIncidentData
{
    /**
     * @param string $dutyOfficerId
     * @param string $bookingId
     * @param RoomIncidentCategory $category
     * @param string $itemName
     * @param string $description
     * @param array<int, UploadedFile> $photos
     * @param int|null $slotId
     */
    public function __construct(
        public readonly string $dutyOfficerId,
        public readonly string $bookingId,
        public readonly RoomIncidentCategory $category,
        public readonly string $itemName,
        public readonly string $description,
        public readonly array $photos,
        public readonly ?int $slotId = null
    ) {}
}
