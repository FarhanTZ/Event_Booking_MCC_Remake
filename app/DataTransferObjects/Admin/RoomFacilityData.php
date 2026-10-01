<?php

namespace App\DataTransferObjects\Admin;

class RoomFacilityData
{
    /**
     * @param int $roomId
     * @param string $facilityName
     * @param int $quantity
     * @param string $unit
     * @param string $condition // 'good', 'fair', 'needs_repair'
     */
    public function __construct(
        public readonly int $roomId,
        public readonly string $facilityName,
        public readonly int $quantity,
        public readonly string $unit,
        public readonly string $condition = 'good',
    ) {}
}
