<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomFacility extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'room_id',
        'facility_name',
        'quantity',
        'unit',
        'condition',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
