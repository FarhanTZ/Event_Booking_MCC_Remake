<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\StaffData;
use App\Enums\AccountType;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateStaffAction
{
    /**
     * Create a new internal staff user (FR-ADM-03).
     *
     * @param StaffData $dto
     * @return User
     */
    public function execute(StaffData $dto): User
    {
        return User::create([
            'role' => $dto->role,
            'account_type' => AccountType::KOMUNITAS, // Default internal
            'full_name' => $dto->fullName,
            'email' => $dto->email,
            'phone_number' => $dto->phoneNumber,
            'password_hash' => Hash::make($dto->password ?? 'Password@123'),
            'institution_name' => $dto->institutionName,
            'address' => $dto->address,
            'is_suspended' => $dto->isSuspended,
            'suspension_reason' => $dto->suspensionReason,
            'suspended_at' => $dto->isSuspended ? now() : null,
            'email_verified_at' => now(),
        ]);
    }
}
