<?php

namespace App\Services;

use App\Contracts\SlotLockServiceInterface;
use App\DataTransferObjects\Availability\LockSlotData;
use App\Models\BookingSlot;
use App\Models\Room;
use Carbon\Carbon;
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

        // Check if already booked in database
        if ($this->isSlotBookedInDb($data->roomId, $data->date, $slotKey)) {
            return [
                'success' => false,
                'message' => 'Maaf, slot waktu ini sudah dipesan pada database sistem.',
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

    /**
     * Acquire batch of slot locks atomically (All-or-Nothing / REQ-USR-07.B).
     * If any slot fails, rolls back all locks in the batch.
     * 
     * @param array<LockSlotData> $slots
     * @param string $userId
     * @return array{success: bool, message: string, expires_at?: int, conflict_slot?: array}
     */
    public function batchAcquireLocks(array $slots, string $userId): array
    {
        if (empty($slots)) {
            return [
                'success' => false,
                'message' => 'Keranjang jadwal kosong. Silakan pilih setidaknya satu slot.',
            ];
        }

        $acquiredKeys = [];
        $expiresAt = now()->addSeconds(self::LOCK_DURATION)->timestamp;

        foreach ($slots as $item) {
            $slotKey = substr(trim($item->slotTime), 0, 5);
            $lockKey = "slot_lock:{$item->roomId}:{$item->date}:{$slotKey}";

            $existing = Cache::get($lockKey);

            // Conflict 1: Locked by another user
            if ($existing && $existing['user_id'] !== $userId) {
                // Rollback any locks acquired during this batch
                $this->rollbackAcquiredKeys($acquiredKeys);

                $room = Room::find($item->roomId);
                $roomName = $room ? $room->name : "Ruangan ID {$item->roomId}";

                return [
                    'success' => false,
                    'conflict_slot' => [
                        'room_id' => $item->roomId,
                        'room_name' => $roomName,
                        'date' => $item->date,
                        'slot_time' => $item->slotTime,
                    ],
                    'message' => "Slot {$item->slotTime} pada tanggal {$item->date} untuk {$roomName} baru saja dikunci oleh pemohon lain. Seluruh penguncian dibatalkan secara aman.",
                ];
            }

            // Conflict 2: Already booked in database
            if ($this->isSlotBookedInDb($item->roomId, $item->date, $slotKey)) {
                $this->rollbackAcquiredKeys($acquiredKeys);

                $room = Room::find($item->roomId);
                $roomName = $room ? $room->name : "Ruangan ID {$item->roomId}";

                return [
                    'success' => false,
                    'conflict_slot' => [
                        'room_id' => $item->roomId,
                        'room_name' => $roomName,
                        'date' => $item->date,
                        'slot_time' => $item->slotTime,
                    ],
                    'message' => "Slot {$item->slotTime} pada tanggal {$item->date} untuk {$roomName} telah dipesan sebelumnya. Seluruh penguncian dibatalkan secara aman.",
                ];
            }

            // Put lock into payload list
            $lockPayload = [
                'room_id' => $item->roomId,
                'date' => $item->date,
                'slot' => $slotKey,
                'user_id' => $userId,
                'locked_at' => now()->timestamp,
                'expires_at' => $expiresAt,
            ];

            Cache::put($lockKey, $lockPayload, self::LOCK_DURATION);
            $acquiredKeys[] = $lockKey;
        }

        return [
            'success' => true,
            'message' => 'Seluruh ' . count($slots) . ' jadwal berhasil dikunci secara atomik selama 15 menit.',
            'expires_at' => $expiresAt,
        ];
    }

    /**
     * Release batch of slot locks.
     * 
     * @param array<LockSlotData> $slots
     * @param string $userId
     * @return bool
     */
    public function batchReleaseLocks(array $slots, string $userId): bool
    {
        foreach ($slots as $item) {
            $this->releaseLock($item);
        }

        return true;
    }

    /**
     * Helper to rollback acquired keys.
     */
    protected function rollbackAcquiredKeys(array $keys): void
    {
        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Check if a specific slot is booked in the database.
     */
    protected function isSlotBookedInDb(int $roomId, string $date, string $slotHour): bool
    {
        $slotStart = Carbon::parse("{$date} {$slotHour}:00", 'Asia/Jakarta');
        $slotEnd = $slotStart->copy()->addHour();

        return BookingSlot::query()
            ->where('room_id', $roomId)
            ->where('is_active', true)
            ->where(function ($query) use ($slotStart, $slotEnd) {
                $query->where(function ($q) use ($slotStart, $slotEnd) {
                    $q->where('event_start_at', '<', $slotEnd)
                      ->where('event_end_at', '>', $slotStart);
                });
            })
            ->exists();
    }
}
