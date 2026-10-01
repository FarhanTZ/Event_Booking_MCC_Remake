<?php

namespace App\Http\Requests\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\ResolveRoomIncidentData;
use Illuminate\Foundation\Http\FormRequest;

class ResolveRoomIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'incident_id' => ['nullable', 'integer', 'exists:room_incidents,id'],
            'slot_id' => ['nullable', 'integer', 'exists:booking_slots,id'],
            'resolution_notes' => ['required', 'string', 'min:5', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'resolution_notes.required' => 'Catatan penyelesaian (Resolution Notes) wajib diisi.',
            'resolution_notes.min' => 'Catatan penyelesaian minimal 5 karakter.',
        ];
    }

    public function toDTO(string $dutyOfficerId, string $bookingId): ResolveRoomIncidentData
    {
        return ResolveRoomIncidentData::fromRequest($this, $dutyOfficerId, $bookingId);
    }
}
