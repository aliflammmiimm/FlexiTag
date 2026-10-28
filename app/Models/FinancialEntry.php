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

    /**
     * Catat otomatis pembayaran pesanan masuk (Online Web maupun Offline Kasir) ke kas.
     */
    public static function recordOrderPayment(Order $order): ?self
    {
        // Hindari duplikasi jika pesanan sudah tercatat
        $exists = self::where('note', 'like', "%{$order->order_no}%")->exists();
        if ($exists) {
            return null;
        }

        $channelLabel = ($order->channel === 'offline') ? 'Offline Kasir' : 'Online Web';

        return self::create([
            'type' => 'pemasukan',
            'amount' => $order->total,
            'category' => "Penjualan Ganci ({$channelLabel})",
            'entry_date' => now()->toDateString(),
            'note' => "Pembayaran pesanan {$order->order_no} ({$order->customer_name})",
        ]);
    }
}
