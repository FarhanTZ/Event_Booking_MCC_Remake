<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $booking_id
 * @property string $event_name
 * @property string $event_category
 * @property string|null $ekraf_subsector
 * @property string|null $event_type
 * @property int $estimated_participants
 * @property string $event_description
 * @property string|null $equipment_needed
 * @property array $requested_facilities
 * @property string $proposal_file_url
 * @property string|null $official_letter_number
 * @property string|null $poster_image_url
 * @property string $pic_name_snapshot
 * @property string $pic_phone_snapshot
 * @property string|null $pic_emergency_phone
 * @property string $institution_name_snapshot
 * @property \Carbon\Carbon $tos_agreed_at
 * @property \Carbon\Carbon $created_at
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|EventDetail query()
 * @method static \App\Models\EventDetail create(array $attributes = [])
 */
class EventDetail extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;
    public const CREATED_AT = 'created_at';

    protected $fillable = [
        'booking_id',
        'event_name',
        'event_category',
        'ekraf_subsector',
        'event_type',
        'estimated_participants',
        'event_description',
        'equipment_needed',
        'requested_facilities',
        'proposal_file_url',
        'official_letter_number',
        'poster_image_url',
        'pic_name_snapshot',
        'pic_phone_snapshot',
        'pic_emergency_phone',
        'institution_name_snapshot',
        'tos_agreed_at',
    ];

    protected $casts = [
        'requested_facilities' => 'array',
        'estimated_participants' => 'integer',
        'tos_agreed_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
