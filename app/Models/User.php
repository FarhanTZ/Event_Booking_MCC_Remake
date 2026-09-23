<?php

namespace App\Models;

use App\Enums\AccountType;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'account_type',
        'full_name',
        'email',
        'phone_number',
        'password_hash',
        'institution_name',
        'logo_url',
        'address',
        'description',
        'social_media',
        'website',
        'category',
        'sub_sector',
        'city',
        'district',
        'is_suspended',
        'suspension_reason',
        'suspended_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'account_type' => AccountType::class,
            'social_media' => 'array',
            'is_suspended' => 'boolean',
            'suspended_at' => 'datetime',
            'password_hash' => 'hashed',
        ];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Get the column name for the password.
     *
     * @return string
     */
    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    /**
     * Domain Invariants & Helper Methods
     */
    public function isSuspended(): bool
    {
        return (bool) $this->is_suspended;
    }

    public function isInstansi(): bool
    {
        return $this->account_type === AccountType::INSTANSI;
    }

    public function isKomunitas(): bool
    {
        return $this->account_type === AccountType::KOMUNITAS;
    }

    public function isSekolah(): bool
    {
        return $this->account_type === AccountType::SEKOLAH;
    }

    public function isMarketing(): bool
    {
        return $this->role === UserRole::MARKETING;
    }

    public function isFrontOffice(): bool
    {
        return $this->role === UserRole::FRONT_OFFICE;
    }

    public function isDutyOfficer(): bool
    {
        return $this->role === UserRole::DUTY_OFFICER;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPER_ADMIN;
    }

    /**
     * Get the formatted logo URL or fallback default.
     */
    public function getLogoAssetUrl(): ?string
    {
        if (! $this->logo_url) {
            return null;
        }

        if (str_starts_with($this->logo_url, 'http://') || str_starts_with($this->logo_url, 'https://')) {
            return $this->logo_url;
        }

        return asset('storage/' . $this->logo_url);
    }

    /**
     * Get user's bookings.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }
}
