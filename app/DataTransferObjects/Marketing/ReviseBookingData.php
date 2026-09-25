<?php

namespace App\DataTransferObjects\Marketing;

readonly class ReviseBookingData
{
    public function __construct(
        public string $bookingId,
        public string $revisionNotes,
        public string $marketingUserId,
        public int $gracePeriodHours = 24,
    ) {}
}
