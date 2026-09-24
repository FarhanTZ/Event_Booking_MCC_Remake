<?php

namespace App\Actions\Availability;

use App\Contracts\SlotLockServiceInterface;
use App\DataTransferObjects\Availability\LockSlotData;

class BatchLockSlotsAction
{
    public function __construct(
        protected SlotLockServiceInterface $lockService
    ) {}

    /**
     * Execute atomic batch locking for multiple slots.
     * 
     * @param array<LockSlotData> $slots
     * @param string $userId
     * @return array
     */
    public function execute(array $slots, string $userId): array
    {
        return $this->lockService->batchAcquireLocks($slots, $userId);
    }
}
