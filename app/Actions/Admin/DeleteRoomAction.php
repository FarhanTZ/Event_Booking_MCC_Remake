<?php

namespace App\Actions\Admin;

use App\Enums\BookingStatus;
use App\Models\BookingSlot;
use App\Models\Room;
use App\Services\SecurityAuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteRoomAction
{
    /**
     * Delete room if no active/upcoming bookings exist, or throw validation error (FR-ADM-01).
     *
     * @param Room $room
     * @param string|null $adminId
     * @return bool
     * @throws ValidationException
     */
    public function execute(Room $room, ?string $adminId = null): bool
    {
        $hasActiveBookings = BookingSlot::where('room_id', $room->id)
            ->whereHas('booking', function ($query) {
                $query->whereIn('status', [
                    BookingStatus::CONFIRMED->value,
                    BookingStatus::CHECKED_IN->value,
                    BookingStatus::AWAITING_PAYMENT->value,
                    BookingStatus::AWAITING_MARKETING->value,
                    BookingStatus::REVISED->value,
                ]);
            })
            ->where('event_end_at', '>=', Carbon::now())
            ->exists();

        if ($hasActiveBookings) {
            throw ValidationException::withMessages([
                'room' => "Ruangan '{$room->name}' tidak dapat dihapus permanen karena masih memiliki agenda peminjaman aktif di masa mendatang. Silakan nonaktifkan status ruangan (Maintenance) sebagai gantinya.",
            ]);
        }

        return DB::transaction(function () use ($room, $adminId) {
            $roomName = $room->name;
            $roomId = $room->id;

            SecurityAuditLogger::logAction(
                action: 'room.deleted',
                description: "Super Admin menghapus data master ruangan '{$roomName}' (ID: {$roomId}).",
                userId: $adminId,
                modelType: 'Room',
                modelId: (string) $roomId,
                metadata: [
                    'name' => $roomName,
                    'floor_level' => $room->floor_level,
                ]
            );

            return (bool) $room->delete();
        });
    }
}
