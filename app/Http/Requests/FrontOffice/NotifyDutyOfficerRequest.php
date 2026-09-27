<?php

namespace App\Http\Requests\FrontOffice;

use App\DataTransferObjects\FrontOffice\NotifyDutyOfficerData;
use Illuminate\Foundation\Http\FormRequest;

class NotifyDutyOfficerRequest extends FormRequest
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
            'channel' => ['nullable', 'string', 'in:whatsapp_web,whatsapp_api,webhook'],
        ];
    }

    public function attributes(): array
    {
        return [
            'booking_id' => 'ID Permohonan Booking',
            'slot_id' => 'Sesi Ruangan',
            'channel' => 'Kanal Notifikasi',
        ];
    }

    public function toDTO(string $officerId): NotifyDutyOfficerData
    {
        return NotifyDutyOfficerData::fromRequest($this, $officerId);
    }
}
