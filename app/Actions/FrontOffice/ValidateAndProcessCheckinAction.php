<?php

namespace App\Actions\FrontOffice;

use App\DataTransferObjects\FrontOffice\ValidateCheckinData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\SecurityAuditLogger;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ValidateAndProcessCheckinAction
{
    public function __construct(
        private SecurityAuditLogger $auditLogger
    ) {}

    /**
     * Inspect and validate booking check-in conditions (Preview/Verification).
     *
     * @param ValidateCheckinData $dto
     * @return array{booking: Booking, earliest_slot: \App\Models\BookingSlot, is_early: bool, minutes_until_start: int}
     * @throws ValidationException
     */
    public function inspect(ValidateCheckinData $dto): array
    {
        $booking = $this->findBooking($dto);

        // 1. Validasi Status Lifecycle: Hanya boleh berstatus Confirmed
        if ($booking->status === BookingStatus::CHECKED_IN) {
            throw ValidationException::withMessages([
                'checkin' => "Permohonan {$booking->booking_code} sudah berstatus Checked-In pada " . ($booking->checked_in_at ? $booking->checked_in_at->translatedFormat('d M Y, H:i') . ' WIB' : 'hari ini') . ".",
            ]);
        }

        if ($booking->status === BookingStatus::COMPLETED) {
            throw ValidationException::withMessages([
                'checkin' => "Permohonan {$booking->booking_code} sudah selesai digunakan (Completed).",
            ]);
        }

        if ($booking->status !== BookingStatus::CONFIRMED) {
            throw ValidationException::withMessages([
                'checkin' => "Permohonan {$booking->booking_code} belum dapat di-check-in karena berstatus: " . $booking->status->label() . ". Hanya permohonan berstatus Disetujui (Confirmed) yang dapat melakukan check-in.",
            ]);
        }

        // 2. Validasi Tanggal Acara: Menolak check-in jika tanggal sesi bukan hari ini (CURRENT_DATE)
        $today = Carbon::today();
        $todaySlots = $booking->slots->filter(function ($slot) use ($today) {
            return Carbon::parse($slot->event_start_at)->isSameDay($today) && $slot->is_active;
        });

        if ($todaySlots->isEmpty()) {
            $earliestSlot = $booking->slots->sortBy('event_start_at')->first();
            $scheduledDateStr = $earliestSlot ? Carbon::parse($earliestSlot->event_start_at)->translatedFormat('l, d F Y') : '-';
            
            throw ValidationException::withMessages([
                'checkin' => "Check-in ditolak. Tanggal pelaksanaan acara ({$scheduledDateStr}) bukan hari ini (" . $today->translatedFormat('l, d F Y') . ").",
            ]);
        }

        // 3. Evaluasi Toleransi Waktu (Maksimal 60 menit sebelum sesi dimulai)
        $earliestTodaySlot = $todaySlots->sortBy('event_start_at')->first();
        $startTime = Carbon::parse($earliestTodaySlot->event_start_at);
        $now = Carbon::now();

        $minutesUntilStart = (int) $now->diffInMinutes($startTime, false); // Positif jika sekarang sebelum start, negatif jika sudah lewat start

        // Jika check-in dilakukan lebih dari 60 menit sebelum sesi dimulai
        $isEarly = $minutesUntilStart > 60;

        return [
            'booking' => $booking,
            'earliest_slot' => $earliestTodaySlot,
            'is_early' => $isEarly,
            'minutes_until_start' => $minutesUntilStart,
        ];
    }

    /**
     * Process and commit check-in execution into database.
     *
     * @param ValidateCheckinData $dto
     * @return Booking
     * @throws ValidationException
     */
    public function execute(ValidateCheckinData $dto): Booking
    {
        $inspection = $this->inspect($dto);
        $booking = $inspection['booking'];

        // Jika datang lebih awal (>60 menit) dan belum mengonfirmasi peringatan toleransi awal
        if ($inspection['is_early'] && ! $dto->isEarlyToleranceConfirmed) {
            throw ValidationException::withMessages([
                'early_warning' => "Sesi acara baru dimulai pada {$inspection['earliest_slot']->event_start_at->format('H:i')} WIB ({$inspection['minutes_until_start']} menit lagi). Check-in lebih awal dari batas toleransi 60 menit membutuhkan konfirmasi khusus resepsionis.",
                'requires_early_confirmation' => true,
                'booking_code' => $booking->booking_code,
            ]);
        }

        // Commit update status to CHECKED_IN
        $booking->update([
            'status' => BookingStatus::CHECKED_IN,
            'checked_in_at' => Carbon::now(),
            'checked_in_by' => $dto->officerId,
        ]);

        \Illuminate\Support\Facades\Log::channel('single')->info("Security Audit [FRONT_OFFICE_CHECKIN]: Booking {$booking->booking_code} validated by officer {$dto->officerId}.", [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'officer_id' => $dto->officerId,
            'method' => $dto->checkinToken ? 'qr_scanner' : 'manual_input',
            'checked_in_at' => Carbon::now()->toIso8601String(),
            'is_early' => $inspection['is_early'],
            'minutes_until_start' => $inspection['minutes_until_start'],
        ]);

        return $booking->fresh(['user', 'eventDetail', 'slots.room']);
    }

    /**
     * Locate booking entity via Token or Code.
     *
     * @param ValidateCheckinData $dto
     * @return Booking
     * @throws ValidationException
     */
    private function findBooking(ValidateCheckinData $dto): Booking
    {
        $query = Booking::query()->with(['user', 'eventDetail', 'slots.room']);

        if ($dto->checkinToken) {
            $booking = $query->where('checkin_token', $dto->checkinToken)->first();
            if (! $booking) {
                throw ValidationException::withMessages([
                    'checkin' => 'QR Code E-Pass tidak valid atau tidak dikenali dalam sistem MCC.',
                ]);
            }
            return $booking;
        }

        if ($dto->bookingCode) {
            $code = strtoupper(trim($dto->bookingCode));
            $booking = $query->where('booking_code', $code)->first();
            if (! $booking) {
                throw ValidationException::withMessages([
                    'checkin' => "Kode Booking \"{$dto->bookingCode}\" tidak ditemukan dalam database.",
                ]);
            }
            return $booking;
        }

        throw ValidationException::withMessages([
            'checkin' => 'Mohon berikan Kode Booking atau Token QR E-Pass yang valid.',
        ]);
    }
}
