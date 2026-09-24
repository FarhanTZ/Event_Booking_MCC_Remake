<?php

namespace App\Http\Requests\Availability;

use App\DataTransferObjects\Availability\LockSlotData;
use Illuminate\Foundation\Http\FormRequest;

class BatchLockSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.room_id' => ['required', 'integer', 'exists:rooms,id'],
            'slots.*.date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'slots.*.slot_time' => ['required', 'string'],
        ];
    }

    /**
     * Convert payload into array of LockSlotData DTOs.
     * 
     * @return array<LockSlotData>
     */
    public function toSlotDtos(string $userId): array
    {
        $dtos = [];
        foreach ($this->validated('slots') as $item) {
            $dtos[] = new LockSlotData(
                roomId: (int) $item['room_id'],
                date: (string) $item['date'],
                slotTime: (string) $item['slot_time'],
                userId: $userId
            );
        }

        return $dtos;
    }
}
