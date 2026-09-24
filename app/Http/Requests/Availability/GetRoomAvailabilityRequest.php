<?php

namespace App\Http\Requests\Availability;

use App\DataTransferObjects\Availability\GetRoomAvailabilityQueryData;
use App\DataTransferObjects\Availability\RoomAvailabilityQueryData;
use Illuminate\Foundation\Http\FormRequest;

class GetRoomAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => ['nullable', 'date_format:Y-m-d'],
        ];
    }

    public function toDto(int $roomId): RoomAvailabilityQueryData
    {
        return RoomAvailabilityQueryData::fromArray([
            'room_id' => $roomId,
            'date' => $this->query('date', now()->toDateString()),
        ], $this->user()?->id);
    }
}
