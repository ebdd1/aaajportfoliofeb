<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PakasirService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        protected PakasirService $pakasir
    ) {}

    public function index()
    {
        $orders = Order::with(['user', 'items.product'])
            ->latest()
            ->paginate(20);

        return inertia('Admin/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);

        return inertia('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function simulate(Order $order): RedirectResponse
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'Hanya order dengan status pending yang bisa di-simulasi.');
        }

        if (!$this->pakasir->isConfigured()) {
            return back()->with('error', 'Pakasir belum dikonfigurasi.');
        }

        Log::channel('admin')->info('Admin simulating payment', [
            'admin_id' => auth()->id(),
            'order_id' => $order->order_id,
            'amount' => $order->total_amount,
            'timestamp' => now()->toIso8601String(),
        ]);

        $payload = [
            'amount' => $order->total_amount,
            'order_id' => $order->order_id,
            'project' => config('services.pakasir.project'),
            'status' => 'completed',
            'payment_method' => 'qris',
            'completed_at' => now()->toIso8601String(),
        ];

        $webhookUrl = config('app.url') . '/api/pakasir/webhook';
        $secret = config('services.pakasir.webhook_secret');

        $signature = hash_hmac('sha256', json_encode($payload), $secret);

        Http::withHeaders(['X-Pakasir-Signature' => $signature])
            ->post($webhookUrl, $payload);

        return redirect()
            ->route('admin.orders.show', $order->id)
            ->with('success', 'Simulasi pembayaran berhasil! Order ditandai sebagai lunas.');
    }

    public function markAsPaid(Order $order): RedirectResponse
    {
        if ($order->status === Order::STATUS_PAID) {
            return back()->with('error', 'Order sudah lunas.');
        }

        Log::channel('admin')->info('Admin marking order as paid', [
            'admin_id' => auth()->id(),
            'order_id' => $order->order_id,
            'previous_status' => $order->status,
            'timestamp' => now()->toIso8601String(),
        ]);

        $order->markAsPaid('manual');

        return back()->with('success', 'Order ditandai sebagai lunas.');
    }

    public function markAsCancelled(Order $order): RedirectResponse
    {
        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'Hanya order pending yang bisa dibatalkan.');
        }

        Log::channel('admin')->info('Admin cancelling order', [
            'admin_id' => auth()->id(),
            'order_id' => $order->order_id,
            'timestamp' => now()->toIso8601String(),
        ]);

        $order->markAsCancelled();

        return back()->with('success', 'Order dibatalkan.');
    }
}
