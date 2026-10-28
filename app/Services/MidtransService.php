<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        $this->configure();
    }

    /**
     * Set up Midtrans environment and configuration.
     */
    public function configure(): void
    {
        Config::$serverKey = (string) config('midtrans.server_key');
        Config::$clientKey = (string) config('midtrans.client_key');
        Config::$isProduction = (bool) config('midtrans.is_production', false);
        Config::$isSanitized = (bool) config('midtrans.is_sanitized', true);
        Config::$is3ds = (bool) config('midtrans.is_3ds', true);

        // Workaround for local dev SSL certificate / PHP 8.3 array merge issues in Midtrans SDK
        Config::$curlOptions = [
            CURLOPT_HTTPHEADER => [],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ];
    }

    /**
     * Check if Midtrans keys are present.
     */
    public function isConfigured(): bool
    {
        return ! empty(config('midtrans.server_key')) && ! empty(config('midtrans.client_key'));
    }

    /**
     * Generate Midtrans Snap token for a given order.
     */
    public function createSnapToken(Order $order): ?string
    {
        if (! $this->isConfigured()) {
            Log::warning("Midtrans is not configured. Skipping Snap token generation for Order #{$order->order_no}");

            return null;
        }

        try {
            $this->configure();

            $grossAmount = (int) round((float) $order->total);

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_no,
                    'gross_amount' => $grossAmount,
                ],
                'item_details' => [
                    [
                        'id' => 'ITEM-FT-'.$order->id,
                        'price' => (int) round((float) $order->unit_price),
                        'quantity' => (int) $order->quantity,
                        'name' => mb_strimwidth('FlexiTag '.ucfirst($order->shape).' ('.$order->size.')', 0, 50, '...'),
                    ],
                ],
                'customer_details' => [
                    'first_name' => $order->customer_name,
                    'email' => $order->email,
                    'phone' => $order->whatsapp,
                    'shipping_address' => [
                        'first_name' => $order->customer_name,
                        'phone' => $order->whatsapp,
                        'address' => mb_strimwidth((string) $order->address, 0, 190, '...'),
                    ],
                ],
            ];

            $token = Snap::getSnapToken($params);

            return $token;
        } catch (Exception $e) {
            Log::error("Midtrans Snap Token generation error for Order #{$order->order_no}: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return null;
        }
    }

    /**
     * Verify Midtrans notification signature key.
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $serverKey = (string) config('midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }
}
