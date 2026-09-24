<?php

namespace App\Contracts;

use App\DataTransferObjects\Availability\RoomAvailabilityQueryData;

interface RoomAvailabilityCalculatorInterface
{
    /**
     * Calculate and return hourly availability slots.
     */
    public function getHourlySlots(RoomAvailabilityQueryData $queryData): array;
}
