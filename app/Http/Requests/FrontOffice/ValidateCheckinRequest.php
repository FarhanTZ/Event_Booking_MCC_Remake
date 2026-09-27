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
        $isProcess = $this->routeIs('front_office.checkin.process');

        return [
            'booking_code' => ['nullable', 'string', 'max:255'],
            'checkin_token' => ['nullable', 'string', 'max:1000'],
            'confirm_early' => ['nullable', 'boolean'],

            // Wajib saat finalisasi checkin proses (FR-FO-03)
            'ktp_holder_name' => $isProcess ? ['required', 'string', 'min:3', 'max:150'] : ['nullable', 'string', 'max:150'],
            'pic_whatsapp_number' => $isProcess ? ['required', 'string', 'min:9', 'max:25'] : ['nullable', 'string', 'max:25'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'ktp_holder_name' => 'Nama Pemilik KTP',
            'pic_whatsapp_number' => 'Nomor WhatsApp PIC Hari-H',
            'booking_code' => 'Kode Booking',
            'checkin_token' => 'Token QR Code',
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
