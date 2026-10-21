<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag_code',
        'activation_code',
        'user_id',
        'status',
        'disabled_by',
        'batch_id',
        'produced_at',
        'activated_at',
    ];

    protected $casts = [
        'produced_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the public URL for the NFC tag.
     */
    public function getPublicUrlAttribute(): string
    {
        return url('/u/'.$this->tag_code);
    }

    /**
     * Compact visual status badge configuration.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'aktif' => [
                'label' => 'Aktif',
                'bg_class' => 'bg-[#EAF5EC] text-[#2E7D32] border border-[#C8E6C9]',
                'dot_class' => 'bg-[#2E7D32]',
            ],
            'nonaktif' => [
                'label' => $this->disabled_by === 'admin' ? 'Diblokir Admin' : 'Nonaktif (Kill-Switch)',
                'bg_class' => 'bg-[#FEE2E2] text-[#991B1B] border border-[#FECACA]',
                'dot_class' => 'bg-[#991B1B]',
            ],
            default => [
                'label' => 'Belum Aktif (Stok)',
                'bg_class' => 'bg-[#F6F5F2] text-[#737373] border border-[#ECE8E1]',
                'dot_class' => 'bg-[#8A8A85]',
            ],
        };
    }

    /**
     * Scope query to filter tags by status.
     */
    public function scopeStatusFilter(Builder $query, string $status): Builder
    {
        return match ($status) {
            'aktif' => $query->where('status', 'aktif'),
            'belum_aktif' => $query->where('status', 'belum_aktif'),
            'nonaktif', 'diblokir' => $query->where('status', 'nonaktif'),
            default => $query,
        };
    }

    /**
     * Scope query to search tags by tag_code, batch_id, or owner name.
     */
    public function scopeSearchFilter(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('tag_code', 'like', "%{$search}%")
                ->orWhere('batch_id', 'like', "%{$search}%")
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('profile', function ($pq) use ($search) {
                            $pq->where('display_name', 'like', "%{$search}%");
                        });
                });
        });
    }
}
