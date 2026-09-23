<?php

namespace App\Actions\Auth;

use App\DataTransferObjects\RegisterUserData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RegisterUserAction
{
    /**
     * Execute user registration transaction.
     */
    public function execute(RegisterUserData $data): User
    {
        return DB::transaction(function () use ($data) {
            $logoPath = null;

            // Handle logo / photo upload to storage with security sanitization
            if ($data->logoFile) {
                $logoPath = \App\Services\FileUploadSecurityService::storeImage($data->logoFile, 'logos');
            }

            return User::create([
                'role' => UserRole::USER,
                'account_type' => $data->accountType,
                'full_name' => $data->fullName,
                'email' => $data->email,
                'phone_number' => $data->phoneNumber,
                'password_hash' => $data->password, // automatically hashed by cast
                'institution_name' => $data->institutionName,
                'logo_url' => $logoPath,
                'address' => $data->address,
                'description' => $data->description,
                'social_media' => $data->socialMedia,
                'website' => $data->website,
                'category' => $data->category,
                'sub_sector' => $data->subSector,
                'city' => $data->city,
                'district' => $data->district,
                'is_suspended' => false,
            ]);
        });
    }
}
