<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Profile;
use App\Models\SocialLink;
use App\Services\PakasirService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function __construct(
        protected PakasirService $pakasir
    ) {}

    private function getSharedData(): array
    {
        $profile = Profile::getSingleton();
        $socialLinks = SocialLink::where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return [
            'profile' => $profile ? [
                'name' => $profile->name,
                'tagline' => $profile->tagline,
                'email' => $profile->email,
            ] : null,
            'socialLinks' => $socialLinks->map(fn ($link) => [
                'id' => $link->id,
                'platform' => $link->platform,
                'url' => $link->url,
            ]),
        ];
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->active()->firstOrFail();

        // Secure: Require authentication for checkout
        if (!auth()->check()) {
            return redirect()->route('login')->with('intended', route('checkout.show', $slug));
        }

        return Inertia::render('Public/Checkout', array_merge([
            'product' => $product,
        ], $this->getSharedData()));
    }

    public function initiate(Request $request, string $slug): RedirectResponse
    {
        // Secure: Require authentication
        if (!auth()->check()) {
            return redirect()->route('login')->with('intended', route('checkout.show', $slug));
        }

        $request->validate([
            'payment_method' => 'required|in:qris,bni_va,bri_va,mandiri_va',
        ]);

        $product = Product::where('slug', $slug)->active()->firstOrFail();

        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $product->price,
            'status' => Order::STATUS_PENDING,
            'payment_method' => $request->payment_method,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'price_at_purchase' => $product->price,
        ]);

        $order->lockPrice();

        $payment = $this->pakasir->createTransaction(
            $request->payment_method,
            $order->order_id,
            $order->total_amount
        );

        if (empty($payment)) {
            $order->markAsCancelled();
            return back()->with('error', 'Gagal membuat transaksi. Coba lagi.');
        }

        $order->update([
            'payment_token' => $payment['payment_number'] ?? null,
            'payment_number' => $payment['payment_number'] ?? null,
            'expired_at' => isset($payment['expired_at']) ? now()->parse($payment['expired_at']) : now()->addHours(24),
        ]);

        return Inertia::render('Public/Payment', array_merge([
            'order' => $order,
            'payment' => $payment,
        ], $this->getSharedData()));
    }

    public function retryPayment(Order $order): RedirectResponse
    {
        // Secure: Require authentication
        if (!auth()->check() || $order->user_id !== auth()->id()) {
            return redirect()->route('login');
        }

        // Only allow retry for pending orders
        if (!$order->isPending()) {
            return redirect()->route('orders.show', $order->order_id)
                ->with('error', 'Tidak dapat retry untuk order yang sudah ' . $order->getStatusLabel());
        }

        // Check if expired
        if ($order->expired_at && $order->expired_at->isPast()) {
            return redirect()->route('orders.show', $order->order_id)
                ->with('error', 'Order sudah kadaluarsa. Silakan buat order baru.');
        }

        // Create new transaction with Pakasir
        $payment = $this->pakasir->createTransaction(
            $order->payment_method,
            $order->order_id,
            $order->total_amount
        );

        if (empty($payment)) {
            return back()->with('error', 'Gagal membuat transaksi. Coba lagi.');
        }

        // Update order with new payment details
        $order->update([
            'payment_token' => $payment['payment_number'] ?? null,
            'payment_number' => $payment['payment_number'] ?? null,
            'expired_at' => isset($payment['expired_at']) ? now()->parse($payment['expired_at']) : now()->addHours(24),
        ]);

        return redirect()->route('payment.show', $order);
    }

    public function payment(Order $order)
    {
        // Secure: Require authentication
        if (!auth()->check() || $order->user_id !== auth()->id()) {
            return redirect()->route('login');
        }

        $payment = null;
        if ($order->payment_number) {
            $payment = [
                'payment_number' => $order->payment_number,
                'amount' => $order->total_amount,
                'fee' => 0,
                'total_payment' => $order->total_amount,
                'payment_method' => $order->payment_method ?? 'qris',
            ];
        }

        return Inertia::render('Public/Payment', array_merge([
            'order' => $order,
            'payment' => $payment,
        ], $this->getSharedData()));
    }
}
