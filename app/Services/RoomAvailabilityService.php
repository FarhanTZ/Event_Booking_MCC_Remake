<?php

namespace App\Services;

use App\Contracts\RoomAvailabilityCalculatorInterface;
use App\DataTransferObjects\Availability\RoomAvailabilityQueryData;
use App\Models\BookingSlot;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class RoomAvailabilityService implements RoomAvailabilityCalculatorInterface
{
    protected const OPERATIONAL_START_HOUR = 8;
    protected const OPERATIONAL_END_HOUR = 22;

    /**
     * Calculate and return hourly availability slots based on DTO.
     */
    public function getHourlySlots(RoomAvailabilityQueryData $queryData): array
    {
        $room = Room::findOrFail($queryData->roomId);

        // Guard Clause: Inactive room / under technical maintenance
        if (! $room->is_active) {
            return $this->generateMaintenanceSlots($queryData->date);
        }

        $bufferMinutes = $room->buffer_minutes ?? 30;
        $bookedSlots = $this->fetchActiveBookedSlots($queryData->roomId, $queryData->date);

        $slots = [];
        for ($hour = self::OPERATIONAL_START_HOUR; $hour < self::OPERATIONAL_END_HOUR; $hour++) {
            $slots[] = $this->buildHourlySlot($hour, $queryData, $bookedSlots, $bufferMinutes);
        }

        return $slots;
    }

    /**
     * Fetch all active booking slots on the specified date with eager loaded relations.
     */
    protected function fetchActiveBookedSlots(int $roomId, string $date): Collection
    {
        $startOfDay = Carbon::parse($date, 'Asia/Jakarta')->startOfDay();
        $endOfDay = Carbon::parse($date, 'Asia/Jakarta')->endOfDay();

        return BookingSlot::with(['booking.eventDetail', 'booking.user'])
            ->where('room_id', $roomId)
            ->where('is_active', true)
            ->whereHas('booking', function ($query) {
                $query->whereNotIn('status', ['cancelled', 'rejected', 'draft']);
            })
            ->where(function ($query) use ($startOfDay, $endOfDay) {
                $query->whereBetween('event_start_at', [$startOfDay, $endOfDay])
                    ->orWhereBetween('event_end_at', [$startOfDay, $endOfDay])
                    ->orWhere(function ($q) use ($startOfDay, $endOfDay) {
                        $q->where('event_start_at', '<=', $startOfDay)
                          ->where('event_end_at', '>=', $endOfDay);
                    });
            })
            ->get();
    }

    /**
     * Build slot evaluation matrix for a specific hour block.
     */
    protected function buildHourlySlot(
        int $hour,
        RoomAvailabilityQueryData $queryData,
        Collection $bookedSlots,
        int $bufferMinutes
    ): array {
        $slotStart = Carbon::parse($queryData->date, 'Asia/Jakarta')->setTime($hour, 0, 0);
        $slotEnd = Carbon::parse($queryData->date, 'Asia/Jakarta')->setTime($hour + 1, 0, 0);
        $timeLabel = $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i');
        $slotKey = $slotStart->format('H:i');

        // 1. Check overlap with database booking
        $matchedBooking = $this->findMatchingBooking($slotStart, $slotEnd, $bookedSlots);
        if ($matchedBooking) {
            $eventName = $matchedBooking->booking?->eventDetail?->event_name 
                ?? $matchedBooking->booking?->booking_code 
                ?? 'Kegiatan Komunitas/Instansi';

            return [
                'time' => $timeLabel,
                'start_time' => $slotStart->format('H:i:s'),
                'end_time' => $slotEnd->format('H:i:s'),
                'status' => 'booked',
                'label' => 'Terisi: ' . $eventName,
                'event_name' => $eventName,
                'is_selectable' => false,
            ];
        }

        // 2. Check overlap with buffer time
        $matchedBuffer = $this->findMatchingBuffer($slotStart, $slotEnd, $bookedSlots, $bufferMinutes);
        if ($matchedBuffer) {
            return [
                'time' => $timeLabel,
                'start_time' => $slotStart->format('H:i:s'),
                'end_time' => $slotEnd->format('H:i:s'),
                'status' => 'buffer',
                'label' => 'Buffer Time (' . $bufferMinutes . 'm Pembersihan & Reset)',
                'is_selectable' => false,
            ];
        }

        // 3. Check temporary cache/Redis lock (15 minutes TTL)
        $lockKey = "slot_lock:{$queryData->roomId}:{$queryData->date}:{$slotKey}";
        $lockData = Cache::get($lockKey);
        if ($lockData) {
            $isMine = ($queryData->currentUserId && isset($lockData['user_id']) && $lockData['user_id'] === $queryData->currentUserId);
            $remainingSeconds = max(0, ($lockData['expires_at'] ?? now()->timestamp) - now()->timestamp);

            return [
                'time' => $timeLabel,
                'start_time' => $slotStart->format('H:i:s'),
                'end_time' => $slotEnd->format('H:i:s'),
                'status' => 'locked',
                'holder' => $isMine ? 'Sedang Anda Isi (Tersimpan)' : 'Sedang diproses pemohon lain (Terkunci 15m)',
                'is_mine' => $isMine,
                'remaining_seconds' => $remainingSeconds,
                'is_selectable' => $isMine,
            ];
        }

        // 4. Default: Slot is available
        return [
            'time' => $timeLabel,
            'start_time' => $slotStart->format('H:i:s'),
            'end_time' => $slotEnd->format('H:i:s'),
            'status' => 'available',
            'label' => 'Slot Kosong & Siap Dipesan',
            'is_selectable' => true,
        ];
    }

    protected function findMatchingBooking(Carbon $slotStart, Carbon $slotEnd, Collection $bookedSlots): ?BookingSlot
    {
        foreach ($bookedSlots as $bs) {
            $eventStart = Carbon::parse($bs->event_start_at, 'Asia/Jakarta');
            $eventEnd = Carbon::parse($bs->event_end_at, 'Asia/Jakarta');

            if ($slotStart < $eventEnd && $slotEnd > $eventStart) {
                return $bs;
            }
        }
        return null;
    }

    protected function findMatchingBuffer(Carbon $slotStart, Carbon $slotEnd, Collection $bookedSlots, int $bufferMinutes): ?BookingSlot
    {
        foreach ($bookedSlots as $bs) {
            $eventEnd = Carbon::parse($bs->event_end_at, 'Asia/Jakarta');
            $bufferEnd = (clone $eventEnd)->addMinutes($bufferMinutes);

            if ($slotStart < $bufferEnd && $slotEnd > $eventEnd) {
                return $bs;
            }
        }
        return null;
    }

    protected function generateMaintenanceSlots(string $date): array
    {
        $slots = [];
        for ($hour = self::OPERATIONAL_START_HOUR; $hour < self::OPERATIONAL_END_HOUR; $hour++) {
            $slotStart = Carbon::parse($date, 'Asia/Jakarta')->setTime($hour, 0, 0);
            $slotEnd = Carbon::parse($date, 'Asia/Jakarta')->setTime($hour + 1, 0, 0);

            $slots[] = [
                'time' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                'start_time' => $slotStart->format('H:i:s'),
                'end_time' => $slotEnd->format('H:i:s'),
                'status' => 'maintenance',
                'label' => 'Pemeliharaan Teknis / Tidak Aktif',
                'is_selectable' => false,
            ];
        }
        return $slots;
    }
}
