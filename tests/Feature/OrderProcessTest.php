<?php

namespace Tests\Feature;

use App\Mail\CustomerOrderConfirmationMail;
use App\Mail\OrderReadyActivationMail;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderProcessTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_can_view_order_page(): void
    {
        $response = $this->get(route('order.create'));
        $response->assertStatus(200);
        $response->assertSee('Kustomisasi & Pesan Gantungan Kunci NFC');
        $response->assertSee('Pesan Online');
        $response->assertSee('Pesan Langsung (Offline)');
    }

    public function test_user_can_submit_online_order(): void
    {
        Mail::fake();

        $response = $this->post(route('order.store'), [
            'customer_name' => 'Fajar Pratama',
            'whatsapp' => '081299887766',
            'email' => 'fajar@example.com',
            'channel' => 'online',
            'delivery_method' => 'kirim',
            'address' => 'Jl. Boulevard Panakkukang No. 88, Makassar',
            'shape' => 'bulat',
            'size' => 'Standar (Rekomendasi)',
            'color' => 'Motif Toraja Pa\'ssulan',
            'quantity' => 2,
            'payment_method' => 'transfer_bank',
            'notes' => 'Tolong grafir inisial FP di belakang.',
        ]);

        $order = Order::where('customer_name', 'Fajar Pratama')->first();
        $this->assertNotNull($order);
        $this->assertEquals('online', $order->channel);
        $this->assertEquals('kirim', $order->delivery_method);
        $this->assertEquals(Order::STATUS_MENUNGGU_PEMBAYARAN, $order->status);
        $this->assertEquals(2, $order->quantity);
        $this->assertEquals((float) config('flexitag.unit_price', 55000) * 2, (float) $order->total);

        // Verify log created
        $this->assertTrue($order->statusLogs()->exists());

        // Verify customer received order confirmation email
        Mail::assertSent(CustomerOrderConfirmationMail::class, function ($mail) {
            return $mail->hasTo('fajar@example.com');
        });

        $response->assertRedirect(route('order.success', ['order_no' => $order->order_no]));
    }

    public function test_user_can_submit_offline_direct_order(): void
    {
        $response = $this->post(route('order.store'), [
            'customer_name' => 'Siti Nurhaliza',
            'whatsapp' => '085211223344',
            'email' => 'siti@example.com',
            'channel' => 'offline',
            'delivery_method' => 'ambil',
            'shape' => 'persegi',
            'size' => 'Standar (Rekomendasi)',
            'color' => 'Persegi Branding UMKM',
            'quantity' => 1,
            'payment_method' => 'tunai_offline',
            'notes' => 'Pesan langsung di kasir workshop.',
        ]);

        $order = Order::where('customer_name', 'Siti Nurhaliza')->first();
        $this->assertNotNull($order);
        $this->assertEquals('offline', $order->channel);
        $this->assertEquals('ambil', $order->delivery_method);
        $this->assertEquals('tunai_offline', $order->payment_method);

        $response->assertRedirect(route('order.success', ['order_no' => $order->order_no]));
    }

    public function test_customer_can_track_order_via_order_number(): void
    {
        $order = Order::create([
            'order_no' => 'ORD-2026-9999',
            'channel' => 'online',
            'customer_name' => 'Dewi Sartika',
            'whatsapp' => '081300009999',
            'email' => 'dewi@example.com',
            'delivery_method' => 'kirim',
            'address' => 'Makassar',
            'shape' => 'Ganci Bulat',
            'size' => 'Diameter 4.5 cm',
            'color' => 'Motif Bugis',
            'quantity' => 1,
            'unit_price' => 75000,
            'total' => 75000,
            'status' => Order::STATUS_DICETAK,
        ]);

        OrderStatusLog::create([
            'order_id' => $order->id,
            'status' => Order::STATUS_DICETAK,
            'changed_at' => now(),
            'note' => 'Akrilik sedang dalam antrean cetak UV.',
            'admin_id' => null,
        ]);

        $response = $this->get(route('order.track', ['order_no' => 'ORD-2026-9999']));
        $response->assertStatus(200);
        $response->assertSee('ORD-2026-9999');
        $response->assertSee('Dewi Sartika');
        $response->assertSee('4. Dicetak');
    }

    public function test_admin_can_record_manual_offline_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Walkin Customer Workshop',
            'whatsapp' => '081999888777',
            'email' => 'walkin@example.com',
            'channel' => 'offline',
            'delivery_method' => 'ambil',
            'shape' => 'Ganci Kapsul',
            'size' => 'Standar 4.5 cm',
            'color' => 'Motif Toraja',
            'quantity' => 3,
            'unit_price' => 75000,
            'payment_method' => 'tunai_offline',
            'status' => Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
            'notes' => 'Lunas bayar tunai di kasir.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $order = Order::where('customer_name', 'Walkin Customer Workshop')->first();
        $this->assertNotNull($order);
        $this->assertEquals('offline', $order->channel);
        $this->assertEquals(Order::STATUS_PEMBAYARAN_DIKONFIRMASI, $order->status);
        $this->assertEquals(225000.00, (float) $order->total);
    }

    public function test_admin_on_track_order_page_sees_admin_dashboard_link(): void
    {
        $admin = User::factory()->create(['name' => 'Saparuddin', 'role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('order.track'));

        $response->assertStatus(200);
        $response->assertSee(route('admin.dashboard'));
        $response->assertSee('Dashboard Admin');
        $response->assertDontSee('Buka Dashboard');
    }

    public function test_online_order_without_payment_method_defaults_to_midtrans(): void
    {
        $response = $this->post(route('order.store'), [
            'customer_name' => 'Rahmat Hidayat',
            'whatsapp' => '081234567890',
            'email' => 'rahmat@example.com',
            'channel' => 'online',
            'delivery_method' => 'kirim',
            'address' => 'Jl. Metro Tanjung Bunga, Makassar',
            'shape' => 'bulat',
            'size' => 'Standar (Rekomendasi)',
            'color' => 'Motif Toraja Pa\'ssulan',
            'quantity' => 1,
            // payment_method tidak dikirim dari form
        ]);

        $order = Order::where('customer_name', 'Rahmat Hidayat')->first();
        $this->assertNotNull($order);
        $this->assertEquals('online', $order->channel);
        $this->assertEquals('midtrans', $order->payment_method);
        $this->assertEquals('kirim', $order->delivery_method);

        $response->assertRedirect(route('order.success', ['order_no' => $order->order_no]));
    }

    public function test_offline_order_automatically_sets_delivery_ambil_and_tunai_payment(): void
    {
        $response = $this->post(route('order.store'), [
            'customer_name' => 'Budi Santoso Offline',
            'whatsapp' => '087711223344',
            'email' => 'budi@example.com',
            'channel' => 'offline',
            // delivery_method and payment_method tidak dikirim
            'shape' => 'persegi',
            'size' => 'Standar (Rekomendasi)',
            'color' => 'Persegi Branding UMKM',
            'quantity' => 1,
        ]);

        $order = Order::where('customer_name', 'Budi Santoso Offline')->first();
        $this->assertNotNull($order);
        $this->assertEquals('offline', $order->channel);
        $this->assertEquals('ambil', $order->delivery_method);
        $this->assertEquals('tunai_offline', $order->payment_method);
        $this->assertEquals('Ambil di Workshop FlexiTag Makassar', $order->address);

        $response->assertRedirect(route('order.success', ['order_no' => $order->order_no]));
    }

    public function test_customer_can_switch_payment_method_between_cash_and_online(): void
    {
        $order = Order::create([
            'order_no' => 'ORD-TEST-SWITCH',
            'channel' => 'online',
            'customer_name' => 'Switch Test User',
            'whatsapp' => '081233445566',
            'email' => 'switch@example.com',
            'delivery_method' => 'ambil',
            'shape' => 'bulat',
            'size' => 'Standar',
            'color' => 'Motif Toraja',
            'quantity' => 1,
            'unit_price' => 75000,
            'total' => 75000,
            'payment_method' => 'midtrans',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        // Switch to cash
        $responseCash = $this->postJson(route('order.select_cash', ['order_no' => $order->order_no]));
        $responseCash->assertStatus(200);
        $responseCash->assertJson(['success' => true, 'payment_method' => 'tunai_offline']);
        $this->assertEquals('tunai_offline', $order->fresh()->payment_method);

        // Switch back to online
        $responseOnline = $this->postJson(route('order.select_online', ['order_no' => $order->order_no]));
        $responseOnline->assertStatus(200);
        $responseOnline->assertJson(['success' => true, 'payment_method' => 'midtrans']);
        $this->assertEquals('midtrans', $order->fresh()->payment_method);
    }

    public function test_updating_status_to_pembayaran_dikonfirmasi_allocates_tags_and_sends_email(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        // Sediakan tag belum aktif
        Tag::create([
            'tag_code' => 'FLX-CONFIRM1',
            'activation_code' => 'KD-9988-ZZ',
            'status' => 'belum_aktif',
            'user_id' => null,
            'order_id' => null,
        ]);

        $order = Order::create([
            'order_no' => 'ORD-TEST-CONFIRM',
            'channel' => 'online',
            'customer_name' => 'Budi Testing',
            'whatsapp' => '081234567890',
            'email' => 'budi_confirm@example.com',
            'delivery_method' => 'kirim',
            'shape' => 'bulat',
            'size' => 'Standar',
            'color' => 'Motif Toraja',
            'quantity' => 1,
            'unit_price' => 75000,
            'total' => 75000,
            'payment_method' => 'midtrans',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.status', $order->id), [
            'status' => Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
            'note' => 'Pembayaran telah dicek dan valid.',
        ]);

        $response->assertRedirect();
        $this->assertEquals(Order::STATUS_PEMBAYARAN_DIKONFIRMASI, $order->fresh()->status);

        // Verifikasi tag teralokasi ke order
        $allocatedTag = Tag::where('order_id', $order->id)->first();
        $this->assertNotNull($allocatedTag);
        $this->assertNotEmpty($allocatedTag->activation_code);

        // Verifikasi email terkirim ke customer
        Mail::assertSent(OrderReadyActivationMail::class, function ($mail) use ($order) {
            return $mail->hasTo('budi_confirm@example.com')
                && $mail->order->id === $order->id;
        });
    }

    public function test_admin_can_resend_activation_email(): void
    {
        Mail::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        Tag::create([
            'tag_code' => 'FLX-RESEND01',
            'activation_code' => 'KD-7777-AA',
            'status' => 'belum_aktif',
            'user_id' => null,
            'order_id' => null,
        ]);

        $order = Order::create([
            'order_no' => 'ORD-2026-TESTRESEND',
            'channel' => 'online',
            'customer_name' => 'Rina Resend',
            'whatsapp' => '081234445555',
            'email' => 'rina_resend@example.com',
            'delivery_method' => 'ambil',
            'shape' => 'persegi',
            'size' => 'Standar',
            'color' => 'Batik Bugis',
            'quantity' => 1,
            'unit_price' => 75000,
            'total' => 75000,
            'payment_method' => 'tunai_offline',
            'status' => Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.resendActivation', $order->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        Mail::assertSent(OrderReadyActivationMail::class, function ($mail) use ($order) {
            return $mail->hasTo('rina_resend@example.com')
                && $mail->order->id === $order->id;
        });
    }

    public function test_order_success_page_renders_instagram_button_and_action_group(): void
    {
        $order = Order::create([
            'order_no' => 'ORD-2026-SUCCESSTEST',
            'channel' => 'online',
            'customer_name' => 'Fajar Instagram',
            'whatsapp' => '081234998877',
            'email' => 'fajar@example.com',
            'delivery_method' => 'ambil',
            'shape' => 'persegi',
            'size' => 'Standar',
            'color' => 'Hitam Emas',
            'quantity' => 1,
            'unit_price' => 55000,
            'total' => 55000,
            'payment_method' => 'tunai_offline',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        $response = $this->get(route('order.success', ['order_no' => $order->order_no]));

        $response->assertStatus(200);
        $response->assertSee('Hubungi via Instagram');
        $response->assertSee('instagram.com');
        $response->assertSee('Lacak Status Pesanan Ini');
        $response->assertSee('Kembali ke Beranda');
        // Pastikan tidak ada tombol dobel WA CS
        $response->assertDontSee('Konfirmasi via WhatsApp CS');
    }

    public function test_order_track_page_renders_responsive_structure(): void
    {
        $order = Order::create([
            'order_no' => 'ORD-2026-TRACKTEST',
            'channel' => 'online',
            'customer_name' => 'Andi Lacak',
            'whatsapp' => '081234112233',
            'email' => 'andi_lacak@example.com',
            'delivery_method' => 'ambil',
            'shape' => 'kapsul',
            'size' => 'Standar',
            'color' => 'Merah Emas',
            'quantity' => 2,
            'unit_price' => 75000,
            'total' => 150000,
            'payment_method' => 'midtrans',
            'status' => Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
        ]);

        Tag::create([
            'tag_code' => 'FLX-TRACK01',
            'activation_code' => 'KD-TRACK-99',
            'status' => 'belum_aktif',
            'user_id' => null,
            'order_id' => $order->id,
        ]);

        $response = $this->get(route('order.track', ['order_no' => $order->order_no]));

        $response->assertStatus(200);
        $response->assertSee('track-form-grid', false);
        $response->assertSee('order-specs-grid', false);
        $response->assertSee('timeline', false);
        $response->assertSee('ORD-2026-TRACKTEST');
        $response->assertSee('KODE AKTIVASI ANDA');
        $response->assertSee('KD-TRACK-99');
        $response->assertSee('Salin Kode');
        $response->assertSee('copyActivationCode');
        $response->assertSee('Hubungi via Instagram');
        $response->assertSee('instagram.com');
        $response->assertDontSee('Hubungi CS via WhatsApp');
    }
}
