<?php

namespace App\DataTransferObjects\Availability;

readonly class LockSlotData
{
    public function __construct(
        public int $roomId,
        public string $date,
        public string $slotTime,
        public string $userId
    ) {}

    public static function fromArray(array $data, string $userId): self
    {
        return new self(
            roomId: (int) $data['room_id'],
            date: (string) $data['date'],
            slotTime: (string) $data['slot_time'],
            userId: $userId
        );
    }
}
