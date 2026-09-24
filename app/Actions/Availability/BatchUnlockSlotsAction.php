<?php

namespace App\Actions\Availability;

use App\Contracts\SlotLockServiceInterface;
use App\DataTransferObjects\Availability\LockSlotData;

class BatchUnlockSlotsAction
{
    public function __construct(
        protected SlotLockServiceInterface $lockService
    ) {}

    /**
     * Release batch of slot locks.
     * 
     * @param array<LockSlotData> $slots
     * @param string $userId
     * @return bool
     */
    public function execute(array $slots, string $userId): bool
    {
        return $this->lockService->batchReleaseLocks($slots, $userId);
    }
}
