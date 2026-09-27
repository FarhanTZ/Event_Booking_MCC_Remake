<?php

namespace App\Http\Requests\FrontOffice;

use App\DataTransferObjects\FrontOffice\AssignDutyOfficerData;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignDutyOfficerRequest extends FormRequest
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
            'duty_officer_id' => [
                'required',
                'string',
                'uuid',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('role', UserRole::DUTY_OFFICER->value);
                }),
            ],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'booking_id' => 'ID Permohonan Booking',
            'slot_id' => 'Sesi Ruangan',
            'duty_officer_id' => 'Petugas Pengawas Ruangan (Duty Officer)',
            'notes' => 'Catatan Disposisi',
        ];
    }

    public function messages(): array
    {
        return [
            'duty_officer_id.required' => 'Wajib memilih nama staf Duty Officer penanggung jawab ruangan.',
            'duty_officer_id.exists' => 'Staf yang dipilih tidak terdaftar sebagai Duty Officer aktif.',
        ];
    }

    public function toDTO(string $assignedById): AssignDutyOfficerData
    {
        return AssignDutyOfficerData::fromRequest($this, $assignedById);
    }
}
