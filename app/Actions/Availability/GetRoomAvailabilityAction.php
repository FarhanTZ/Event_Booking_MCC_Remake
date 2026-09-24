<?php

namespace App\Actions\Availability;

use App\Contracts\RoomAvailabilityCalculatorInterface;
use App\DataTransferObjects\Availability\RoomAvailabilityQueryData;
use App\Models\Room;

class GetRoomAvailabilityAction
{
    public function __construct(
        protected RoomAvailabilityCalculatorInterface $calculator
    ) {}

    /**
     * Execute the use case to retrieve room availability matrix.
     */
    public function execute(RoomAvailabilityQueryData $data): array
    {
        $room = Room::with(['facilities'])->findOrFail($data->roomId);
        $slots = $this->calculator->getHourlySlots($data);

        return [
            'room' => $room,
            'date' => $data->date,
            'slots' => $slots,
        ];
    }
}
