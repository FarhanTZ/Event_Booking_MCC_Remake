<?php

namespace App\Services;

use App\Contracts\SlotLockServiceInterface;
use App\DataTransferObjects\Availability\LockSlotData;
use Illuminate\Support\Facades\Cache;

class SlotLockService implements SlotLockServiceInterface
{
    public const LOCK_DURATION = 900; // 15 minutes TTL

    /**
     * Acquire temporary slot lock.
     */
    public function acquireLock(LockSlotData $data): array
    {
        $slotKey = substr(trim($data->slotTime), 0, 5);
        $lockKey = "slot_lock:{$data->roomId}:{$data->date}:{$slotKey}";

        $existing = Cache::get($lockKey);
        
        // Guard Clause: already locked by current user
        if ($existing && $existing['user_id'] === $data->userId) {
            return [
                'success' => true,
                'message' => 'Slot berhasil diamankan.',
                'expires_at' => $existing['expires_at'],
            ];
        }

        // Guard Clause: locked by another applicant
        if ($existing && $existing['user_id'] !== $data->userId) {
            return [
                'success' => false,
                'message' => 'Maaf, slot waktu ini sedang diproses oleh pemohon lain. Silakan pilih slot lain.',
                'expires_at' => $existing['expires_at'],
            ];
        }

        $expiresAt = now()->addSeconds(self::LOCK_DURATION)->timestamp;
        $lockPayload = [
            'room_id' => $data->roomId,
            'date' => $data->date,
            'slot' => $slotKey,
            'user_id' => $data->userId,
            'locked_at' => now()->timestamp,
            'expires_at' => $expiresAt,
        ];

        Cache::put($lockKey, $lockPayload, self::LOCK_DURATION);

        return [
            'success' => true,
            'message' => 'Slot waktu berhasil dikunci selama 15 menit.',
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Release temporary slot lock.
     */
    public function releaseLock(LockSlotData $data): bool
    {
        $slotKey = substr(trim($data->slotTime), 0, 5);
        $lockKey = "slot_lock:{$data->roomId}:{$data->date}:{$slotKey}";

        $existing = Cache::get($lockKey);
        if (! $existing) {
            return true;
        }

        if ($existing['user_id'] === $data->userId) {
            Cache::forget($lockKey);
            return true;
        }

        return false;
    }
}
