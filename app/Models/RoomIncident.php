<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $booking_id
 * @property string $reported_by_do
 * @property string $category
 * @property string|null $item_name
 * @property string $description
 * @property bool $is_resolved
 * @property string|null $resolution_notes
 * @property \Carbon\Carbon|null $resolved_at
 * @property \Carbon\Carbon $created_at
 * 
 * @property-read \App\Models\Booking $booking
 * @property-read \App\Models\User $reportedByDo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\IncidentPhoto> $photos
 */
class RoomIncident extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'room_incidents';

    protected $fillable = [
        'booking_id',
        'reported_by_do',
        'category',
        'item_name',
        'description',
        'is_resolved',
        'resolution_notes',
        'resolved_at',
        'created_at',
    ];

    protected $casts = [
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function reportedByDo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_do');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(IncidentPhoto::class, 'incident_id');
    }
}
