<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreBlogTagRequest;
use App\Http\Requests\Blog\UpdateBlogTagRequest;
use App\Models\Blog\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogTagController extends Controller
{
    public function index(Request $request): Response
    {
        $tags = Tag::withCount(['posts' => fn($q) => $q->published()])
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Blog/Tags/Index', [
            'tags' => $tags,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(StoreBlogTagRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        Tag::create($validated);

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag berhasil ditambahkan');
    }

    public function update(UpdateBlogTagRequest $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validated();

        if (isset($validated['slug']) && $validated['slug'] !== $tag->slug) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        $tag->update($validated);

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag berhasil diperbarui');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->posts()->detach();
        $tag->delete();

        return redirect()->route('admin.blog.tags.index')
            ->with('success', 'Tag berhasil dihapus');
    }
}
