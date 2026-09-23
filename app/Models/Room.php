<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'slug',
        'floor_level',
        'capacity',
        'room_size_sqm',
        'buffer_minutes',
        'is_commercial_allowed',
        'base_hourly_rate',
        'is_active',
        'description',
        'supported_layouts',
    ];

    protected $casts = [
        'supported_layouts' => 'array',
        'is_active' => 'boolean',
        'is_commercial_allowed' => 'boolean',
        'floor_level' => 'integer',
        'capacity' => 'integer',
        'buffer_minutes' => 'integer',
        'base_hourly_rate' => 'decimal:2',
        'room_size_sqm' => 'decimal:2',
    ];

    public function images()
    {
        return $this->hasMany(RoomImage::class, 'room_id')->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(RoomImage::class, 'room_id')->where('is_primary', true);
    }

    public function facilities()
    {
        return $this->hasMany(RoomFacility::class, 'room_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_id');
    }

    /**
     * Get primary image URL or fallback to real local MCC asset based on room name/floor.
     */
    public function getThumbnailUrl(): string
    {
        $nameLower = strtolower($this->name);

        if (str_contains($nameLower, 'auditorium')) {
            return asset('assets/images/rooms/rooms_mcc/auditorium_lt_7.jpg');
        }
        if (str_contains($nameLower, 'amphitheater 1')) {
            return asset('assets/images/rooms/rooms_mcc/amphitheater_1_lt_5.jpg');
        }
        if (str_contains($nameLower, 'amphitheater 2')) {
            return asset('assets/images/rooms/rooms_mcc/amphitheater_2_lt_5.jpg');
        }
        if (str_contains($nameLower, 'podcast')) {
            return asset('assets/images/rooms/rooms_mcc/ruang_podcast_lt_2.jpg');
        }
        if (str_contains($nameLower, 'food lab')) {
            return asset('assets/images/rooms/rooms_mcc/food_lab_lt_3.jpg');
        }
        if (str_contains($nameLower, 'komputer')) {
            return asset('assets/images/rooms/rooms_mcc/lab_komputer_lt_4.jpg');
        }
        if (str_contains($nameLower, 'musik') || str_contains($nameLower, 'recording')) {
            return asset('assets/images/rooms/rooms_mcc/studio_musik_recording_lt_4.jpg');
        }
        if (str_contains($nameLower, 'foto')) {
            return asset('assets/images/rooms/rooms_mcc/studio_foto_lt_5.jpg');
        }
        if (str_contains($nameLower, 'rooftop')) {
            return asset('assets/images/rooms/rooms_mcc/rooftop_lt_8.jpg');
        }
        if (str_contains($nameLower, 'gallery') || str_contains($nameLower, 'galeri') || str_contains($nameLower, 'planning')) {
            return asset('assets/images/rooms/rooms_mcc/creative_city_planing_galery_lt_2.jpg');
        }

        // Fallback default
        return asset('assets/images/media/backgrounds/gedungmcc.png');
    }
}