<?php

namespace App\Actions\Admin;

use App\DataTransferObjects\Admin\StaffData;
use App\Models\User;

class UpdateStaffAction
{
    /**
     * Update an internal staff user (FR-ADM-03).
     *
     * @param User $staff
     * @param StaffData $dto
     * @return User
     */
    public function execute(User $staff, StaffData $dto): User
    {
        $staff->update([
            'role' => $dto->role,
            'full_name' => $dto->fullName,
            'email' => $dto->email,
            'phone_number' => $dto->phoneNumber,
            'institution_name' => $dto->institutionName,
            'address' => $dto->address,
        ]);

        return $staff->fresh();
    }
}
