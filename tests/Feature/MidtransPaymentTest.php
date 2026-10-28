<?php

namespace Tests\Feature;

use App\Models\FinancialEntry;
use App\Models\Order;
use App\Models\OrderStatusLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['midtrans.server_key' => 'Mid-server-test-key-12345']);
        config(['midtrans.client_key' => 'Mid-client-test-key-12345']);
        config(['midtrans.is_production' => false]);
    }

    public function test_midtrans_callback_with_valid_signature_marks_order_as_paid(): void
    {
        $order = Order::create([
            'order_no' => Order::generateOrderNo(),
            'channel' => 'online',
            'customer_name' => 'Budi Santoso',
            'whatsapp' => '081234567890',
            'email' => 'budi@example.com',
            'delivery_method' => 'ambil',
            'address' => 'Workshop FlexiTag',
            'shape' => 'bulat',
            'size' => 'Standar',
            'color' => 'Hitam Emas',
            'quantity' => 1,
            'unit_price' => 75000,
            'total' => 75000,
            'payment_method' => 'qris',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        $serverKey = 'Mid-server-test-key-12345';
        $statusCode = '200';
        $grossAmount = '75000.00';
        $signatureKey = hash('sha512', $order->order_no.$statusCode.$grossAmount.$serverKey);

        $payload = [
            'order_id' => $order->order_no,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_time' => now()->toDateTimeString(),
        ];

        $response = $this->postJson(route('midtrans.callback'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Notification handled successfully',
        ]);

        $order->refresh();
        $this->assertEquals(Order::STATUS_PEMBAYARAN_DIKONFIRMASI, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertEquals('qris', $order->payment_payload['payment_type']);

        // Assert OrderStatusLog exists
        $this->assertTrue(
            OrderStatusLog::where('order_id', $order->id)
                ->where('status', Order::STATUS_PEMBAYARAN_DIKONFIRMASI)
                ->exists()
        );

        // Assert FinancialEntry recorded
        $this->assertTrue(
            FinancialEntry::where('note', 'like', "%{$order->order_no}%")->exists()
        );
    }

    public function test_midtrans_callback_with_invalid_signature_is_rejected(): void
    {
        $order = Order::create([
            'order_no' => Order::generateOrderNo(),
            'channel' => 'online',
            'customer_name' => 'Hacker Test',
            'whatsapp' => '081234567890',
            'email' => 'hacker@example.com',
            'delivery_method' => 'ambil',
            'address' => 'Unknown',
            'shape' => 'bulat',
            'size' => 'Standar',
            'color' => 'Hitam',
            'quantity' => 1,
            'unit_price' => 75000,
            'total' => 75000,
            'payment_method' => 'qris',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        $payload = [
            'order_id' => $order->order_no,
            'status_code' => '200',
            'gross_amount' => '75000.00',
            'signature_key' => 'invalid-fake-signature',
            'transaction_status' => 'settlement',
        ];

        $response = $this->postJson(route('midtrans.callback'), $payload);

        $response->assertStatus(403);
        $response->assertJson(['message' => 'Invalid signature key']);

        $order->refresh();
        $this->assertEquals(Order::STATUS_MENUNGGU_PEMBAYARAN, $order->status);
    }

    public function test_midtrans_callback_with_expire_cancels_order(): void
    {
        $order = Order::create([
            'order_no' => Order::generateOrderNo(),
            'channel' => 'online',
            'customer_name' => 'Expired User',
            'whatsapp' => '081234567890',
            'email' => 'expired@example.com',
            'delivery_method' => 'ambil',
            'address' => 'Workshop',
            'shape' => 'bulat',
            'size' => 'Standar',
            'color' => 'Hitam',
            'quantity' => 1,
            'unit_price' => 75000,
            'total' => 75000,
            'payment_method' => 'transfer_bank',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        $serverKey = 'Mid-server-test-key-12345';
        $statusCode = '200';
        $grossAmount = '75000.00';
        $signatureKey = hash('sha512', $order->order_no.$statusCode.$grossAmount.$serverKey);

        $payload = [
            'order_id' => $order->order_no,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'expire',
            'payment_type' => 'bank_transfer',
        ];

        $response = $this->postJson(route('midtrans.callback'), $payload);

        $response->assertStatus(200);

        $order->refresh();
        $this->assertEquals(Order::STATUS_DIBATALKAN, $order->status);
    }

    public function test_check_status_endpoint_returns_payment_status(): void
    {
        $order = Order::create([
            'order_no' => Order::generateOrderNo(),
            'channel' => 'online',
            'customer_name' => 'Check Status User',
            'whatsapp' => '081234567890',
            'email' => 'check@example.com',
            'delivery_method' => 'ambil',
            'address' => 'Workshop',
            'shape' => 'bulat',
            'size' => 'Standar',
            'color' => 'Hitam',
            'quantity' => 1,
            'unit_price' => 75000,
            'total' => 75000,
            'payment_method' => 'qris',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        $response = $this->getJson(route('order.check_status', ['order_no' => $order->order_no]));

        $response->assertStatus(200);
        $response->assertJson([
            'order_no' => $order->order_no,
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
            'is_paid' => false,
        ]);

        $order->update(['status' => Order::STATUS_PEMBAYARAN_DIKONFIRMASI]);

        $response2 = $this->getJson(route('order.check_status', ['order_no' => $order->order_no]));
        $response2->assertStatus(200);
        $response2->assertJson([
            'order_no' => $order->order_no,
            'status' => Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
            'is_paid' => true,
        ]);
    }
}
