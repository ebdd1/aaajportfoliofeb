<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->active()
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount(['products' => fn($q) => $q->active()])
            ->orderBy('display_order')
            ->get();

        $featuredProducts = Product::with('category')
            ->active()
            ->featured()
            ->limit(4)
            ->get();

        return Inertia::render('Public/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'filters' => $request->only(['search', 'category']),
            'socialLinks' => SocialLink::active()->get(),
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::with('category')
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $relatedProducts = Product::with('category')
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return Inertia::render('Public/Products/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'socialLinks' => SocialLink::active()->get(),
        ]);
    }
}
