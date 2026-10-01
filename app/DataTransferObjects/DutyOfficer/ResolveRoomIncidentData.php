<?php

namespace App\DataTransferObjects\DutyOfficer;

use Illuminate\Http\Request;

readonly class ResolveRoomIncidentData
{
    /**
     * @param string $bookingId
     * @param string $dutyOfficerId
     * @param int|null $incidentId
     * @param string $resolutionNotes
     * @param int|null $slotId
     */
    public function __construct(
        public string $bookingId,
        public string $dutyOfficerId,
        public ?int $incidentId,
        public string $resolutionNotes,
        public ?int $slotId = null,
    ) {}

    public static function fromRequest(Request $request, string $dutyOfficerId, string $bookingId): self
    {
        return new self(
            bookingId: $bookingId,
            dutyOfficerId: $dutyOfficerId,
            incidentId: $request->filled('incident_id') ? (int) $request->input('incident_id') : null,
            resolutionNotes: (string) $request->input('resolution_notes'),
            slotId: $request->filled('slot_id') ? (int) $request->input('slot_id') : null,
        );
    }
}
