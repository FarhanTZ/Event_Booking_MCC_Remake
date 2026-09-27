<?php

namespace App\Http\Requests\FrontOffice;

use App\DataTransferObjects\FrontOffice\InitiateCheckoutDutyOfficerData;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InitiateCheckoutDutyOfficerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'string', 'exists:bookings,id'],
            'slot_id' => ['nullable', 'integer', 'exists:booking_slots,id'],
            'assigned_checkout_do_id' => [
                'required_without:duty_officer_id',
                'nullable',
                'string',
                'uuid',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', UserRole::DUTY_OFFICER->value);
                }),
            ],
            'duty_officer_id' => [
                'nullable',
                'string',
                'uuid',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', UserRole::DUTY_OFFICER->value);
                }),
            ],
            'notes' => ['nullable', 'string', 'max:500'],
            'send_whatsapp' => ['nullable', 'boolean'],
            'channel' => ['nullable', 'string', 'in:whatsapp_web,whatsapp_api,webhook'],
        ];
    }

    public function attributes(): array
    {
        return [
            'booking_id' => 'ID Permohonan Booking',
            'slot_id' => 'Sesi Ruangan',
            'assigned_checkout_do_id' => 'Staf Duty Officer Shift Berjalan',
            'duty_officer_id' => 'Staf Duty Officer',
            'notes' => 'Catatan Khusus Inspeksi Checkout',
            'send_whatsapp' => 'Opsi Kirim Tugas WhatsApp',
            'channel' => 'Kanal Pengiriman',
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_checkout_do_id.required_without' => 'Wajib memilih nama staf Duty Officer yang aktif pada shift berjalan.',
            'assigned_checkout_do_id.exists' => 'Staf yang dipilih tidak terdaftar sebagai Duty Officer aktif.',
            'duty_officer_id.exists' => 'Staf yang dipilih tidak terdaftar sebagai Duty Officer aktif.',
        ];
    }

    public function toDTO(string $assignedById): InitiateCheckoutDutyOfficerData
    {
        return InitiateCheckoutDutyOfficerData::fromRequest($this, $assignedById);
    }
}
