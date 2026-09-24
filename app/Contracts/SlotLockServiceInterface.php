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

    /**
     * Acquire batch of slot locks atomically (All-or-Nothing).
     * If any slot fails, rolls back all locks in the batch.
     * 
     * @param array<LockSlotData> $slots
     * @param string $userId
     * @return array{success: bool, message: string, expires_at?: int, conflict_slot?: array}
     */
    public function batchAcquireLocks(array $slots, string $userId): array;

    /**
     * Release batch of slot locks.
     * 
     * @param array<LockSlotData> $slots
     * @param string $userId
     * @return bool
     */
    public function batchReleaseLocks(array $slots, string $userId): bool;
}
