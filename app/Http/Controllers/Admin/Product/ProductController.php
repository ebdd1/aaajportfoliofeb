<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::withCount(['orders', 'roadmapItems']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $products = $query->orderBy('display_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return inertia('Admin/Products/Catalog/Index', [
            'products' => $products,
            'filters' => $request->only(['status', 'type', 'search']),
        ]);
    }

    public function create(): Response
    {
        return inertia('Admin/Products/Catalog/Create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $existingSlug = Product::where('slug', $data['slug'])->exists();
        if ($existingSlug) {
            $data['slug'] = $data['slug'] . '-' . time();
        }

        $maxOrder = Product::max('display_order') ?? 0;
        $data['display_order'] = $maxOrder + 1;

        Product::create($data);

        return redirect()
            ->route('admin.products.catalog.index')
            ->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product): Response
    {
        return inertia('Admin/Products/Catalog/Edit', [
            'product' => $product,
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['name']) && $data['name'] !== $product->name) {
            $newSlug = Str::slug($data['name']);
            $existingSlug = Product::where('slug', $newSlug)->where('id', '!=', $product->id)->exists();
            if ($existingSlug) {
                $newSlug = $newSlug . '-' . time();
            }
            $data['slug'] = $newSlug;
        }

        $product->update($data);

        return redirect()
            ->route('admin.products.catalog.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('admin.products.catalog.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function togglePublic(Product $product): RedirectResponse
    {
        $product->update(['is_public' => !$product->is_public]);

        $status = $product->is_public ? 'ditampilkan' : 'disembunyikan';

        return redirect()
            ->back()
            ->with('success', "Produk berhasil {$status} dari halaman publik.");
    }
}
