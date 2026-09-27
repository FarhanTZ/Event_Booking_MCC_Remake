<?php

namespace App\DataTransferObjects\FrontOffice;

readonly class HandoverUnclaimedKtpData
{
    public function __construct(
        public int $ktpVaultLogId,
        public string $retrievedByName,
        public ?string $retrievedByNik,
        public ?string $notes,
        public bool $unsuspendUser,
        public string $handledByFoId,
    ) {}
}
