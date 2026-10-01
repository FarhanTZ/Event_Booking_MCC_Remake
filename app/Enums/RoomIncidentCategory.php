<?php

namespace App\Enums;

enum RoomIncidentCategory: string
{
    case CLEANLINESS = 'cleanliness';
    case PROPERTY_DAMAGE = 'property_damage';
    case EQUIPMENT_LOSS = 'equipment_loss';

    public function label(): string
    {
        return match ($this) {
            self::CLEANLINESS => 'Kebersihan Ruangan (Kotor/Sampah)',
            self::PROPERTY_DAMAGE => 'Kerusakan Fasilitas / Properti',
            self::EQUIPMENT_LOSS => 'Kehilangan Aset / Peralatan',
        };
    }
}
