<?php

namespace App\Http\Requests\FrontOffice;

use Illuminate\Foundation\Http\FormRequest;

class ReleaseKtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isFrontOffice() || $this->user()->isSuperAdmin());
    }

    public function rules(): array
    {
        return [
            'booking_id' => ['required', 'string', 'uuid', 'exists:bookings,id'],
            'slot_id' => ['nullable', 'integer', 'exists:booking_slots,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'booking_id.required' => 'ID pemesanan wajib disertakan.',
            'booking_id.exists' => 'Data pemesanan tidak ditemukan dalam sistem.',
            'slot_id.exists' => 'Slot sesi ruangan tidak valid.',
        ];
    }
}
