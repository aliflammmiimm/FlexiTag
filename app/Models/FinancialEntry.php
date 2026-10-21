<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'category',
        'entry_date',
        'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'entry_date' => 'date',
    ];

    public function scopePemasukan(Builder $query): Builder
    {
        return $query->where('type', 'pemasukan');
    }

    public function scopePengeluaran(Builder $query): Builder
    {
        return $query->where('type', 'pengeluaran');
    }
}
