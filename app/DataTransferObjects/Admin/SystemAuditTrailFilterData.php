<?php

namespace App\DataTransferObjects\Admin;

readonly class SystemAuditTrailFilterData
{
    public function __construct(
        public ?string $search = null,
        public string $type = 'all', // all, curation, vault, incident, override
        public ?string $dateFrom = null,
        public ?string $dateTo = null,
        public ?string $actorId = null,
        public ?string $actorRole = null,
        public int $perPage = 20
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: ! empty($data['search']) ? trim((string) $data['search']) : null,
            type: ! empty($data['type']) ? (string) $data['type'] : 'all',
            dateFrom: ! empty($data['date_from']) ? (string) $data['date_from'] : null,
            dateTo: ! empty($data['date_to']) ? (string) $data['date_to'] : null,
            actorId: ! empty($data['actor_id']) ? (string) $data['actor_id'] : null,
            actorRole: ! empty($data['actor_role']) && $data['actor_role'] !== 'all' ? (string) $data['actor_role'] : null,
            perPage: ! empty($data['per_page']) ? (int) $data['per_page'] : 20
        );
    }
}
