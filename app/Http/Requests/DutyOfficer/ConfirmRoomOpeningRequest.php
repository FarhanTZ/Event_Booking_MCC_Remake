<?php

namespace App\Http\Requests\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\ConfirmRoomOpeningData;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmRoomOpeningRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot_id' => ['nullable', 'integer', 'exists:booking_slots,id'],
            'facility_checklist' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function toDTO(string $dutyOfficerId, string $bookingId): ConfirmRoomOpeningData
    {
        return ConfirmRoomOpeningData::fromRequest($this, $dutyOfficerId, $bookingId);
    }
}
