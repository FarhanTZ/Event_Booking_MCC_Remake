<?php

namespace App\DataTransferObjects\Admin;

readonly class ExecutiveAnalyticsFilterData
{
    public function __construct(
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?int $floor = null,
        public ?int $roomId = null,
        public ?string $subsector = null,
        public ?string $scheme = null // subsidy / commercial
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            dateFrom: ! empty($data['date_from']) ? (string) $data['date_from'] : null,
            dateTo: ! empty($data['date_to']) ? (string) $data['date_to'] : null,
            floor: ! empty($data['floor']) ? (int) $data['floor'] : null,
            roomId: ! empty($data['room_id']) ? (int) $data['room_id'] : null,
            subsector: ! empty($data['subsector']) && $data['subsector'] !== 'all' ? (string) $data['subsector'] : null,
            scheme: ! empty($data['scheme']) && $data['scheme'] !== 'all' ? (string) $data['scheme'] : null
        );
    }
}
