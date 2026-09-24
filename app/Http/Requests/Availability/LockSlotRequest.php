<?php

namespace App\Http\Requests\Availability;

use App\DataTransferObjects\Availability\LockSlotData;
use Illuminate\Foundation\Http\FormRequest;

class LockSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'date' => ['required', 'date_format:Y-m-d'],
            'slot_time' => ['required', 'string', 'max:50'],
        ];
    }

    public function toDto(): LockSlotData
    {
        $userId = $this->user()?->id ?? 'guest_' . $this->session()->getId();

        return LockSlotData::fromArray($this->validated(), $userId);
    }
}
