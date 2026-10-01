<?php

namespace App\Http\Requests\Admin;

use App\DataTransferObjects\Admin\StaffData;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === UserRole::SUPER_ADMIN;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('name') && ! $this->has('full_name')) {
            $this->merge([
                'full_name' => $this->input('name'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('staff') ? ($this->route('staff')->id ?? $this->route('staff')) : null;

        return [
            'full_name' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'string', 'email:rfc', 'max:150', Rule::unique('users', 'email')->ignore($userId)],
            'phone_number' => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/', Rule::unique('users', 'phone_number')->ignore($userId)],
            'role' => ['required', 'string', Rule::in(['marketing', 'front_office', 'duty_officer', 'super_admin'])],
            'institution_name' => ['nullable', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'full_name.required' => 'Nama lengkap staf wajib diisi.',
            'email.required' => 'Alamat email dinas wajib diisi.',
            'email.unique' => 'Alamat email sudah terdaftar untuk pengguna lain.',
            'phone_number.required' => 'Nomor WhatsApp / telepon staf wajib diisi.',
            'phone_number.unique' => 'Nomor WhatsApp sudah digunakan akun lain.',
            'role.required' => 'Divisi pos kerja staf wajib dipilih.',
            'role.in' => 'Pilihan divisi tidak valid.',
        ];
    }

    /**
     * Convert request to StaffData DTO.
     */
    public function toDto(): StaffData
    {
        return StaffData::fromArray([
            'full_name' => $this->validated('full_name'),
            'email' => $this->validated('email'),
            'phone_number' => $this->validated('phone_number'),
            'role' => $this->validated('role'),
            'institution_name' => $this->validated('institution_name') ?? 'Malang Creative Center (MCC)',
            'address' => $this->validated('address') ?? 'Jl. A. Yani No.53, Blimbing, Kota Malang, Jawa Timur 65126',
        ]);
    }
}
