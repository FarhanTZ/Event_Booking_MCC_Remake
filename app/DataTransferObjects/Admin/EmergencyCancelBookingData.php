<?php

namespace App\DataTransferObjects\Admin;

readonly class EmergencyCancelBookingData
{
    public function __construct(
        public string $bookingId,
        public string $adminId,
        public string $cancellationReason,
        public string $emergencyCategory
    ) {}

    public static function fromArray(array $data, string $bookingId, string $adminId): self
    {
        return new self(
            bookingId: $bookingId,
            adminId: $adminId,
            cancellationReason: trim((string) ($data['cancellation_reason'] ?? '')),
            emergencyCategory: (string) ($data['emergency_category'] ?? 'agenda_pemerintah_darurat')
        );
    }
}
