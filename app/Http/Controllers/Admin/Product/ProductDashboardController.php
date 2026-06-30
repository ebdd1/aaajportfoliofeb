<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Finance\Invoice;
use App\Models\Product\Product;
use App\Models\Product\ProductOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductDashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $products = Product::withCount(['orders', 'roadmapItems'])
            ->with(['orders' => function ($query) {
                $query->where('status', '!=', 'cancelled');
            }])
            ->orderBy('status')
            ->orderBy('name')
            ->get();

        $productsSummary = $products->map(function ($product) {
            $activeOrders = $product->orders->whereIn('status', ['new', 'in_discussion', 'in_progress'])->count();
            $totalRevenue = $product->orders
                ->where('status', 'completed')
                ->sum('agreed_price');

            return [
                'id' => $product->id,
                'name' => $product->name,
                'status' => $product->status,
                'status_label' => $product->status_label,
                'type' => $product->type,
                'total_orders' => $product->orders_count,
                'active_orders' => $activeOrders,
                'total_revenue' => $totalRevenue,
                'roadmap_items_count' => $product->roadmap_items_count,
            ];
        });

        $newOrders = ProductOrder::with('product')
            ->where('status', 'new')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentOrders = ProductOrder::with('product')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pipelineByStatus = ProductOrder::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $revenueThisMonth = Invoice::paid()
            ->whereBetween('paid_at', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->sum('total');

        return inertia('Admin/Products/Dashboard', [
            'productsSummary' => $productsSummary,
            'newOrders' => $newOrders,
            'recentOrders' => $recentOrders,
            'pipelineByStatus' => [
                'new' => $pipelineByStatus['new'] ?? 0,
                'in_discussion' => $pipelineByStatus['in_discussion'] ?? 0,
                'in_progress' => $pipelineByStatus['in_progress'] ?? 0,
                'completed' => $pipelineByStatus['completed'] ?? 0,
                'cancelled' => $pipelineByStatus['cancelled'] ?? 0,
            ],
            'stats' => [
                'active_products' => $products->where('status', 'active')->count(),
                'new_orders_count' => $newOrders->count(),
                'revenue_this_month' => $revenueThisMonth,
            ],
        ]);
    }
}
