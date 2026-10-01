<?php

namespace App\DataTransferObjects\DutyOfficer;

use App\Enums\RoomIncidentCategory;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

readonly class ReportRoomIncidentData
{
    /**
     * @param string $bookingId
     * @param string $dutyOfficerId
     * @param RoomIncidentCategory $category
     * @param string $itemName
     * @param string $description
     * @param int|null $slotId
     * @param array<int, UploadedFile> $photos
     */
    public function __construct(
        public string $bookingId,
        public string $dutyOfficerId,
        public RoomIncidentCategory $category,
        public string $itemName,
        public string $description,
        public ?int $slotId = null,
        public array $photos = [],
    ) {}

    public static function fromRequest(Request $request, string $dutyOfficerId, string $bookingId): self
    {
        $rawCategory = (string) $request->input('category');
        $categoryEnum = RoomIncidentCategory::tryFrom($rawCategory) ?? RoomIncidentCategory::PROPERTY_DAMAGE;

        $photos = $request->file('photos', []);
        if ($photos instanceof UploadedFile) {
            $photos = [$photos];
        }

        return new self(
            bookingId: $bookingId,
            dutyOfficerId: $dutyOfficerId,
            category: $categoryEnum,
            itemName: (string) $request->input('item_name'),
            description: (string) $request->input('description'),
            slotId: $request->filled('slot_id') ? (int) $request->input('slot_id') : null,
            photos: is_array($photos) ? $photos : [],
        );
    }
}
