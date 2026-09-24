<?php

namespace App\Contracts;

use App\DataTransferObjects\Availability\LockSlotData;

interface SlotLockServiceInterface
{
    /**
     * Acquire a temporary slot lock.
     */
    public function acquireLock(LockSlotData $data): array;

    /**
     * Release a temporary slot lock.
     */
    public function releaseLock(LockSlotData $data): bool;
}
