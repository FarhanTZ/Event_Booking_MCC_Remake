<?php

namespace App\Http\Requests\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\ConfirmAbandonedCleanData;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmAbandonedCleanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->role?->value, ['duty_officer', 'super_admin']);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string', 'max:1000'],
            'slot_id' => ['nullable', 'integer', 'exists:booking_slots,id'],
            'cleanliness_checklist' => ['nullable', 'array'],
            'inventory_checklist' => ['nullable', 'array'],
        ];
    }

    public function toDTO(string $dutyOfficerId, string $bookingId): ConfirmAbandonedCleanData
    {
        return new ConfirmAbandonedCleanData(
            dutyOfficerId: $dutyOfficerId,
            bookingId: $bookingId,
            slotId: $this->filled('slot_id') ? (int) $this->input('slot_id') : null,
            notes: $this->input('notes'),
            cleanlinessChecklist: (array) $this->input('cleanliness_checklist', []),
            inventoryChecklist: (array) $this->input('inventory_checklist', [])
        );
    }
}
