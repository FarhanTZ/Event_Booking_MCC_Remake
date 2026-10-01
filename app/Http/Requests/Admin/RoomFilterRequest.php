<?php

namespace App\Http\Requests\Admin;

use App\DataTransferObjects\Admin\RoomFilterData;
use Illuminate\Foundation\Http\FormRequest;

class RoomFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role?->value === 'super_admin';
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'floor' => ['nullable', 'integer', 'between:1,8'],
            'status' => ['nullable', 'string', 'in:all,active,inactive,commercial'],
            'sort_field' => ['nullable', 'string', 'in:name,floor_level,capacity,base_hourly_rate,created_at'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }

    public function toDTO(): RoomFilterData
    {
        return new RoomFilterData(
            search: $this->filled('search') ? trim((string) $this->input('search')) : null,
            floor: $this->filled('floor') ? (int) $this->input('floor') : null,
            status: $this->input('status', 'all'),
            sortField: $this->input('sort_field', 'floor_level'),
            sortDirection: $this->input('sort_direction', 'asc')
        );
    }
}
