<?php

namespace App\Http\Requests\Admin;

use App\DataTransferObjects\Admin\ApplySanctionData;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class ApplySanctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === UserRole::SUPER_ADMIN;
    }

    public function rules(): array
    {
        return [
            'suspension_reason' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'suspension_reason.required' => 'Alasan pembekuan akun / penetapan sanksi wajib diisi secara resmi.',
            'suspension_reason.min' => 'Alasan pembekuan akun minimal 10 karakter agar tercatat jelas pada audit log.',
            'suspension_reason.max' => 'Alasan pembekuan akun maksimal 1000 karakter.',
        ];
    }

    public function toDto(string $userId): ApplySanctionData
    {
        return ApplySanctionData::fromArray($this->validated(), $userId);
    }
}
