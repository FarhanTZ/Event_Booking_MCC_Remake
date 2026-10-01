<?php

namespace App\Http\Requests\Admin;

use App\DataTransferObjects\Admin\RoomFacilityData;
use Illuminate\Foundation\Http\FormRequest;

class StoreRoomFacilityRequest extends FormRequest
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
            'facility_name' => ['required', 'string', 'max:100'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10000'],
            'unit' => ['required', 'string', 'max:20'],
            'condition' => ['required', 'string', 'in:good,fair,needs_repair'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'facility_name.required' => 'Nama fasilitas inventaris wajib diisi.',
            'quantity.required' => 'Jumlah unit fasilitas wajib diisi.',
            'quantity.min' => 'Jumlah minimal 1 unit.',
            'unit.required' => 'Satuan unit wajib diisi (contoh: unit, buah, set, kursi).',
            'condition.required' => 'Kondisi fisik aset wajib dipilih (Baik, Cukup, atau Perlu Perbaikan).',
        ];
    }

    public function toDTO(int $roomId): RoomFacilityData
    {
        return new RoomFacilityData(
            roomId: $roomId,
            facilityName: trim((string) $this->input('facility_name')),
            quantity: (int) $this->input('quantity', 1),
            unit: trim((string) $this->input('unit', 'unit')),
            condition: (string) $this->input('condition', 'good')
        );
    }
}
