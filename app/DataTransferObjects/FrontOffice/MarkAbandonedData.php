<?php

namespace App\DataTransferObjects\FrontOffice;

readonly class MarkAbandonedData
{
    public function __construct(
        public string $bookingId,
        public ?int $slotId,
        public string $unclaimedBoxId,
        public ?string $notes,
        public string $markedById,
    ) {}
}
