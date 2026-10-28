<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\Order;
use App\Models\Tag;
use App\Models\User;
use App\Services\OrderActivationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * 1. Pastikan setiap ada user yang melakukan order baru masuk ke notifikasi admin.
     */
    public function test_submitting_online_order_creates_admin_drawer_notification(): void
    {
        $response = $this->post(route('order.store'), [
            'customer_name' => 'Budi Santoso',
            'whatsapp' => '081234567890',
            'email' => 'budi@example.com',
            'channel' => 'online',
            'delivery_method' => 'kirim',
            'address' => 'Jl. Pettarani No. 10, Makassar',
            'shape' => 'persegi',
            'size' => 'Standar',
            'color' => 'Motif Bugis Sulapa Eppa',
            'quantity' => 2,
            'payment_method' => 'transfer_bank',
            'notes' => 'Tolong pengerjaan rapi.',
        ]);

        $response->assertStatus(302);

        $order = Order::where('customer_name', 'Budi Santoso')->latest()->first();
        $this->assertNotNull($order);

        // Verifikasi notifikasi tersimpan di tabel messages
        $notification = Message::where('type', Message::TYPE_NEW_ORDER)
            ->where('order_id', $order->id)
            ->first();

        $this->assertNotNull($notification);
        $this->assertEquals('Budi Santoso', $notification->sender_name);
        $this->assertStringContainsString((string) $order->order_no, $notification->location);
        $this->assertNull($notification->read_at);
        $this->assertNotNull($notification->action_url);
    }

    /**
     * 2. Pastikan user yang login dapat mengirimkan saran/laporan kendala dan masuk ke notifikasi admin.
     */
    public function test_user_can_submit_feedback_and_it_appears_in_admin_notifications(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'name' => 'Nur Azizah',
            'email' => 'azizah@example.com',
            'no_whatsapp' => '085211223344',
        ]);

        $response = $this->actingAs($user)->post(route('user.settings.feedback'), [
            'category' => 'saran',
            'message' => 'Mohon ditambahkan pilihan warna akrilik transparan glow in the dark.',
            'contact' => '085211223344',
        ]);

        $response->assertRedirect(route('user.settings'));
        $response->assertSessionHas('status');

        $feedback = Message::where('type', Message::TYPE_USER_FEEDBACK)
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($feedback);
        $this->assertEquals('Nur Azizah', $feedback->sender_name);
        $this->assertStringContainsString('Saran', $feedback->location);
        $this->assertEquals('Mohon ditambahkan pilihan warna akrilik transparan glow in the dark.', $feedback->message);
        $this->assertNull($feedback->read_at);
    }

    /**
     * 3. Pastikan jika jumlah tag yang belum diaktivasi < 10, muncul notifikasi stok kritis ke admin.
     */
    public function test_low_tag_stock_triggers_admin_low_stock_notification(): void
    {
        // Bersihkan tag belum aktif sementara dalam transaksi ini
        Tag::where('status', 'belum_aktif')->whereNull('user_id')->delete();

        // Buat hanya 5 tag belum aktif (< 10)
        for ($i = 1; $i <= 5; $i++) {
            Tag::create([
                'tag_code' => "FLX-TEST{$i}",
                'activation_code' => "KD-111{$i}-AB",
                'status' => 'belum_aktif',
                'user_id' => null,
                'order_id' => null,
            ]);
        }

        $activationService = app(OrderActivationService::class);
        $remaining = $activationService->checkAndNotifyLowStock(forceSend: true);

        $this->assertEquals(5, $remaining);

        $alert = Message::where('type', Message::TYPE_LOW_STOCK)
            ->whereNull('read_at')
            ->first();

        $this->assertNotNull($alert);
        $this->assertStringContainsString('5 unit', $alert->message);
        $this->assertStringContainsString('generate', $alert->action_url);
    }

    /**
     * 4. Pastikan admin dapat menandai semua notifikasi sebagai telah dibaca.
     */
    public function test_admin_can_mark_all_notifications_as_read(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_test@flexitag.id',
        ]);

        // Buat beberapa notifikasi unread
        Message::create([
            'type' => Message::TYPE_NEW_ORDER,
            'message' => 'Pesanan uji coba 1',
            'created_at' => now(),
        ]);

        Message::create([
            'type' => Message::TYPE_USER_FEEDBACK,
            'message' => 'Saran uji coba 2',
            'created_at' => now(),
        ]);

        $this->assertTrue(Message::adminNotifications()->unread()->count() >= 2);

        $response = $this->actingAs($admin)->post(route('admin.messages.readAll'));
        $response->assertStatus(302);

        // Setelah tandai semua dibaca, tidak boleh ada unread
        $unreadCount = Message::adminNotifications()->unread()->count();
        $this->assertEquals(0, $unreadCount);
    }
}
