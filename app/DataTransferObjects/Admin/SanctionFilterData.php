<?php

namespace App\DataTransferObjects\Admin;

readonly class SanctionFilterData
{
    public function __construct(
        public ?string $search = null,
        public string $status = 'all', // all, suspended, abandoned, incident
        public int $perPage = 15,
        public string $sortField = 'created_at',
        public string $sortDirection = 'desc'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: ! empty($data['search']) ? trim((string) $data['search']) : null,
            status: ! empty($data['status']) ? (string) $data['status'] : 'all',
            perPage: ! empty($data['per_page']) ? (int) $data['per_page'] : 15,
            sortField: ! empty($data['sort_by']) ? (string) $data['sort_by'] : 'created_at',
            sortDirection: ! empty($data['sort_dir']) && in_array(strtolower((string) $data['sort_dir']), ['asc', 'desc']) 
                ? strtolower((string) $data['sort_dir']) 
                : 'desc'
        );
    }
}
