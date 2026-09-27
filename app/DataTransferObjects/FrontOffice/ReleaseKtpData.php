<?php

namespace App\DataTransferObjects\FrontOffice;

class ReleaseKtpData
{
    public function __construct(
        public readonly string $bookingId,
        public readonly ?int $slotId,
        public readonly string $releasedById,
        public readonly ?string $notes = null,
    ) {}
}
