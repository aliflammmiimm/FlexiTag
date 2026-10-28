<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'channel',
        'customer_name',
        'whatsapp',
        'email',
        'delivery_method',
        'address',
        'shape',
        'size',
        'color',
        'notes',
        'quantity',
        'unit_price',
        'total',
        'payment_method',
        'snap_token',
        'payment_payload',
        'paid_at',
        'status',
        'design_file_path',
        'payment_proof_path',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * SRS Official Order Status Constants.
     */
    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';

    public const STATUS_PEMBAYARAN_DIKONFIRMASI = 'pembayaran_dikonfirmasi';

    public const STATUS_DESAIN_DISETUJUI = 'desain_disetujui';

    public const STATUS_DICETAK = 'dicetak';

    public const STATUS_DIRAKIT_DAN_DIUJI = 'dirakit_dan_diuji';

    public const STATUS_SIAP_DIAMBIL_DIKIRIM = 'siap_diambil_dikirim';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    /**
     * Map all official stages to human-readable names with workflow step numbers.
     */
    public static function allStatuses(): array
    {
        return [
            self::STATUS_MENUNGGU_PEMBAYARAN => '1. Menunggu Pembayaran',
            self::STATUS_PEMBAYARAN_DIKONFIRMASI => '2. Pembayaran Dikonfirmasi',
            self::STATUS_DESAIN_DISETUJUI => '3. Desain Disetujui',
            self::STATUS_DICETAK => '4. Dicetak',
            self::STATUS_DIRAKIT_DAN_DIUJI => '5. Dirakit & Diuji (QC)',
            self::STATUS_SIAP_DIAMBIL_DIKIRIM => '6. Siap Diambil / Dikirim',
            self::STATUS_SELESAI => '7. Selesai (Diterima)',
            self::STATUS_DIBATALKAN => 'Dibatalkan',
        ];
    }

    /**
     * Visual Badge Configuration mapping the detailed SRS status to compact UI badges.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            self::STATUS_MENUNGGU_PEMBAYARAN => [
                'label' => 'Menunggu Bayar',
                'category' => 'pending',
                'bg_class' => 'bg-[#FEF5E7] text-[#A86414] border border-[#FAD7A0]',
                'dot_class' => 'bg-[#A86414]',
            ],
            self::STATUS_PEMBAYARAN_DIKONFIRMASI,
            self::STATUS_DESAIN_DISETUJUI,
            self::STATUS_DICETAK,
            self::STATUS_DIRAKIT_DAN_DIUJI => [
                'label' => 'Diproses (Cetak/Rakit)',
                'category' => 'perlu_dikirim',
                'bg_class' => 'bg-[#F3E8FF] text-[#6B21A8] border border-[#E9D5FF]',
                'dot_class' => 'bg-[#6B21A8]',
            ],
            self::STATUS_SIAP_DIAMBIL_DIKIRIM => [
                'label' => 'Siap / Dikirim',
                'category' => 'terkirim',
                'bg_class' => 'bg-[#E6F3FA] text-[#1E70A0] border border-[#BAE6FD]',
                'dot_class' => 'bg-[#1E70A0]',
            ],
            self::STATUS_SELESAI => [
                'label' => 'Selesai',
                'category' => 'selesai',
                'bg_class' => 'bg-[#EAF5EC] text-[#2E7D32] border border-[#C8E6C9]',
                'dot_class' => 'bg-[#2E7D32]',
            ],
            self::STATUS_DIBATALKAN => [
                'label' => 'Dibatalkan',
                'category' => 'dibatalkan',
                'bg_class' => 'bg-[#FEE2E2] text-[#991B1B] border border-[#FECACA]',
                'dot_class' => 'bg-[#991B1B]',
            ],
            default => [
                'label' => ucfirst(str_replace('_', ' ', (string) $this->status)),
                'category' => 'other',
                'bg_class' => 'bg-[#F6F5F2] text-[#737373] border border-[#ECE8E1]',
                'dot_class' => 'bg-[#737373]',
            ],
        };
    }

    /**
     * Detail status label for modals and full tracking.
     */
    public function getDetailStatusLabelAttribute(): string
    {
        return self::allStatuses()[$this->status] ?? ucfirst(str_replace('_', ' ', (string) $this->status));
    }

    /**
     * History of status changes for this order.
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class, 'order_id')->orderBy('changed_at', 'desc');
    }

    /**
     * Tags (NFC Keychain Hardware) assigned to this order.
     */
    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class, 'order_id');
    }

    /**
     * Generate unique Order Number: ORD-YYYY-XXXX
     */
    public static function generateOrderNo(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        $orderNo = sprintf('ORD-%s-%04d', $year, $count);

        while (self::where('order_no', $orderNo)->exists()) {
            $count++;
            $orderNo = sprintf('ORD-%s-%04d', $year, $count);
        }

        return $orderNo;
    }

    /**
     * Badge config for Online vs Offline order channel.
     */
    public function getChannelBadgeAttribute(): array
    {
        if ($this->channel === 'offline') {
            return [
                'label' => 'Offline / Langsung',
                'bg_class' => 'bg-[#FEF5E7] text-[#A86414] border border-[#FAD7A0]',
                'icon' => 'store',
            ];
        }

        return [
            'label' => 'Online Web',
            'bg_class' => 'bg-[#EBF5FB] text-[#1E70A0] border border-[#BAE6FD]',
            'icon' => 'globe',
        ];
    }
}
