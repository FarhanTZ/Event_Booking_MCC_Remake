<?php

namespace App\DataTransferObjects\Marketing;

readonly class ApproveBookingData
{
    public function __construct(
        public string $bookingId,
        public string $pricingScheme, // 'subsidy' | 'commercial'
        public float $nominalAmount,
        public ?string $paymentNotes,
        public ?string $approvalNotes,
        public string $marketingUserId,
    ) {}

    public function isSubsidy(): bool
    {
        return $this->pricingScheme === 'subsidy';
    }

    public function isCommercial(): bool
    {
        return $this->pricingScheme === 'commercial';
    }
}
