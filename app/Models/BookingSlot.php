<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSlot extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'room_id',
        'duty_officer_id',
        'duty_officer_assigned_at',
        'duty_officer_assigned_by',
        'duty_officer_notes',
        'do_notified_at',
        'do_notified_by',
        'do_notified_channel',
        'assigned_checkout_do_id',
        'checkout_duty_officer_id',
        'checkout_do_assigned_at',
        'checkout_initiated_at',
        'checkout_do_assigned_by',
        'checkout_initiated_by',
        'checkout_do_notes',
        'checkout_do_notified_at',
        'checkout_do_notified_by',
        'checkout_do_notified_channel',
        'event_start_at',
        'event_end_at',
        'operational_range',
        'is_active',
    ];

    protected $casts = [
        'event_start_at' => 'datetime',
        'event_end_at' => 'datetime',
        'duty_officer_assigned_at' => 'datetime',
        'do_notified_at' => 'datetime',
        'checkout_do_assigned_at' => 'datetime',
        'checkout_initiated_at' => 'datetime',
        'checkout_do_notified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function dutyOfficer()
    {
        return $this->belongsTo(User::class, 'duty_officer_id');
    }

    public function dutyOfficerAssignedBy()
    {
        return $this->belongsTo(User::class, 'duty_officer_assigned_by');
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

    public function checkoutDoNotifiedBy()
    {
        return $this->belongsTo(User::class, 'checkout_do_notified_by');
    }
}
