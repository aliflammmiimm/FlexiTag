<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'google_id', 'role', 'is_active', 'jabatan_admin', 'no_whatsapp', 'two_factor_enabled', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    /**
     * Profile relation (1:1).
     */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    /**
     * Tags relation (1:N).
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'user_id');
    }

    /**
     * Links relation (1:N).
     */
    public function links(): HasMany
    {
        return $this->hasMany(Link::class, 'user_id');
    }

    /**
     * Mode data relation (1:N).
     */
    public function modeData(): HasMany
    {
        return $this->hasMany(ModeData::class, 'user_id');
    }

    /**
     * Privacy settings relation (1:1).
     */
    public function privacySetting(): HasOne
    {
        return $this->hasOne(PrivacySetting::class, 'user_id');
    }

    /**
     * Role badge metadata.
     */
    public function getRoleBadgeAttribute(): array
    {
        return match ($this->role) {
            'admin' => [
                'label' => 'Super Admin',
                'bg' => 'bg-[#FCF5E3]',
                'text' => 'text-[#8C6D23]',
                'dot' => 'bg-[#E5B32A]',
            ],
            'staff' => [
                'label' => 'Staff Operasional',
                'bg' => 'bg-[#F7EBEB]',
                'text' => 'text-maroon',
                'dot' => 'bg-maroon',
            ],
            default => [
                'label' => 'Pengguna Tag',
                'bg' => 'bg-[#F6F5F2]',
                'text' => 'text-[#4A4A4A]',
                'dot' => 'bg-[#8A8A85]',
            ],
        };
    }

    /**
     * Active status badge metadata.
     */
    public function getStatusBadgeAttribute(): array
    {
        if ($this->is_active) {
            return [
                'label' => 'Aktif',
                'bg' => 'bg-[#EAF5EC]',
                'text' => 'text-[#2E7D32]',
                'border' => 'border-[#C8E6C9]',
            ];
        }

        return [
            'label' => 'Diblokir',
            'bg' => 'bg-[#FEE2E2]',
            'text' => 'text-[#991B1B]',
            'border' => 'border-[#FECACA]',
        ];
    }
}
