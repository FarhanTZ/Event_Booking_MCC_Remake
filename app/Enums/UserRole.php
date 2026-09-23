<?php

namespace App\Enums;

enum UserRole: string
{
    case USER = 'user';
    case MARKETING = 'marketing';
    case FRONT_OFFICE = 'front_office';
    case DUTY_OFFICER = 'duty_officer';
    case SUPER_ADMIN = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::USER => 'Pemohon Publik',
            self::MARKETING => 'Marketing & Kurasi',
            self::FRONT_OFFICE => 'Front Office / Kustodian KTP',
            self::DUTY_OFFICER => 'Duty Officer / Inspeksi',
            self::SUPER_ADMIN => 'Super Administrator',
        };
    }
}
