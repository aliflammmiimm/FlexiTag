<?php

namespace App\Services;

use App\Mail\LowTagStockAlertMail;
use App\Mail\OrderReadyActivationMail;
use App\Models\Message;
use App\Models\Order;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderActivationService
{
    /**
     * Ambang batas minimal stok tag belum diaktivasi untuk memicu peringatan admin.
     */
    public const LOW_STOCK_THRESHOLD = 10;

    /**
     * Alokasikan kode aktivasi random dari daftar tag yang belum terpakai (belum_aktif dan belum berpemilik).
     *
     * @return Collection<int, Tag>
     */
    public function assignRandomTagsToOrder(Order $order): Collection
    {
        return DB::transaction(function () use ($order) {
            // Cek apakah pesanan ini sudah memiliki tag yang dialokasikan sebelumnya
            $existingTags = Tag::where('order_id', $order->id)->get();
            $needed = max(0, (int) $order->quantity - $existingTags->count());

            if ($needed > 0) {
                // Pilih secara random tag yang belum aktif, belum memiliki user, dan belum dialokasikan ke order manapun
                $newTags = Tag::where('status', 'belum_aktif')
                    ->whereNull('user_id')
                    ->whereNull('order_id')
                    ->inRandomOrder()
                    ->lockForUpdate()
                    ->take($needed)
                    ->get();

                foreach ($newTags as $tag) {
                    $tag->update(['order_id' => $order->id]);
                }

                if ($newTags->count() < $needed) {
                    Log::warning("Stok tag tidak mencukupi untuk pesanan #{$order->order_no}. Dibutuhkan: {$needed}, dialokasikan: {$newTags->count()}.");
                }
            }

            return Tag::where('order_id', $order->id)->get();
        });
    }

    /**
     * Jalankan alur alokasi kode aktivasi random dan kirimkan email ke pengguna ketika pembayaran pesanan dikonfirmasi.
     *
     * @return array{success: bool, message: string, tags: Collection<int, Tag>}
     */
    public function processOrderConfirmation(Order $order): array
    {
        // 1. Alokasikan tag secara random ke order ini jika belum ada
        $tags = $this->assignRandomTagsToOrder($order);

        if ($tags->isEmpty()) {
            $this->checkAndNotifyLowStock();

            return [
                'success' => false,
                'message' => 'Stok tag belum aktif tidak tersedia untuk dialokasikan.',
                'tags' => $tags,
            ];
        }

        // 2. Kirim email berisi kode aktivasi ke alamat email pengguna
        $emailSent = false;
        if (! empty($order->email) && filter_var($order->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($order->email)->send(new OrderReadyActivationMail($order, $tags));
                $emailSent = true;
            } catch (\Throwable $e) {
                Log::error("Gagal mengirimkan email aktivasi pesanan #{$order->order_no} ke {$order->email}: ".$e->getMessage());
            }
        } else {
            Log::warning("Pesanan #{$order->order_no} tidak memiliki email valid: '{$order->email}'. Email aktivasi dilewati.");
        }

        // 3. Periksa sisa stok tag yang belum diaktivasi, kirim alert ke admin jika < 10
        $this->checkAndNotifyLowStock();

        $codes = $tags->pluck('activation_code')->implode(', ');
        $message = $emailSent
            ? "Berhasil mengalokasikan {$tags->count()} kode aktivasi ({$codes}) dan mengirim email aktivasi ke {$order->email}."
            : "Kode aktivasi ({$codes}) dialokasikan, namun email gagal terkirim / alamat email tidak valid.";

        return [
            'success' => $emailSent,
            'message' => $message,
            'tags' => $tags,
        ];
    }

    /**
     * Alias backwards-compatibility untuk pesanan siap.
     *
     * @return array{success: bool, message: string, tags: Collection<int, Tag>}
     */
    public function processOrderReady(Order $order): array
    {
        return $this->processOrderConfirmation($order);
    }

    /**
     * Hitung sisa stok tag belum aktif yang belum terikat ke order maupun user.
     */
    public function getAvailableStockCount(): int
    {
        return Tag::where('status', 'belum_aktif')
            ->whereNull('user_id')
            ->whereNull('order_id')
            ->count();
    }

    /**
     * Periksa jumlah tag yang belum diaktivasi. Jika < 10, kirim notifikasi email ke admin.
     */
    public function checkAndNotifyLowStock(bool $forceSend = false): int
    {
        $remainingCount = $this->getAvailableStockCount();

        if ($remainingCount < self::LOW_STOCK_THRESHOLD) {
            // 1. Catat notifikasi stok kritis ke drawer admin jika belum ada entri unread serupa
            try {
                $existingUnread = Message::where('type', Message::TYPE_LOW_STOCK)
                    ->whereNull('read_at')
                    ->first();

                if (! $existingUnread) {
                    Message::recordLowStockAlert($remainingCount, self::LOW_STOCK_THRESHOLD);
                } else {
                    $existingUnread->update([
                        'message' => "Stok tag belum diaktivasi hanya tersisa {$remainingCount} unit (< ".self::LOW_STOCK_THRESHOLD.'). Segera generate batch tag baru agar kode aktivasi random terus tersedia untuk pemesan.',
                        'created_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Gagal mencatat notifikasi stok tag menipis ke drawer admin: '.$e->getMessage());
            }

            // 2. Hindari spamming email ke admin jika notifikasi sudah terkirim dalam 30 menit terakhir
            $cacheKey = 'admin_low_tag_stock_alert_sent';
            if ($forceSend || ! Cache::has($cacheKey)) {
                $activeCount = Tag::where('status', 'aktif')->count();
                $totalCount = Tag::count();
                $adminEmail = config('mail.from.address') ?? 'admin@flexitag.id';

                try {
                    Mail::to($adminEmail)->send(new LowTagStockAlertMail(
                        remainingCount: $remainingCount,
                        activeCount: $activeCount,
                        totalCount: $totalCount
                    ));

                    // Simpan flag cache selama 30 menit
                    Cache::put($cacheKey, true, now()->addMinutes(30));
                    Log::warning("Notifikasi stok tag menipis (< 10) berhasil dikirim ke admin ({$adminEmail}). Sisa: {$remainingCount}.");
                } catch (\Throwable $e) {
                    Log::error('Gagal mengirim email alert stok tag menipis ke admin: '.$e->getMessage());
                }
            }
        } else {
            // Jika stok sudah di-generate kembali (>= 10), tandai notifikasi stok lama sebagai selesai/dibaca
            try {
                Message::where('type', Message::TYPE_LOW_STOCK)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            } catch (\Throwable $e) {
                // Abaikan kesalahan silent
            }
        }

        return $remainingCount;
    }

    /**
     * Deteksi kemungkinan salah ketik (typo) domain email umum (misal: gmai.com -> gmail.com).
     */
    public static function detectEmailDomainTypo(string $email): ?string
    {
        if (! str_contains($email, '@')) {
            return null;
        }

        $parts = explode('@', strtolower(trim($email)), 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$user, $domain] = $parts;

        $commonTypos = [
            'gmai.com' => 'gmail.com',
            'gamil.com' => 'gmail.com',
            'gmial.com' => 'gmail.com',
            'gmaill.com' => 'gmail.com',
            'gmaik.com' => 'gmail.com',
            'gmeil.com' => 'gmail.com',
            'yaho.com' => 'yahoo.com',
            'yahooo.com' => 'yahoo.com',
            'yaho.co.id' => 'yahoo.co.id',
            'yaho.id' => 'yahoo.com',
            'hotmial.com' => 'hotmail.com',
            'hotmaill.com' => 'hotmail.com',
            'outlok.com' => 'outlook.com',
            'outloo.com' => 'outlook.com',
            'iclloud.com' => 'icloud.com',
            'iclod.com' => 'icloud.com',
        ];

        if (isset($commonTypos[$domain])) {
            return $user.'@'.$commonTypos[$domain];
        }

        return null;
    }
}
