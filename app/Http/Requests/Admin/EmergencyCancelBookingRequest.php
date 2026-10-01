<?php

namespace App\Http\Requests\Admin;

use App\DataTransferObjects\Admin\EmergencyCancelBookingData;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmergencyCancelBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === UserRole::SUPER_ADMIN;
    }

    public function rules(): array
    {
        return [
            'emergency_category' => [
                'required',
                'string',
                Rule::in([
                    'agenda_pemerintah_darurat',
                    'kerusakan_teknis_gedung',
                    'kondisi_kahar_force_majeure',
                    'pelanggaran_berat_keamanan',
                    'lainnya',
                ]),
            ],
            'cancellation_reason' => ['required', 'string', 'min:15', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'emergency_category.required' => 'Kategori alasan pembatalan darurat wajib dipilih.',
            'emergency_category.in' => 'Kategori alasan pembatalan darurat tidak valid.',
            'cancellation_reason.required' => 'Catatan alasan pembatalan darurat wajib diisi untuk pencatatan audit log permanen.',
            'cancellation_reason.min' => 'Catatan alasan pembatalan minimal 15 karakter agar terdokumentasi jelas.',
            'cancellation_reason.max' => 'Catatan alasan pembatalan maksimal 1000 karakter.',
        ];
    }

    public function toDto(string $bookingId, string $adminId): EmergencyCancelBookingData
    {
        return EmergencyCancelBookingData::fromArray($this->validated(), $bookingId, $adminId);
    }
}
