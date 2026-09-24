<?php

namespace App\DataTransferObjects\Availability;

readonly class RoomAvailabilityQueryData
{
    public function __construct(
        public int $roomId,
        public string $date,
        public ?string $currentUserId = null
    ) {}

    public static function fromArray(array $data, ?string $currentUserId = null): self
    {
        return new self(
            roomId: (int) ($data['room_id'] ?? 1),
            date: (string) ($data['date'] ?? now()->toDateString()),
            currentUserId: $currentUserId
        );
    }
}
