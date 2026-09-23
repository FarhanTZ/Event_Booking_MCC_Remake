<?php

namespace App\Http\Requests\Auth;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Tipe Akun
            'account_type' => ['required', 'string', new Enum(AccountType::class)],

            // Identitas Personal & Kontak PIC
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email:rfc,filter', 'max:150', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:25', 'unique:users,phone_number'],
            
            // Password
            'password' => ['required', 'string', 'confirmed', Password::min(8)],

            // Identitas Lembaga / Komunitas / Sekolah
            'institution_name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:1000'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // Maks 2MB

            // Khusus Tipe Akun "Instansi"
            'website' => ['nullable', 'string', 'url', 'max:255'],
            'category' => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('account_type') === AccountType::INSTANSI->value),
                'string',
                'max:100',
            ],
            'sub_sector' => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('account_type') === AccountType::INSTANSI->value),
                'string',
                'max:100',
            ],
            'city' => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('account_type') === AccountType::INSTANSI->value),
                'string',
                'max:100',
            ],
            'district' => [
                'nullable',
                Rule::requiredIf(fn () => $this->input('account_type') === AccountType::INSTANSI->value),
                'string',
                'max:100',
            ],

            // Social Media (Optional URLs)
            'facebook' => ['nullable', 'string', 'max:255'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'twitter' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'youtube' => ['nullable', 'string', 'max:255'],

            // Persyaratan KTP & Ketentuan Layanan
            'terms' => ['accepted'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'account_type' => 'Tipe Akun',
            'full_name' => 'Nama Lengkap PIC',
            'email' => 'Alamat Email',
            'phone_number' => 'Nomor Telepon',
            'password' => 'Kata Sandi',
            'password_confirmation' => 'Konfirmasi Kata Sandi',
            'institution_name' => 'Nama Lembaga / Komunitas / Sekolah',
            'address' => 'Alamat',
            'description' => 'Deskripsi',
            'logo' => 'Logo / Foto',
            'website' => 'Website',
            'category' => 'Kategori',
            'sub_sector' => 'Sub-Sektor',
            'city' => 'Kota',
            'district' => 'Kecamatan',
            'terms' => 'Persetujuan Ketentuan Layanan & SOP',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem MCC.',
            'phone_number.unique' => 'Nomor telepon ini sudah terdaftar di sistem MCC.',
            'logo.max' => 'Ukuran file gambar tidak boleh melebihi 2MB.',
            'logo.mimes' => 'Format gambar harus berupa file JPG, JPEG, atau PNG.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui SOP Fasilitas MCC dan kesediaan menitipkan KTP asli.',
        ];
    }
}
