<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\WebhookLog;
use App\Services\PakasirService;
use App\Notifications\OrderStatusUpdatedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PakasirWebhookController extends Controller
{
    public function __construct(
        protected PakasirService $pakasir
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Pakasir webhook received', [
            'order_id' => $payload['order_id'] ?? null,
            'status' => $payload['status'] ?? null,
            'ip' => $request->ip(),
        ]);

        $signature = $request->header('X-Pakasir-Signature');
        $secret = config('services.pakasir.webhook_secret');

        if (empty($secret)) {
            Log::error('Pakasir webhook: webhook_secret not configured');
            return response()->json(['error' => 'Server configuration error'], 500);
        }

        if (empty($signature)) {
            Log::warning('Pakasir webhook: Missing signature', ['ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        if (!hash_equals($expected, $signature)) {
            Log::warning('Pakasir webhook: Invalid signature', [
                'ip' => $request->ip(),
                'signature_prefix' => substr($signature, 0, 10) . '...',
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $signatureHash = hash('sha256', $signature);

        if (WebhookLog::isProcessed($signatureHash)) {
            Log::info('Pakasir webhook: Already processed (replay attack)', [
                'signature_hash' => substr($signatureHash, 0, 16) . '...',
            ]);
            return response()->json(['message' => 'Already processed']);
        }

        $orderId = $payload['order_id'] ?? null;
        $status = $payload['status'] ?? null;
        $paymentMethod = $payload['payment_method'] ?? null;

        if (!$orderId || !$status) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $order = Order::where('order_id', $orderId)->first();

        if (!$order) {
            Log::warning('Order not found for webhook', ['order_id' => $orderId]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        if ($order->status === Order::STATUS_PAID) {
            WebhookLog::logWebhook($orderId, $signatureHash, $status, $payload, $request->ip());
            return response()->json(['message' => 'Already processed']);
        }

        if ($status === 'completed') {
            $webhookAmount = $payload['amount'] ?? null;

            if ($webhookAmount !== $order->total_amount) {
                Log::warning('Pakasir webhook: Amount mismatch', [
                    'order_id' => $orderId,
                    'expected_amount' => $order->total_amount,
                    'webhook_amount' => $webhookAmount,
                ]);
                return response()->json(['error' => 'Amount mismatch'], 400);
            }

            $order->markAsPaid($paymentMethod);
            Log::info('Order marked as paid', ['order_id' => $orderId]);
            $order->user->notify(new OrderStatusUpdatedNotification($order));
        } elseif ($status === 'expired') {
            $order->markAsExpired();
            Log::info('Order marked as expired', ['order_id' => $orderId]);
            $order->user->notify(new OrderStatusUpdatedNotification($order));
        } elseif ($status === 'cancelled') {
            $order->markAsCancelled();
            Log::info('Order marked as cancelled', ['order_id' => $orderId]);
            $order->user->notify(new OrderStatusUpdatedNotification($order));
        }

        WebhookLog::logWebhook($orderId, $signatureHash, $status, $payload, $request->ip());

        return response()->json(['message' => 'Webhook processed']);
    }
}
