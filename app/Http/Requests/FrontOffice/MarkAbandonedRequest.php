<?php

namespace App\Http\Requests\FrontOffice;

use App\DataTransferObjects\FrontOffice\MarkAbandonedData;
use Illuminate\Foundation\Http\FormRequest;

class MarkAbandonedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isFrontOffice() || $this->user()->isSuperAdmin());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'string', 'exists:bookings,id'],
            'slot_id' => ['nullable', 'integer', 'exists:booking_slots,id'],
            'unclaimed_box_id' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'booking_id.required' => 'Identitas booking wajib disertakan.',
            'booking_id.exists' => 'Data booking tidak ditemukan dalam sistem.',
            'unclaimed_box_id.required' => 'Nomor kotak arsip KTP tertinggal (Unclaimed Box ID) wajib diisi.',
            'unclaimed_box_id.max' => 'Nomor kotak arsip maksimal 50 karakter.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ];
    }

    /**
     * Transform validated request data to strongly typed DTO.
     */
    public function toDTO(string $userId): MarkAbandonedData
    {
        return new MarkAbandonedData(
            bookingId: (string) $this->input('booking_id'),
            slotId: $this->filled('slot_id') ? (int) $this->input('slot_id') : null,
            unclaimedBoxId: trim((string) $this->input('unclaimed_box_id')),
            notes: $this->filled('notes') ? trim((string) $this->input('notes')) : null,
            markedById: $userId
        );
    }
}
