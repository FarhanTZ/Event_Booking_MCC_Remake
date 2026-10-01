<?php

namespace App\DataTransferObjects\DutyOfficer;

use Illuminate\Http\Request;

readonly class SubmitRoomClearanceData
{
    /**
     * @param string $bookingId
     * @param string $dutyOfficerId
     * @param int|null $slotId
     * @param array<string, mixed> $cleanlinessChecklist
     * @param array<string, mixed> $inventoryChecklist
     * @param string|null $notes
     */
    public function __construct(
        public string $bookingId,
        public string $dutyOfficerId,
        public ?int $slotId = null,
        public array $cleanlinessChecklist = [],
        public array $inventoryChecklist = [],
        public ?string $notes = null,
    ) {}

    public static function fromRequest(Request $request, string $dutyOfficerId, string $bookingId): self
    {
        return new self(
            bookingId: $bookingId,
            dutyOfficerId: $dutyOfficerId,
            slotId: $request->filled('slot_id') ? (int) $request->input('slot_id') : null,
            cleanlinessChecklist: (array) $request->input('cleanliness_checklist', []),
            inventoryChecklist: (array) $request->input('inventory_checklist', []),
            notes: $request->input('notes'),
        );
    }
}
