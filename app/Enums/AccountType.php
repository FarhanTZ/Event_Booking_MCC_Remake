<?php

namespace App\Enums;

enum AccountType: string
{
    case INSTANSI = 'instansi';
    case KOMUNITAS = 'komunitas';
    case SEKOLAH = 'sekolah';

    public function label(): string
    {
        return match ($this) {
            self::INSTANSI => 'Instansi / Lembaga / Bisnis',
            self::KOMUNITAS => 'Kelompok / Komunitas',
            self::SEKOLAH => 'Kampus / Sekolah',
        };
    }

    public function entityNameLabel(): string
    {
        return match ($this) {
            self::INSTANSI => 'Nama Instansi',
            self::KOMUNITAS => 'Nama Kelompok / Komunitas',
            self::SEKOLAH => 'Nama Kampus / Sekolah',
        };
    }

    public function logoLabel(): string
    {
        return match ($this) {
            self::INSTANSI => 'Logo Instansi',
            self::KOMUNITAS => 'Foto / Logo Komunitas',
            self::SEKOLAH => 'Foto / Logo Sekolah',
        };
    }
}
