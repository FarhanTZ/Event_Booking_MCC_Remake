<?php

namespace App\DataTransferObjects\Admin;

readonly class ApplySanctionData
{
    public function __construct(
        public string $userId,
        public string $suspensionReason
    ) {}

    public static function fromArray(array $data, string $userId): self
    {
        return new self(
            userId: $userId,
            suspensionReason: trim((string) ($data['suspension_reason'] ?? ''))
        );
    }
}
