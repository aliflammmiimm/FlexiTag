<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Categories mapped to human-readable labels and badge styles.
     */
    public static function categories(): array
    {
        return [
            'motif_lokal' => [
                'label' => 'Motif Lokal Toraja / Bugis',
                'bg' => 'bg-[#FCF5E3]',
                'text' => 'text-[#8C6D23]',
                'border' => 'border-[#F0C75E]/40',
            ],
            'pribadi' => [
                'label' => 'Pribadi & Minimalis',
                'bg' => 'bg-[#F6F5F2]',
                'text' => 'text-[#4A4A4A]',
                'border' => 'border-[#ECE8E1]',
            ],
            'organisasi' => [
                'label' => 'Organisasi & Kampus',
                'bg' => 'bg-[#F7EBEB]',
                'text' => 'text-[#540404]',
                'border' => 'border-[#F5C2C2]',
            ],
            'umkm' => [
                'label' => 'Branding UMKM',
                'bg' => 'bg-[#EAF5EC]',
                'text' => 'text-[#2E7D32]',
                'border' => 'border-[#BBE3C2]',
            ],
        ];
    }

    public function getCategoryBadgeAttribute(): array
    {
        return self::categories()[$this->category] ?? [
            'label' => ucfirst($this->category),
            'bg' => 'bg-[#F6F5F2]',
            'text' => 'text-[#737373]',
            'border' => 'border-[#ECE8E1]',
        ];
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('storage/'.$this->image_path);
        }

        return asset('images/keychain.jpg');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Hitung perkiraan unit terjual dari data pesanan aktif.
     */
    public function getUnitsSoldAttribute(): int
    {
        $keywords = [];
        $lowerName = strtolower((string) $this->name);
        if (str_contains($lowerName, 'toraja')) {
            $keywords[] = 'toraja';
        }
        if (str_contains($lowerName, 'bugis')) {
            $keywords[] = 'bugis';
        }
        if (str_contains($lowerName, 'bulat')) {
            $keywords[] = 'bulat';
        }
        if (str_contains($lowerName, 'persegi') || str_contains($lowerName, 'umkm')) {
            $keywords[] = 'persegi';
            $keywords[] = 'umkm';
        }

        if (empty($keywords)) {
            return (int) Order::where('status', '!=', Order::STATUS_DIBATALKAN)
                ->where('notes', 'like', '%'.$this->name.'%')
                ->sum('quantity');
        }

        return (int) Order::where('status', '!=', Order::STATUS_DIBATALKAN)
            ->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('shape', 'like', '%'.$kw.'%')
                        ->orWhere('color', 'like', '%'.$kw.'%')
                        ->orWhere('notes', 'like', '%'.$kw.'%');
                }
            })
            ->sum('quantity');
    }
}
