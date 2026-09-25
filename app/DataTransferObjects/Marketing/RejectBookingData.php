<?php

namespace App\DataTransferObjects\Marketing;

readonly class RejectBookingData
{
    public function __construct(
        public string $bookingId,
        public string $rejectionCategory,
        public string $rejectionNotes,
        public string $marketingUserId,
    ) {}
}
