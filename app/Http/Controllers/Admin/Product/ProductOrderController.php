<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductOrderRequest;
use App\Models\Product\Product;
use App\Models\Product\ProductOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductOrder::with('product');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $products = Product::orderBy('name')->get();

        return inertia('Admin/Products/Orders/Index', [
            'orders' => $orders,
            'products' => $products,
            'filters' => $request->only(['product_id', 'status']),
        ]);
    }

    public function show(ProductOrder $order): Response
    {
        $order->load(['product', 'invoice']);

        return inertia('Admin/Products/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function store(StoreProductOrderRequest $request): RedirectResponse
    {
        ProductOrder::create($request->validated());

        return redirect()
            ->route('admin.products.orders.index')
            ->with('success', 'Order berhasil dibuat.');
    }

    public function updateStatus(Request $request, ProductOrder $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:new,in_discussion,in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'agreed_price' => 'nullable|numeric|min:0',
        ]);

        $data = ['status' => $request->status];

        if ($request->filled('notes')) {
            $data['notes'] = $request->notes;
        }

        if ($request->filled('agreed_price')) {
            $data['agreed_price'] = $request->agreed_price;
        }

        $order->update($data);

        return redirect()
            ->back()
            ->with('success', 'Status order berhasil diperbarui.');
    }

    public function destroy(ProductOrder $order): RedirectResponse
    {
        $order->delete();

        return redirect()
            ->route('admin.products.orders.index')
            ->with('success', 'Order berhasil dihapus.');
    }
}
