<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $booking_id
 * @property string $ktp_holder_name
 * @property string|null $pic_whatsapp_number
 * @property string|null $storage_drawer_number
 * @property string|null $nik_number
 * @property string|null $ktp_photo_scan_url
 * @property string $received_by_fo
 * @property \Carbon\Carbon $retained_at
 * @property string|null $returned_by_fo
 * @property \Carbon\Carbon|null $returned_at
 * @property string|null $unclaimed_box_id
 * @property string|null $notes
 */
class KtpVaultLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'ktp_vault_logs';

    protected $fillable = [
        'booking_id',
        'ktp_holder_name',
        'pic_whatsapp_number',
        'storage_drawer_number',
        'nik_number',
        'ktp_photo_scan_url',
        'received_by_fo',
        'retained_at',
        'returned_by_fo',
        'returned_at',
        'unclaimed_box_id',
        'notes',
    ];

    protected $casts = [
        'retained_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_fo');
    }

    public function returnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by_fo');
    }
}
