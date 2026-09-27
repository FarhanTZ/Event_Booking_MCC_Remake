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
}