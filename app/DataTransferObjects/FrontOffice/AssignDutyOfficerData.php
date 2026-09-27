<?php

namespace App\DataTransferObjects\FrontOffice;

use Illuminate\Http\Request;

readonly class AssignDutyOfficerData
{
    public function __construct(
        public string $bookingId,
        public ?int $slotId,
        public string $dutyOfficerId,
        public string $assignedById,
        public ?string $notes = null
    ) {}

    public static function fromRequest(Request $request, string $assignedById): self
    {
        return new self(
            bookingId: (string) $request->input('booking_id'),
            slotId: $request->filled('slot_id') ? (int) $request->input('slot_id') : null,
            dutyOfficerId: (string) $request->input('duty_officer_id'),
            assignedById: $assignedById,
            notes: $request->filled('notes') ? trim((string) $request->input('notes')) : null
        );
    }
}
