<?php

namespace App\Http\Requests\DutyOfficer;

use App\DataTransferObjects\DutyOfficer\DutyOfficerDashboardFilterData;
use Illuminate\Foundation\Http\FormRequest;

class DutyOfficerFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'floor' => ['nullable', 'integer', 'min:1', 'max:8'],
            'shift' => ['nullable', 'string', 'in:morning,evening,all'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'scope' => ['nullable', 'string', 'in:assigned,all'],
            'status' => ['nullable', 'string', 'in:all,needs_opening,checked_in,upcoming,completed'],
            'search' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function toDTO(string $userId): DutyOfficerDashboardFilterData
    {
        return DutyOfficerDashboardFilterData::fromRequest($this, $userId);
    }
}
