<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\FinancialEntry;
use App\Models\Order;
use App\Models\OrderStatusLog;
use App\Services\MidtransService;
use App\Services\OrderActivationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentCallbackController extends Controller
{
    /**
     * Handle incoming payment notification webhook from Midtrans.
     */
    public function handle(Request $request, MidtransService $midtransService): JsonResponse
    {
        $payload = $request->all();

        Log::info('Midtrans payment notification received:', $payload);

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');
        $paymentType = (string) ($payload['payment_type'] ?? 'midtrans');

        if (empty($orderId)) {
            return response()->json(['message' => 'Missing order_id'], 400);
        }

        // Verify SHA512 signature key
        if (! $midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning("Midtrans callback signature verification failed for Order #{$orderId}");

            return response()->json(['message' => 'Invalid signature key'], 403);
        }

        $order = Order::where('order_no', $orderId)->first();

        if (! $order) {
            Log::warning("Midtrans callback received for non-existent order #{$orderId}");

            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::transaction(function () use ($order, $transactionStatus, $fraudStatus, $paymentType, $payload): void {
            // Check success statuses: settlement or accepted capture
            $isPaid = ($transactionStatus === 'settlement')
                || ($transactionStatus === 'capture' && $fraudStatus === 'accept');

            if ($isPaid) {
                // If order not yet confirmed, confirm it now
                if ($order->status === Order::STATUS_MENUNGGU_PEMBAYARAN) {
                    $order->update([
                        'status' => Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
                        'paid_at' => now(),
                        'payment_payload' => $payload,
                    ]);

                    OrderStatusLog::create([
                        'order_id' => $order->id,
                        'status' => Order::STATUS_PEMBAYARAN_DIKONFIRMASI,
                        'changed_at' => now(),
                        'note' => 'Pembayaran otomatis terkonfirmasi melalui Midtrans ('.strtoupper($paymentType).').',
                        'admin_id' => null,
                    ]);

                    FinancialEntry::recordOrderPayment($order);

                    // Otomatis alokasikan tag random dan kirimkan email aktivasi ke pemesan
                    try {
                        app(OrderActivationService::class)->processOrderConfirmation($order);
                    } catch (\Throwable $e) {
                        Log::error("Gagal mengirim email aktivasi via Midtrans callback order #{$order->order_no}: ".$e->getMessage());
                    }

                    AuditLog::record('payment_confirmed', "Pembayaran pesanan [{$order->order_no}] otomatis terverifikasi via Midtrans Callback.");
                } else {
                    // Update payload and paid_at if not set
                    $order->update([
                        'paid_at' => $order->paid_at ?? now(),
                        'payment_payload' => $payload,
                    ]);
                }
            } elseif (in_array($transactionStatus, ['cancel', 'expire', 'deny'], true)) {
                if ($order->status === Order::STATUS_MENUNGGU_PEMBAYARAN) {
                    $order->update([
                        'status' => Order::STATUS_DIBATALKAN,
                        'payment_payload' => $payload,
                    ]);

                    OrderStatusLog::create([
                        'order_id' => $order->id,
                        'status' => Order::STATUS_DIBATALKAN,
                        'changed_at' => now(),
                        'note' => "Pembayaran dibatalkan / kadaluarsa melalui Midtrans ({$transactionStatus}).",
                        'admin_id' => null,
                    ]);

                    AuditLog::record('payment_cancelled', "Pembayaran pesanan [{$order->order_no}] dibatalkan/kadaluarsa via Midtrans ({$transactionStatus}).");
                }
            } elseif ($transactionStatus === 'pending') {
                $order->update([
                    'payment_payload' => $payload,
                ]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Notification handled successfully',
        ]);
    }
}
