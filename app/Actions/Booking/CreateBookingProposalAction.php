<?php

namespace App\Actions\Booking;

use App\Contracts\SlotLockServiceInterface;
use App\DataTransferObjects\Availability\LockSlotData;
use App\DataTransferObjects\Booking\CreateBookingProposalData;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\EventDetail;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateBookingProposalAction
{
    public function __construct(
        protected SlotLockServiceInterface $lockService
    ) {}

    /**
     * Execute the use case to create a new booking proposal with multi-session support.
     */
    public function execute(CreateBookingProposalData $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $sessions = $data->getSessions();

            // 1. Upload files securely
            $proposalPath = $data->proposalFile->store('proposals', 'public');
            $proposalUrl = '/storage/' . $proposalPath;

            $posterUrl = null;
            if ($data->posterFile) {
                $posterPath = $data->posterFile->store('posters', 'public');
                $posterUrl = '/storage/' . $posterPath;
            }

            // 2. Determine pricing scheme and total nominal amount across all sessions
            $isCommercial = str_contains(strtolower($data->eventType), 'berbayar') 
                || str_contains(strtolower($data->eventType), 'komersial');
            $pricingScheme = $isCommercial ? 'commercial' : 'subsidy';

            $totalNominalAmount = 0.00;
            if ($isCommercial) {
                foreach ($sessions as $session) {
                    $room = Room::find($session['room_id']);
                    $hourlyRate = $room ? (float) $room->base_hourly_rate : 0.00;
                    $totalNominalAmount += ($hourlyRate * count($session['slots']));
                }
            }

            // 3. Generate Unique Booking Code
            $firstDate = $sessions[0]['date'] ?? $data->date;
            $datePrefix = Carbon::parse($firstDate)->format('Ymd');
            $randomCode = strtoupper(Str::random(5));
            $bookingCode = "MCC-{$datePrefix}-{$randomCode}";

            // 4. Create Main Booking Record (Single booking transaction)
            /** @var Booking $booking */
            $booking = Booking::query()->create([
                'id' => (string) Str::uuid(),
                'booking_code' => $bookingCode,
                'user_id' => $data->userId,
                'status' => 'awaiting_marketing',
                'clearance_status' => 'pending',
                'pricing_scheme' => $pricingScheme,
                'nominal_amount' => $totalNominalAmount,
                'version' => 1,
            ]);

            // 5. Create BookingSlot Records for all sessions
            $allLockSlotsToRelease = [];

            foreach ($sessions as $session) {
                $roomId = (int) $session['room_id'];
                $sessionDate = (string) $session['date'];
                $sessionSlots = (array) $session['slots'];

                // Sort slots to find overall start and end time of this session
                $sortedSlots = collect($sessionSlots)->sort()->values();
                if ($sortedSlots->isNotEmpty()) {
                    $firstSlotStart = substr(trim($sortedSlots->first()), 0, 5); // e.g. "08:00"
                    $lastSlotEnd = substr(trim($sortedSlots->last()), -5);       // e.g. "10:00"

                    $eventStartAt = Carbon::parse("{$sessionDate} {$firstSlotStart}:00", 'Asia/Jakarta');
                    $eventEndAt = Carbon::parse("{$sessionDate} {$lastSlotEnd}:00", 'Asia/Jakarta');

                    BookingSlot::query()->create([
                        'booking_id' => (string) $booking->id,
                        'room_id' => $roomId,
                        'event_start_at' => $eventStartAt,
                        'event_end_at' => $eventEndAt,
                        'is_active' => true,
                    ]);
                }

                foreach ($sessionSlots as $slot) {
                    $allLockSlotsToRelease[] = new LockSlotData(
                        roomId: $roomId,
                        date: $sessionDate,
                        slotTime: $slot,
                        userId: $data->userId
                    );
                }
            }

            // 6. Create EventDetail Record
            EventDetail::query()->create([
                'booking_id' => (string) $booking->id,
                'event_name' => $data->eventName,
                'event_category' => $data->eventCategory,
                'ekraf_subsector' => $data->ekrafSubsector,
                'event_type' => $data->eventType,
                'estimated_participants' => $data->estimatedParticipants,
                'event_description' => $data->eventDescription,
                'equipment_needed' => $data->equipmentNeeded,
                'requested_facilities' => $data->requestedFacilities,
                'proposal_file_url' => $proposalUrl,
                'official_letter_number' => $data->officialLetterNumber,
                'poster_image_url' => $posterUrl,
                'pic_name_snapshot' => $data->picName,
                'pic_phone_snapshot' => $data->picPhone,
                'pic_emergency_phone' => $data->picEmergencyPhone,
                'institution_name_snapshot' => $data->institutionName,
                'tos_agreed_at' => now(),
            ]);

            // 7. Release temporary slot locks from cache
            $this->lockService->batchReleaseLocks($allLockSlotsToRelease, $data->userId);

            return $booking;
        });
    }
}
