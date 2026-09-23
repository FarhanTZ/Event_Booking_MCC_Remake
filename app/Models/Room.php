<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected  = [
        'name',
        'slug',
        'floor_level',
        'capacity',
        'area_sqm',
        'hourly_rate',
        'is_active',
        'supported_layouts',
    ];

    protected  = [
        'supported_layouts' => 'array',
        'is_active' => 'boolean',
    ];

    public function images()
    {
        return ->hasMany(RoomImage::class);
    }

    public function facilities()
    {
        return ->hasMany(RoomFacility::class);
    }

    public function bookings()
    {
        return ->hasMany(Booking::class);
    }
}