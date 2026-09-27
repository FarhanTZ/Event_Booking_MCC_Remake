<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $incident_id
 * @property string $photo_url
 * @property string|null $caption
 * @property \Carbon\Carbon $created_at
 * 
 * @property-read \App\Models\RoomIncident $incident
 */
class IncidentPhoto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'incident_photos';

    protected $fillable = [
        'incident_id',
        'photo_url',
        'caption',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(RoomIncident::class, 'incident_id');
    }
}
