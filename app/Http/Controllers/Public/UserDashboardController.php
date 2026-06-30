<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserDashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'paid_orders' => Order::where('user_id', $user->id)
                ->where('status', Order::STATUS_PAID)
                ->count(),
            'pending_orders' => Order::where('user_id', $user->id)
                ->where('status', Order::STATUS_PENDING)
                ->count(),
        ];

        $recentOrders = Order::with(['items.product'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($order) => [
                'id' => $order->id,
                'order_id' => $order->order_id,
                'short_order_id' => $order->short_order_id,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'created_at' => $order->created_at,
                'paid_at' => $order->paid_at,
                'items' => $order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'price_at_purchase' => $item->price_at_purchase,
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'slug' => $item->product->slug,
                    ] : null,
                ]),
            ]);

        return Inertia::render('User/Dashboard', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
