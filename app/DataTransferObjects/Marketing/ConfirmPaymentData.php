<?php

namespace App\DataTransferObjects\Marketing;

readonly class ConfirmPaymentData
{
    public function __construct(
        public string $bookingId,
        public string $paymentReference,
        public ?string $paymentNotes,
        public string $marketingUserId,
    ) {}
}
