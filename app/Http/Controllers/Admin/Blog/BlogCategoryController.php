<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreBlogCategoryRequest;
use App\Http\Requests\Blog\UpdateBlogCategoryRequest;
use App\Models\Blog\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = Category::withCount(['posts' => fn($q) => $q->published()])
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Blog/Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(StoreBlogCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Category::create($validated);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(UpdateBlogCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        if (isset($validated['slug']) && $validated['slug'] !== $category->slug) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $category->update($validated);

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->posts()->detach();
        $category->delete();

        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
