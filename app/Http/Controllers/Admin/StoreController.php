<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class StoreController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', Order::STATUS_PENDING)->count(),
            'completed_orders' => Order::where('status', Order::STATUS_PAID)->count(),
            'total_revenue' => Order::where('status', Order::STATUS_PAID)->sum('total_amount'),
            'this_month_revenue' => Order::where('status', Order::STATUS_PAID)
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('total_amount'),
        ];

        $recentOrders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $topProducts = Product::withCount(['orderItems'])
            ->orderBy('order_items_count', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Store/Index', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    }

    public function orders(): Response
    {
        $orders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Store/Orders', [
            'orders' => $orders,
        ]);
    }

    public function showOrder(Order $order): Response
    {
        $order->load(['user', 'items.product']);

        return Inertia::render('Admin/Store/OrderDetail', [
            'order' => $order,
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,paid,cancelled,expired',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()
            ->route('admin.store.orders.show', $order->id)
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
