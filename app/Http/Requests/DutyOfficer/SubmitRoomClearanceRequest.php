<?php

namespace App\Http\Requests\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\SubmitRoomClearanceData;
use Illuminate\Foundation\Http\FormRequest;

class SubmitRoomClearanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slot_id' => ['nullable', 'integer', 'exists:booking_slots,id'],
            'cleanliness_checklist' => ['nullable', 'array'],
            'cleanliness_checklist.trash' => ['required', 'accepted'],
            'cleanliness_checklist.tape' => ['required', 'accepted'],
            'cleanliness_checklist.posters' => ['required', 'accepted'],
            'inventory_checklist' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'cleanliness_checklist.trash.accepted' => 'Seluruh area harus dipastikan bebas dari sisa sampah konsumsi.',
            'cleanliness_checklist.tape.accepted' => 'Sisa lakban dan perekat dekorasi harus dipastikan telah dibersihkan.',
            'cleanliness_checklist.posters.accepted' => 'Poster dan spanduk dinding harus dipastikan telah dicopot.',
        ];
    }

    public function toDTO(string $dutyOfficerId, string $bookingId): SubmitRoomClearanceData
    {
        return SubmitRoomClearanceData::fromRequest($this, $dutyOfficerId, $bookingId);
    }
}
