<?php

namespace App\Actions\Availability;

use App\Contracts\SlotLockServiceInterface;
use App\DataTransferObjects\Availability\LockSlotData;

class LockRoomSlotAction
{
    public function __construct(
        protected SlotLockServiceInterface $lockService
    ) {}

    /**
     * Execute the use case to lock a room slot for 15 minutes.
     */
    public function execute(LockSlotData $data): array
    {
        return $this->lockService->acquireLock($data);
    }
}
