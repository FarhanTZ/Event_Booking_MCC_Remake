<?php

namespace App\DataTransferObjects\Admin;

use App\Enums\UserRole;

class StaffData
{
    /**
     * @param string $fullName
     * @param string $email
     * @param string $phoneNumber
     * @param UserRole $role
     * @param string $institutionName
     * @param string $address
     * @param string|null $password
     * @param bool $isSuspended
     * @param string|null $suspensionReason
     */
    public function __construct(
        public readonly string $fullName,
        public readonly string $email,
        public readonly string $phoneNumber,
        public readonly UserRole $role,
        public readonly string $institutionName,
        public readonly string $address,
        public readonly ?string $password = null,
        public readonly bool $isSuspended = false,
        public readonly ?string $suspensionReason = null
    ) {}

    /**
     * Create DTO from array data.
     *
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $role = $data['role'] instanceof UserRole
            ? $data['role']
            : UserRole::from((string) $data['role']);

        return new self(
            fullName: (string) ($data['full_name'] ?? ''),
            email: strtolower(trim((string) ($data['email'] ?? ''))),
            phoneNumber: (string) ($data['phone_number'] ?? ''),
            role: $role,
            institutionName: (string) ($data['institution_name'] ?? 'Malang Creative Center (MCC)'),
            address: (string) ($data['address'] ?? 'Jl. A. Yani No.53, Blimbing, Kota Malang, Jawa Timur 65126'),
            password: ! empty($data['password']) ? (string) $data['password'] : null,
            isSuspended: (bool) ($data['is_suspended'] ?? false),
            suspensionReason: ! empty($data['suspension_reason']) ? (string) $data['suspension_reason'] : null
        );
    }
}
