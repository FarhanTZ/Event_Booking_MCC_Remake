<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'paid_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'completed_at' => 'datetime',
        'revision_deadline' => 'datetime',
        'nominal_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}