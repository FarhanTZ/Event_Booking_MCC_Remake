<?php

namespace App\DataTransferObjects\DutyOfficer;

class ConfirmAbandonedCleanData
{
    /**
     * @param string $dutyOfficerId
     * @param string $bookingId
     * @param int|null $slotId
     * @param string|null $notes
     * @param array<string, mixed> $cleanlinessChecklist
     * @param array<string, mixed> $inventoryChecklist
     */
    public function __construct(
        public readonly string $dutyOfficerId,
        public readonly string $bookingId,
        public readonly ?int $slotId = null,
        public readonly ?string $notes = null,
        public readonly array $cleanlinessChecklist = [],
        public readonly array $inventoryChecklist = []
    ) {}
}
