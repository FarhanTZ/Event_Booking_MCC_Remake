<?php

namespace App\DataTransferObjects\Admin;

class StaffFilterData
{
    /**
     * @param string|null $search
     * @param string|null $role
     * @param string|null $status
     * @param string $sortField
     * @param string $sortDirection
     */
    public function __construct(
        public readonly ?string $search = null,
        public readonly ?string $role = null,
        public readonly ?string $status = null,
        public readonly string $sortField = 'created_at',
        public readonly string $sortDirection = 'desc'
    ) {}

    /**
     * Create filter DTO from array/request data.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            search: ! empty($data['search']) ? trim((string) $data['search']) : (! empty($data['q']) ? trim((string) $data['q']) : null),
            role: ! empty($data['role']) && $data['role'] !== 'all' ? (string) $data['role'] : null,
            status: ! empty($data['status']) && $data['status'] !== 'all' ? (string) $data['status'] : null,
            sortField: (string) ($data['sort'] ?? 'created_at'),
            sortDirection: (string) ($data['direction'] ?? 'desc')
        );
    }
}
