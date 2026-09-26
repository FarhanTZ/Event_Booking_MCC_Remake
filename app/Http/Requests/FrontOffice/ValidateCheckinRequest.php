<?php

namespace App\Http\Requests\FrontOffice;

use App\DataTransferObjects\FrontOffice\ValidateCheckinData;
use Illuminate\Foundation\Http\FormRequest;

class ValidateCheckinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_code' => ['nullable', 'string', 'max:50'],
            'checkin_token' => ['nullable', 'string', 'max:100'],
            'confirm_early' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $this->filled('booking_code') && ! $this->filled('checkin_token')) {
                $validator->errors()->add('identifier', 'Wajib memasukkan Kode Booking atau memindai QR Code E-Pass.');
            }
        });
    }

    public function toDTO(string $officerId): ValidateCheckinData
    {
        return ValidateCheckinData::fromRequest($this, $officerId);
    }
}
