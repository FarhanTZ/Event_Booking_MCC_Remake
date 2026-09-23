<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected  = [
        'booking_code',
        'user_id',
        'room_id',
        'status',
        'rate_type',
        'revision_deadline',
        'checkin_token',
        'checked_in_at',
    ];

    protected  = [
        'revision_deadline' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function user()
    {
        return ->belongsTo(User::class);
    }

    public function room()
    {
        return ->belongsTo(Room::class);
    }

    public function slots()
    {
        return ->hasMany(BookingSlot::class);
    }

    public function eventDetail()
    {
        return ->hasOne(EventDetail::class);
    }
}