<?php

namespace App\Http\Requests\Availability;

use App\DataTransferObjects\Availability\LockSlotData;
use Illuminate\Foundation\Http\FormRequest;

class BatchUnlockSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'slots' => ['required', 'array', 'min:1'],
            'slots.*.room_id' => ['required', 'integer'],
            'slots.*.date' => ['required', 'string'],
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
