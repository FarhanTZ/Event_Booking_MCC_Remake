<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomImage extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'room_id',
        'image_url',
        'caption',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
