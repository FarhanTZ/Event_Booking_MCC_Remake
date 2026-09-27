<?php

namespace App\Enums;

enum BookingStatus: string
{
    case DRAFT = 'draft';
    case AWAITING_MARKETING = 'awaiting_marketing';
    case REVISED = 'revised';
    case AWAITING_PAYMENT = 'awaiting_payment';
    case CONFIRMED = 'confirmed';
    case CHECKED_IN = 'checked_in';
    case COMPLETED = 'completed';
    case ABANDONED_CLEAN = 'abandoned_clean';
    case ABANDONED_INCIDENT = 'abandoned_incident';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draf',
            self::AWAITING_MARKETING => 'Menunggu Kurasi',
            self::REVISED => 'Perlu Revisi',
            self::AWAITING_PAYMENT => 'Menunggu Pembayaran',
            self::CONFIRMED => 'Disetujui',
            self::CHECKED_IN => 'Check-in Berlangsung',
            self::COMPLETED => 'Selesai',
            self::ABANDONED_CLEAN => 'Ditinggal (Bersih)',
            self::ABANDONED_INCIDENT => 'Ditinggal (Bermasalah)',
            self::REJECTED => 'Ditolak',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    /**
     * Get Tailwind CSS classes for badges according to PRD/Design.md strict mapping.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::CONFIRMED, self::COMPLETED => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            self::AWAITING_MARKETING, self::AWAITING_PAYMENT => 'bg-amber-50 text-amber-700 border border-amber-200',
            self::REVISED => 'bg-indigo-50 text-indigo-700 border border-indigo-200',
            self::REJECTED, self::CANCELLED => 'bg-rose-50 text-rose-700 border border-rose-200',
            self::CHECKED_IN => 'bg-teal-50 text-teal-700 border border-teal-200',
            self::ABANDONED_CLEAN => 'bg-amber-50 text-amber-800 border border-amber-300',
            self::ABANDONED_INCIDENT => 'bg-rose-100 text-rose-800 border border-rose-300 font-bold',
            self::DRAFT => 'bg-slate-100 text-slate-700 border border-slate-200',
        };
    }
}
