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
     * Execute the use case to create a new booking proposal.
     */
    public function execute(CreateBookingProposalData $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $room = Room::findOrFail($data->roomId);

            // 1. Upload files securely
            $proposalPath = $data->proposalFile->store('proposals', 'public');
            $proposalUrl = '/storage/' . $proposalPath;

            $posterUrl = null;
            if ($data->posterFile) {
                $posterPath = $data->posterFile->store('posters', 'public');
                $posterUrl = '/storage/' . $posterPath;
            }

            // 2. Determine pricing scheme
            $isCommercial = str_contains(strtolower($data->eventType), 'berbayar') 
                || str_contains(strtolower($data->eventType), 'komersial');
            $pricingScheme = $isCommercial ? 'commercial' : 'subsidy';
            $nominalAmount = $isCommercial ? (float) ($room->base_hourly_rate * count($data->slots)) : 0.00;

            // 3. Generate Unique Booking Code
            $datePrefix = Carbon::parse($data->date)->format('Ymd');
            $randomCode = strtoupper(Str::random(5));
            $bookingCode = "MCC-{$datePrefix}-{$randomCode}";

            // 4. Create Main Booking Record
            /** @var Booking $booking */
            $booking = Booking::query()->create([
                'id' => (string) Str::uuid(),
                'booking_code' => $bookingCode,
                'user_id' => $data->userId,
                'status' => 'awaiting_marketing',
                'clearance_status' => 'pending',
                'pricing_scheme' => $pricingScheme,
                'nominal_amount' => $nominalAmount,
                'version' => 1,
            ]);

            // 5. Calculate overall event start and end time from slots array
            $sortedSlots = collect($data->slots)->sort()->values();
            $firstSlotStart = substr(trim($sortedSlots->first()), 0, 5); // "08:00"
            $lastSlotEnd = substr(trim($sortedSlots->last()), -5);      // "10:00"

            $eventStartAt = Carbon::parse("{$data->date} {$firstSlotStart}:00", 'Asia/Jakarta');
            $eventEndAt = Carbon::parse("{$data->date} {$lastSlotEnd}:00", 'Asia/Jakarta');

            // 6. Create BookingSlot Record (PostgreSQL Trigger will automatically calculate operational_range with 30m buffer)
            BookingSlot::query()->create([
                'booking_id' => (string) $booking->id,
                'room_id' => $data->roomId,
                'event_start_at' => $eventStartAt,
                'event_end_at' => $eventEndAt,
                'is_active' => true,
            ]);

            // 7. Create EventDetail Record
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

            // 8. Release temporary slot locks from cache
            foreach ($data->slots as $slot) {
                $this->lockService->releaseLock(new LockSlotData(
                    roomId: $data->roomId,
                    date: $data->date,
                    slotTime: $slot,
                    userId: $data->userId
                ));
            }

            return $booking;
        });
    }
}
