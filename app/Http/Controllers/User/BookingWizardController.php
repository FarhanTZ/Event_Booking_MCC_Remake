<?php

namespace App\Http\Controllers\User;

use App\Actions\Booking\CreateBookingProposalAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingProposalRequest;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingWizardController extends Controller
{
    /**
     * Render the booking wizard form supporting single-slot and multi-session baskets.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->user() && $request->user()->isSuspended()) {
            return redirect()->route('dashboard')->with(
                'error',
                'Akun Anda sedang dibekukan / masuk daftar sanksi (Blacklist) MCC: ' . ($request->user()->suspension_reason ?? 'Pelanggaran aturan gedung.') . '. Pengajuan reservasi tidak diizinkan.'
            );
        }

        $sessions = [];
        $rawSessions = [];

        // 1. Check if multi-session basket was provided (as JSON or array)
        if ($request->has('basket')) {
            $basketData = $request->query('basket');
            $decoded = is_string($basketData) ? json_decode($basketData, true) : $basketData;
            if (is_array($decoded) && ! empty($decoded)) {
                // Group individual slot items by roomId + date
                $grouped = [];
                foreach ($decoded as $item) {
                    $key = "{$item['room_id']}_{$item['date']}";
                    if (! isset($grouped[$key])) {
                        $grouped[$key] = [
                            'room_id' => (int) $item['room_id'],
                            'date' => (string) $item['date'],
                            'slots' => [],
                        ];
                    }
                    $slotTime = $item['slot_time'] ?? $item['slot'] ?? '';
                    if ($slotTime && ! in_array($slotTime, $grouped[$key]['slots'], true)) {
                        $grouped[$key]['slots'][] = $slotTime;
                    }
                }
                $rawSessions = array_values($grouped);
            }
        } elseif ($request->has('sessions')) {
            $rawSessions = (array) $request->query('sessions');
        }

        // 2. Fallback to traditional single room & slot query parameters
        if (empty($rawSessions)) {
            $roomId = (int) $request->query('room_id', 1);
            $date = (string) $request->query('date', now()->toDateString());
            $slotTime = (string) $request->query('slot', '08:00 - 09:00');

            $rawSessions = [
                [
                    'room_id' => $roomId,
                    'date' => $date,
                    'slots' => [$slotTime],
                ],
            ];
        }

        // 3. Eager load all rooms involved in this basket
        $roomIds = array_unique(array_column($rawSessions, 'room_id'));
        $rooms = Room::with(['facilities', 'images'])->whereIn('id', $roomIds)->get()->keyBy('id');
        $primaryRoom = $rooms->first() ?? Room::with(['facilities', 'images'])->firstOrFail();

        // 4. Compile formatted session rows for display & validation
        $totalHours = 0;
        $minCapacity = 999999;
        $totalFee = 0.00;

        foreach ($rawSessions as $raw) {
            $roomId = (int) $raw['room_id'];
            $room = $rooms->get($roomId) ?? $primaryRoom;
            $sessionSlots = (array) ($raw['slots'] ?? []);
            
            $sortedSlots = collect($sessionSlots)->sort()->values();
            $firstSlotStart = $sortedSlots->isNotEmpty() ? substr(trim($sortedSlots->first()), 0, 5) : '08:00';
            $lastSlotEnd = $sortedSlots->isNotEmpty() ? substr(trim($sortedSlots->last()), -5) : '09:00';
            $durationHours = count($sessionSlots);

            $totalHours += $durationHours;
            $minCapacity = min($minCapacity, $room->capacity);
            $totalFee += ($room->base_hourly_rate * $durationHours);

            $sessions[] = [
                'room' => $room,
                'room_id' => $room->id,
                'date' => (string) $raw['date'],
                'date_formatted' => Carbon::parse($raw['date'])->locale('id')->translatedFormat('l, d F Y'),
                'slots' => $sessionSlots,
                'start_time' => $firstSlotStart,
                'end_time' => $lastSlotEnd,
                'duration_hours' => $durationHours,
            ];
        }

        if ($minCapacity === 999999) {
            $minCapacity = $primaryRoom->capacity;
        }

        return view('user.wizard', [
            'primaryRoom' => $primaryRoom,
            'room' => $primaryRoom, // Backward-compat for single room references
            'date' => $sessions[0]['date'] ?? now()->toDateString(),
            'slotTime' => $sessions[0]['slots'][0] ?? '08:00 - 09:00',
            'sessions' => $sessions,
            'totalHours' => $totalHours,
            'minCapacity' => $minCapacity,
            'totalFee' => $totalFee,
            'user' => auth()->user(),
        ]);
    }

    /**
     * Store submitted booking proposal.
     */
    public function store(
        CreateBookingProposalRequest $request,
        CreateBookingProposalAction $action
    ): RedirectResponse {
        $booking = $action->execute($request->toDto());

        return redirect()->route('dashboard')->with(
            'success',
            "Permohonan reservasi ruangan berhasil diajukan dengan kode {$booking->booking_code}! Tim kurasi MCC akan meninjau proposal Anda."
        );
    }
}
