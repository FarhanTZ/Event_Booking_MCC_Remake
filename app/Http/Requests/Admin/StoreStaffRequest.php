<?php

namespace App\Http\Requests\Admin;

use App\DataTransferObjects\Admin\StaffData;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreStaffRequest extends FormRequest
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
        return [
            'full_name' => ['required', 'string', 'min:3', 'max:150'],
            'email' => ['required', 'string', 'email:rfc', 'max:150', Rule::unique('users', 'email')],
            'phone_number' => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/', Rule::unique('users', 'phone_number')],
            'role' => ['required', 'string', Rule::in(['marketing', 'front_office', 'duty_officer', 'super_admin'])],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
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
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar di sistem MCC.',
            'phone_number.required' => 'Nomor WhatsApp / telepon staf wajib diisi.',
            'phone_number.regex' => 'Format nomor WhatsApp harus valid diawali 08/62/+62.',
            'phone_number.unique' => 'Nomor WhatsApp sudah digunakan akun staf lain.',
            'role.required' => 'Divisi pos kerja / role staf wajib dipilih.',
            'role.in' => 'Pilihan divisi tidak valid. Pilih antara Marketing, Front Office, Duty Officer, atau Super Admin.',
            'password.required' => 'Kata sandi awal akun staf wajib ditentukan.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter dengan kombinasi huruf besar, huruf kecil, dan angka.',
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
            'password' => $this->validated('password'),
            'institution_name' => $this->validated('institution_name') ?? 'Malang Creative Center (MCC)',
            'address' => $this->validated('address') ?? 'Jl. A. Yani No.53, Blimbing, Kota Malang, Jawa Timur 65126',
        ]);
    }
}
