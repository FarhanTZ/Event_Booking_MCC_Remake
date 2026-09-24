<?php

namespace App\Actions\Availability;

use App\Contracts\SlotLockServiceInterface;
use App\DataTransferObjects\Availability\LockSlotData;

class UnlockRoomSlotAction
{
    public function __construct(
        protected SlotLockServiceInterface $lockService
    ) {}

    /**
     * Execute the use case to release a temporary slot lock.
     */
    public function execute(LockSlotData $data): bool
    {
        return $this->lockService->releaseLock($data);
    }
}
