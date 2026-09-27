<?php

namespace App\DataTransferObjects\DutyOfficer;

use Illuminate\Http\Request;

readonly class ConfirmRoomOpeningData
{
    /**
     * @param array<string, bool> $facilityChecklist
     */
    public function __construct(
        public string $bookingId,
        public ?int $slotId,
        public string $dutyOfficerId,
        public array $facilityChecklist = [],
        public ?string $notes = null
    ) {}

    public static function fromRequest(Request $request, string $dutyOfficerId, string $bookingId): self
    {
        return new self(
            bookingId: $bookingId,
            slotId: $request->filled('slot_id') ? (int) $request->input('slot_id') : null,
            dutyOfficerId: $dutyOfficerId,
            facilityChecklist: (array) $request->input('facility_checklist', [
                'electricity' => true,
                'ac' => true,
                'projector' => true,
                'furniture_layout' => true,
                'cleanliness' => true,
            ]),
            notes: $request->filled('notes') ? trim((string) $request->input('notes')) : null
        );
    }
}
