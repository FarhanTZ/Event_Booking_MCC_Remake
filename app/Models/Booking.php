<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $booking_code
 * @property string $user_id
 * @property string $status
 * @property string $clearance_status
 * @property string $pricing_scheme
 * @property float $nominal_amount
 * @property string|null $payment_notes
 * @property \Carbon\Carbon|null $paid_at
 * @property string|null $confirmed_by
 * @property string|null $checkin_token
 * @property \Carbon\Carbon|null $checked_in_at
 * @property string|null $checked_in_by
 * @property \Carbon\Carbon|null $completed_at
 * @property string|null $completed_by
 * @property \Carbon\Carbon|null $revision_deadline
 * @property int $version
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Booking query()
 * @method static \App\Models\Booking create(array $attributes = [])
 */
class Booking extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'booking_code',
        'user_id',
        'status',
        'clearance_status',
        'pricing_scheme',
        'nominal_amount',
        'payment_notes',
        'paid_at',
        'confirmed_by',
        'checkin_token',
        'checked_in_at',
        'checked_in_by',
        'duty_officer_id',
        'do_notified_at',
        'assigned_checkout_do_id',
        'checkout_duty_officer_id',
        'checkout_do_assigned_at',
        'checkout_initiated_at',
        'checkout_do_assigned_by',
        'checkout_initiated_by',
        'checkout_do_notes',
        'checkout_do_notified_at',
        'completed_at',
        'completed_by',
        'revision_deadline',
        'version',
    ];

    protected $casts = [
        'status' => \App\Enums\BookingStatus::class,
        'paid_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'do_notified_at' => 'datetime',
        'checkout_do_assigned_at' => 'datetime',
        'checkout_initiated_at' => 'datetime',
        'checkout_do_notified_at' => 'datetime',
        'completed_at' => 'datetime',
        'revision_deadline' => 'datetime',
        'nominal_amount' => 'decimal:2',
    ];

    public function isAwaitingMarketing(): bool
    {
        return $this->status === \App\Enums\BookingStatus::AWAITING_MARKETING;
    }

    public function isAwaitingPayment(): bool
    {
        return $this->status === \App\Enums\BookingStatus::AWAITING_PAYMENT;
    }

    public function isConfirmed(): bool
    {
        return $this->status === \App\Enums\BookingStatus::CONFIRMED;
    }

    public function isRevised(): bool
    {
        return $this->status === \App\Enums\BookingStatus::REVISED;
    }

    public function isRejected(): bool
    {
        return $this->status === \App\Enums\BookingStatus::REJECTED;
    }

    /**
     * Get the earliest event session start timestamp.
     */
    public function getEarliestEventDate(): ?\Carbon\Carbon
    {
        $firstSlot = $this->slots->sortBy('event_start_at')->first();
        return $firstSlot ? \Carbon\Carbon::parse($firstSlot->event_start_at) : null;
    }

    /**
     * Determine if the booking requires Urgent Triage (<= H-3 from event execution).
     */
    public function isUrgentTriage(): bool
    {
        if ($this->status !== \App\Enums\BookingStatus::AWAITING_MARKETING) {
            return false;
        }

        $earliestDate = $this->getEarliestEventDate();
        if (! $earliestDate) {
            return false;
        }

        $daysDiff = \Carbon\Carbon::now()->startOfDay()->diffInDays($earliestDate->startOfDay(), false);
        return $daysDiff <= 3;
    }

    /**
     * Get urgency badge label and status.
     *
     * @return array{label: string, days: int, is_critical: bool}|null
     */
    public function getUrgencyBadgeInfo(): ?array
    {
        if ($this->status !== \App\Enums\BookingStatus::AWAITING_MARKETING) {
            return null;
        }

        $earliestDate = $this->getEarliestEventDate();
        if (! $earliestDate) {
            return null;
        }

        $daysDiff = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays($earliestDate->startOfDay(), false);
        if ($daysDiff > 3) {
            return null;
        }

        $daysLabel = $daysDiff <= 0 ? 'Hari-H / Lewat' : "H-{$daysDiff}";

        return [
            'label' => "Mendesak ({$daysLabel})",
            'days' => $daysDiff,
            'is_critical' => $daysDiff <= 1,
        ];
    }

    public function isCheckedIn(): bool
    {
        return $this->status === \App\Enums\BookingStatus::CHECKED_IN;
    }

    public function isCompleted(): bool
    {
        return $this->status === \App\Enums\BookingStatus::COMPLETED;
    }

    public function isAbandoned(): bool
    {
        return $this->status === \App\Enums\BookingStatus::ABANDONED_CLEAN
            || $this->status === \App\Enums\BookingStatus::ABANDONED_INCIDENT;
    }

    public function isAbandonedClean(): bool
    {
        return $this->status === \App\Enums\BookingStatus::ABANDONED_CLEAN;
    }

    public function isAbandonedIncident(): bool
    {
        return $this->status === \App\Enums\BookingStatus::ABANDONED_INCIDENT;
    }

    /**
     * Determine if session is overdue for checkout (> 30 minutes after event end while still checked-in) (FR-FO-08).
     */
    public function isOverdueForCheckout(?\Carbon\Carbon $currentTime = null): bool
    {
        if (! $this->isCheckedIn()) {
            return false;
        }

        $now = $currentTime ?? \Carbon\Carbon::now();
        $latestSlot = $this->slots->sortByDesc('event_end_at')->first();

        if (! $latestSlot || ! $latestSlot->event_end_at) {
            return false;
        }

        $toleranceTime = \Carbon\Carbon::parse($latestSlot->event_end_at)->addMinutes(30);

        return $now->greaterThan($toleranceTime);
    }

    /**
     * Determine if booking can be marked as abandoned by Front Office (FR-FO-08).
     */
    public function canMarkAbandoned(): bool
    {
        return $this->isCheckedIn();
    }

    /**
     * Determine if Front Office can initiate checkout inspection (FR-FO-06).
     * Allowed when booking is Checked-In (ongoing) or Completed (re-dispatch/re-inspection).
     */
    public function canInitiateCheckout(): bool
    {
        return $this->isCheckedIn() || $this->isCompleted();
    }

    /**
     * Check if checkout duty officer has been assigned.
     */
    public function hasCheckoutDutyOfficer(): bool
    {
        return ! empty($this->assigned_checkout_do_id) || ! empty($this->checkout_duty_officer_id);
    }

    public function isCleared(): bool
    {
        return $this->clearance_status === 'cleared';
    }

    public function hasIncident(): bool
    {
        return $this->clearance_status === 'incident_detected';
    }

    public function isIncidentResolved(): bool
    {
        return $this->clearance_status === 'resolved';
    }

    public function isClearancePending(): bool
    {
        return $this->clearance_status === 'pending' || empty($this->clearance_status);
    }

    /**
     * Determine if physical KTP can be released to applicant (FR-FO-07).
     * Allowed only when clearance is 'cleared' (green) or 'resolved' (blue), and booking is checked-in.
     */
    public function canReleaseKtp(): bool
    {
        return $this->isCheckedIn() && ($this->isCleared() || $this->isIncidentResolved());
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function slots()
    {
        return $this->hasMany(BookingSlot::class, 'booking_id');
    }

    public function eventDetail()
    {
        return $this->hasOne(EventDetail::class, 'booking_id');
    }

    public function approvals()
    {
        return $this->hasMany(BookingApproval::class, 'booking_id');
    }

    public function dutyOfficer()
    {
        return $this->belongsTo(User::class, 'duty_officer_id');
    }

    public function checkoutDutyOfficer()
    {
        return $this->belongsTo(User::class, 'assigned_checkout_do_id')
            ?? $this->belongsTo(User::class, 'checkout_duty_officer_id');
    }

    public function checkoutDoAssignedBy()
    {
        return $this->belongsTo(User::class, 'checkout_do_assigned_by')
            ?? $this->belongsTo(User::class, 'checkout_initiated_by');
    }

    public function ktpVaultLogs()
    {
        return $this->hasMany(KtpVaultLog::class, 'booking_id');
    }

    public function latestKtpVaultLog()
    {
        return $this->hasOne(KtpVaultLog::class, 'booking_id')->latestOfMany('id');
    }

    public function incidents()
    {
        return $this->hasMany(RoomIncident::class, 'booking_id');
    }

    public function latestIncident()
    {
        return $this->hasOne(RoomIncident::class, 'booking_id')->latestOfMany('id');
    }

    /**
     * Get PIC Contact Name for field operations.
     */
    public function getPicName(): string
    {
        return $this->latestKtpVaultLog?->ktp_holder_name
            ?? ($this->eventDetail?->pic_name_snapshot ?? ($this->user?->full_name ?? 'Penanggung Jawab Lapangan'));
    }

    /**
     * Get PIC Phone / WhatsApp Number for field operations.
     */
    public function getPicPhoneNumber(): string
    {
        return $this->latestKtpVaultLog?->pic_whatsapp_number
            ?? ($this->eventDetail?->pic_phone_snapshot ?? ($this->user?->phone_number ?? '-'));
    }

    /**
     * Generate direct Click-to-WhatsApp URL to communicate with field PIC.
     */
    public function getPicWhatsAppUrl(?string $dutyOfficerName = null): string
    {
        $phone = $this->getPicPhoneNumber();
        if (empty($phone) || $phone === '-') {
            return '';
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $doName = $dutyOfficerName ?? 'Duty Officer MCC';
        $picName = $this->getPicName();
        $eventName = $this->eventDetail?->event_name ?? 'Acara di MCC';
        $firstSlot = $this->slots->sortBy('event_start_at')->first();
        $roomName = $firstSlot?->room ? "{$firstSlot->room->name} (Lantai {$firstSlot->room->floor_level})" : 'Gedung MCC';

        $text = "Halo Kak {$picName}, saya {$doName} (Petugas Duty Officer MCC) yang bertugas mendampingi sesi acara \"{$eventName}\" di {$roomName}. Apakah ruangan dan fasilitas telah siap digunakan dengan baik, atau ada kebutuhan operasional yang perlu kami bantu?";
        $encodedText = urlencode($text);

        return "https://api.whatsapp.com/send?phone={$cleanPhone}&text={$encodedText}";
    }

    /**
     * Get operational remaining time and progress countdown data for active building usage.
     *
     * @return array{
     *   start_time: ?\Carbon\Carbon,
     *   end_time: ?\Carbon\Carbon,
     *   schedule_formatted: string,
     *   total_seconds: int,
     *   remaining_seconds: int,
     *   elapsed_seconds: int,
     *   progress_percent: float,
     *   is_active_now: bool,
     *   is_upcoming: bool,
     *   is_ended: bool,
     *   is_overdue: bool,
     *   status_state: string,
     *   countdown_text: string
     * }
     */
    public function getOperationalCountdownInfo(?\Carbon\Carbon $currentTime = null): array
    {
        $now = $currentTime ?? \Carbon\Carbon::now('Asia/Jakarta');
        $slotsSorted = $this->slots->sortBy('event_start_at');
        $firstSlot = $slotsSorted->first();
        $lastSlot = $this->slots->sortByDesc('event_end_at')->first();

        $start = $firstSlot ? \Carbon\Carbon::parse($firstSlot->event_start_at, 'Asia/Jakarta') : null;
        $end = $lastSlot ? \Carbon\Carbon::parse($lastSlot->event_end_at, 'Asia/Jakarta') : null;

        if (! $start || ! $end) {
            return [
                'start_time' => null,
                'end_time' => null,
                'schedule_formatted' => '-',
                'total_seconds' => 0,
                'remaining_seconds' => 0,
                'elapsed_seconds' => 0,
                'progress_percent' => 0,
                'is_active_now' => false,
                'is_upcoming' => false,
                'is_ended' => false,
                'is_overdue' => false,
                'status_state' => 'unknown',
                'countdown_text' => 'Jadwal belum disetel',
            ];
        }

        $scheduleFormatted = $start->format('H:i') . ' - ' . $end->format('H:i') . ' WIB';
        $totalSeconds = max(1, $end->timestamp - $start->timestamp);

        if ($now->lessThan($start)) {
            $diffSeconds = $start->timestamp - $now->timestamp;
            $hours = floor($diffSeconds / 3600);
            $minutes = floor(($diffSeconds % 3600) / 60);
            $text = $hours > 0 ? "Mulai dalam {$hours}j {$minutes}m" : "Mulai dalam {$minutes} menit";

            return [
                'start_time' => $start,
                'end_time' => $end,
                'schedule_formatted' => $scheduleFormatted,
                'total_seconds' => $totalSeconds,
                'remaining_seconds' => $totalSeconds,
                'elapsed_seconds' => 0,
                'progress_percent' => 0.0,
                'is_active_now' => false,
                'is_upcoming' => true,
                'is_ended' => false,
                'is_overdue' => false,
                'status_state' => 'upcoming',
                'countdown_text' => $text,
            ];
        }

        if ($now->greaterThan($end)) {
            $overdueSeconds = $now->timestamp - $end->timestamp;
            $hours = floor($overdueSeconds / 3600);
            $minutes = floor(($overdueSeconds % 3600) / 60);
            $isOverdue = $this->isCheckedIn() && $overdueSeconds > 1800; // > 30 minutes
            $text = $hours > 0 ? "Sesi Berakhir ({$hours}j {$minutes}m lalu)" : "Sesi Berakhir ({$minutes}m lalu)";

            return [
                'start_time' => $start,
                'end_time' => $end,
                'schedule_formatted' => $scheduleFormatted,
                'total_seconds' => $totalSeconds,
                'remaining_seconds' => 0,
                'elapsed_seconds' => $totalSeconds,
                'progress_percent' => 100.0,
                'is_active_now' => false,
                'is_upcoming' => false,
                'is_ended' => true,
                'is_overdue' => $isOverdue,
                'status_state' => $isOverdue ? 'overdue' : 'ended',
                'countdown_text' => $text,
            ];
        }

        // Active ongoing session
        $elapsedSeconds = $now->timestamp - $start->timestamp;
        $remainingSeconds = max(0, $end->timestamp - $now->timestamp);
        $percent = min(100.0, round(($elapsedSeconds / $totalSeconds) * 100, 1));

        $remHours = floor($remainingSeconds / 3600);
        $remMinutes = floor(($remainingSeconds % 3600) / 60);
        $text = $remHours > 0 ? "Sisa {$remHours}j {$remMinutes}m" : "Sisa {$remMinutes} menit";

        $statusState = $remainingSeconds <= 900 ? 'ending_soon' : 'active';

        return [
            'start_time' => $start,
            'end_time' => $end,
            'schedule_formatted' => $scheduleFormatted,
            'total_seconds' => $totalSeconds,
            'remaining_seconds' => $remainingSeconds,
            'elapsed_seconds' => $elapsedSeconds,
            'progress_percent' => $percent,
            'is_active_now' => true,
            'is_upcoming' => false,
            'is_ended' => false,
            'is_overdue' => false,
            'status_state' => $statusState,
            'countdown_text' => $text,
        ];
    }
}