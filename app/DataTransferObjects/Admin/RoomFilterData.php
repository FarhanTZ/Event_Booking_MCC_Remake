<?php

namespace App\DataTransferObjects\Admin;

class RoomFilterData
{
    /**
     * @param string|null $search
     * @param int|null $floor
     * @param string|null $status // 'all', 'active', 'inactive', 'commercial'
     * @param string $sortField
     * @param string $sortDirection
     */
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?int $floor = null,
        public readonly ?string $status = 'all',
        public readonly string $sortField = 'floor_level',
        public readonly string $sortDirection = 'asc',
    ) {}
}
