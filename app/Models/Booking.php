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
        'completed_at',
        'completed_by',
        'revision_deadline',
        'version',
    ];

    protected $casts = [
        'status' => \App\Enums\BookingStatus::class,
        'paid_at' => 'datetime',
        'checked_in_at' => 'datetime',
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
        if (! $this->isUrgentTriage()) {
            return null;
        }

        $earliestDate = $this->getEarliestEventDate();
        $daysDiff = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays($earliestDate->startOfDay(), false);
        $daysLabel = $daysDiff <= 0 ? 'Hari-H / Lewat' : "H-{$daysDiff}";

        return [
            'label' => "Mendesak ({$daysLabel})",
            'days' => $daysDiff,
            'is_critical' => $daysDiff <= 1,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function slots()
    {
        return $this->hasMany(BookingSlot::class, 'booking_id');
    }

    public function eventDetail()
    {
        return $this->hasOne(EventDetail::class, 'booking_id');
    }
}