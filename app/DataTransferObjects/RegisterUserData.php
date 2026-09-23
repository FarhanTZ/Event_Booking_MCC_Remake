<?php

namespace App\DataTransferObjects;

use App\Enums\AccountType;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\UploadedFile;

readonly class RegisterUserData
{
    public function __construct(
        public AccountType $accountType,
        public string $fullName,
        public string $email,
        public string $phoneNumber,
        public string $password,
        public string $institutionName,
        public string $address,
        public ?string $description = null,
        public ?UploadedFile $logoFile = null,
        public ?string $website = null,
        public ?string $category = null,
        public ?string $subSector = null,
        public ?string $city = null,
        public ?string $district = null,
        public array $socialMedia = [],
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        $accountType = AccountType::from($request->validated('account_type'));

        // Normalize phone number to standard format
        $phone = $request->validated('phone_number');
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (str_starts_with($phone, '08')) {
            $phone = '+628' . substr($phone, 2);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '+628' . substr($phone, 1);
        }

        // Parse social media dictionary
        $socialMedia = array_filter([
            'facebook' => $request->validated('facebook'),
            'instagram' => $request->validated('instagram'),
            'twitter' => $request->validated('twitter'),
            'tiktok' => $request->validated('tiktok'),
            'youtube' => $request->validated('youtube'),
        ], fn ($val) => ! empty($val));

        return new self(
            accountType: $accountType,
            fullName: trim($request->validated('full_name')),
            email: strtolower(trim($request->validated('email'))),
            phoneNumber: $phone,
            password: $request->validated('password'),
            institutionName: trim($request->validated('institution_name')),
            address: trim($request->validated('address')),
            description: $request->filled('description') ? trim($request->validated('description')) : null,
            logoFile: $request->file('logo'),
            website: $accountType === AccountType::INSTANSI ? $request->validated('website') : null,
            category: $accountType === AccountType::INSTANSI ? $request->validated('category') : null,
            subSector: $accountType === AccountType::INSTANSI ? $request->validated('sub_sector') : null,
            city: $accountType === AccountType::INSTANSI ? $request->validated('city') : null,
            district: $accountType === AccountType::INSTANSI ? $request->validated('district') : null,
            socialMedia: $socialMedia,
        );
    }
}
