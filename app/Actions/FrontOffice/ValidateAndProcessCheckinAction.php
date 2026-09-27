<?php

namespace App\Actions\FrontOffice;

use App\DataTransferObjects\FrontOffice\ValidateCheckinData;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingSlot;
use App\Models\KtpVaultLog;
use App\Services\SecurityAuditLogger;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * Process and commit check-in execution into database (FR-FO-02 & FR-FO-03).
     *
     * @param ValidateCheckinData $dto
     * @return Booking
     * @throws ValidationException
     */
    public function execute(ValidateCheckinData $dto): Booking
    {
        $inspection = $this->inspect($dto);
        $booking = $inspection['booking'];

        // 1. Validasi konfirmasi toleransi kedatangan awal jika >60 menit
        if ($inspection['is_early'] && ! $dto->isEarlyToleranceConfirmed) {
            throw ValidationException::withMessages([
                'early_warning' => "Sesi acara baru dimulai pada {$inspection['earliest_slot']->event_start_at->format('H:i')} WIB ({$inspection['minutes_until_start']} menit lagi). Check-in lebih awal dari batas toleransi 60 menit membutuhkan konfirmasi khusus resepsionis.",
                'requires_early_confirmation' => true,
                'booking_code' => $booking->booking_code,
            ]);
        }

        // 2. Validasi kelengkapan KTP Jaminan Fisik (FR-FO-03: Nama KTP & WhatsApp Hari-H)
        if (empty($dto->ktpHolderName) || empty($dto->picWhatsappNumber)) {
            throw ValidationException::withMessages([
                'ktp_holder_name' => empty($dto->ktpHolderName) ? ['Nama pemilik KTP fisik penyerah wajib diisi.'] : [],
                'pic_whatsapp_number' => empty($dto->picWhatsappNumber) ? ['Nomor WhatsApp PIC lapangan hari-H wajib diisi.'] : [],
            ]);
        }

        return DB::transaction(function () use ($booking, $dto, $inspection) {
            $now = Carbon::now();

            // A. Update Status Booking menjadi Checked-In dan set clearance_status = 'pending'
            $bookingUpdateData = [
                'status' => BookingStatus::CHECKED_IN,
                'clearance_status' => 'pending',
                'checked_in_at' => $now,
                'checked_in_by' => $dto->officerId,
            ];

            if (! empty($dto->dutyOfficerId)) {
                $bookingUpdateData['duty_officer_id'] = $dto->dutyOfficerId;
            }

            $booking->update($bookingUpdateData);

            // Jika Duty Officer dipilih langsung saat check-in, simpan juga ke seluruh slot sesi
            if (! empty($dto->dutyOfficerId)) {
                BookingSlot::where('booking_id', $booking->id)->update([
                    'duty_officer_id' => $dto->dutyOfficerId,
                    'duty_officer_assigned_at' => $now,
                    'duty_officer_assigned_by' => $dto->officerId,
                    'duty_officer_notes' => $dto->dutyOfficerNotes,
                ]);
            }

            // B. Simpan Catatan Jaminan Fisik KTP (FR-FO-03) ke ktp_vault_logs
            KtpVaultLog::create([
                'booking_id' => $booking->id,
                'ktp_holder_name' => $dto->ktpHolderName,
                'pic_whatsapp_number' => $dto->picWhatsappNumber,
                'storage_drawer_number' => null, // Tidak menggunakan nomor laci
                'nik_number' => null, // Tanpa data NIK digital
                'ktp_photo_scan_url' => null, // Tanpa foto KTP digital
                'received_by_fo' => $dto->officerId,
                'retained_at' => $now,
                'notes' => $dto->notes,
            ]);

            Log::channel('single')->info("Security Audit [FRONT_OFFICE_CHECKIN]: Booking {$booking->booking_code} checked-in with physical KTP vault deposit.", [
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'officer_id' => $dto->officerId,
                'ktp_holder' => $dto->ktpHolderName,
                'pic_whatsapp' => $dto->picWhatsappNumber,
                'duty_officer_id' => $dto->dutyOfficerId,
                'checked_in_at' => $now->toIso8601String(),
                'is_early' => $inspection['is_early'],
            ]);

            return $booking->fresh(['user', 'eventDetail', 'slots.room', 'dutyOfficer', 'latestKtpVaultLog']);
        });
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

        $token = $dto->checkinToken ? trim($dto->checkinToken) : null;
        $code = $dto->bookingCode ? trim($dto->bookingCode) : null;

        // 1. Jika QR code menyimpan format JSON terstruktur (dari EPassService::buildQrPayload)
        if ($token && (str_starts_with($token, '{') && str_ends_with($token, '}'))) {
            try {
                $decodedJson = json_decode($token, true);
                if (is_array($decodedJson)) {
                    if (! empty($decodedJson['token'])) {
                        $token = $decodedJson['token'];
                    }
                    if (! empty($decodedJson['code'])) {
                        $code = $decodedJson['code'];
                    }
                }
            } catch (\Throwable $e) {
                // Abaikan jika bukan JSON valid
            }
        }

        // 2. Jika QR code memuat URL (e.g. http://localhost:8000/verify?token=XYZ atau http://localhost:8000/checkin/MCC-2026-XXXX)
        if ($token && (str_starts_with($token, 'http://') || str_starts_with($token, 'https://'))) {
            $parsedUrl = parse_url($token);
            if (! empty($parsedUrl['query'])) {
                parse_str($parsedUrl['query'], $queryParams);
                if (! empty($queryParams['token'])) {
                    $token = $queryParams['token'];
                }
                if (! empty($queryParams['code'])) {
                    $code = $queryParams['code'];
                }
            } else {
                // Check if last path segment is booking_code (MCC-...)
                $segments = explode('/', trim($parsedUrl['path'] ?? '', '/'));
                $lastSegment = end($segments);
                if ($lastSegment && str_starts_with(strtoupper($lastSegment), 'MCC-')) {
                    $code = $lastSegment;
                    $token = null;
                }
            }
        }

        // 3. Pencarian via Token QR
        if ($token && strlen($token) >= 30) {
            $booking = $query->where('checkin_token', $token)->first();
            if ($booking) {
                return $booking;
            }
        }

        // 4. Pencarian via Kode Booking (e.g. MCC-...)
        $lookupCode = $code ?: ($token && str_starts_with(strtoupper($token), 'MCC-') ? $token : null);
        if ($lookupCode) {
            $cleanedCode = strtoupper(trim($lookupCode));
            $booking = $query->where('booking_code', $cleanedCode)->first();
            if ($booking) {
                return $booking;
            }

            throw ValidationException::withMessages([
                'checkin' => "Kode Booking \"{$cleanedCode}\" tidak ditemukan dalam database.",
            ]);
        }

        // Jika ada token yang dicari tapi tidak cocok
        if ($token) {
            throw ValidationException::withMessages([
                'checkin' => 'QR Code E-Pass tidak valid atau tidak dikenali dalam sistem MCC.',
            ]);
        }

        throw ValidationException::withMessages([
            'checkin' => 'Mohon berikan Kode Booking atau Token QR E-Pass yang valid.',
        ]);
    }
}
