<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    public $timestamps = false;

    // Supported Notification Types
    public const TYPE_LOST_ITEM = 'lost_item';

    public const TYPE_CONTACT = 'contact';

    public const TYPE_ABUSE_REPORT = 'abuse_report';

    public const TYPE_USER_FEEDBACK = 'user_feedback';

    public const TYPE_NEW_ORDER = 'new_order';

    public const TYPE_LOW_STOCK = 'low_stock';

    protected $fillable = [
        'type',
        'user_id',
        'order_id',
        'sender_name',
        'location',
        'message',
        'contact',
        'action_url',
        'read_at',
        'created_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Scope for unread messages.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for specific message types.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for all notifications targeted at admin fleet console.
     */
    public function scopeAdminNotifications(Builder $query): Builder
    {
        return $query->whereIn('type', [
            self::TYPE_ABUSE_REPORT,
            self::TYPE_CONTACT,
            self::TYPE_USER_FEEDBACK,
            self::TYPE_NEW_ORDER,
            self::TYPE_LOW_STOCK,
        ]);
    }

    /**
     * Buat notifikasi pesanan baru untuk admin console.
     */
    public static function recordNewOrderNotification(Order $order): self
    {
        $shape = ucfirst($order->shape ?? 'Akrilik');
        $color = $order->color ?? 'Standar';
        $formattedTotal = 'Rp '.number_format((float) $order->total, 0, ',', '.');

        return self::create([
            'type' => self::TYPE_NEW_ORDER,
            'user_id' => null,
            'order_id' => $order->id,
            'sender_name' => $order->customer_name,
            'location' => "Pesanan #{$order->order_no}",
            'message' => "Pesanan {$order->quantity} unit ganci ({$shape}, {$color}) telah dibuat. Total belanja: {$formattedTotal}.",
            'contact' => $order->whatsapp ?: $order->email,
            'action_url' => route('admin.orders', ['search' => $order->order_no]),
            'read_at' => null,
            'created_at' => now(),
        ]);
    }

    /**
     * Buat notifikasi peringatan stok tag menipis untuk admin console.
     */
    public static function recordLowStockAlert(int $remainingCount, int $threshold = 10): self
    {
        return self::create([
            'type' => self::TYPE_LOW_STOCK,
            'user_id' => null,
            'order_id' => null,
            'sender_name' => 'Sistem Inventaris NFC',
            'location' => 'Makassar Core Node',
            'message' => "Stok tag belum diaktivasi hanya tersisa {$remainingCount} unit (< {$threshold}). Segera generate batch tag baru agar kode aktivasi random terus tersedia untuk pemesan.",
            'contact' => null,
            'action_url' => route('admin.generate'),
            'read_at' => null,
            'created_at' => now(),
        ]);
    }

    /**
     * Buat notifikasi saran, masukan, atau laporan dari user login.
     */
    public static function recordUserFeedback(User $user, string $category, string $content, ?string $contact = null): self
    {
        $categoryLabel = match ($category) {
            'saran' => 'Saran & Ide Fitur',
            'kendala' => 'Laporan Masalah / Bug',
            'pertanyaan' => 'Pertanyaan / Bantuan',
            default => 'Masukan Pengguna',
        };

        return self::create([
            'type' => self::TYPE_USER_FEEDBACK,
            'user_id' => $user->id,
            'order_id' => null,
            'sender_name' => $user->name,
            'location' => "Panel Pengguna ({$categoryLabel})",
            'message' => $content,
            'contact' => $contact ?: ($user->no_whatsapp ?: $user->email),
            'action_url' => null,
            'read_at' => null,
            'created_at' => now(),
        ]);
    }
}
