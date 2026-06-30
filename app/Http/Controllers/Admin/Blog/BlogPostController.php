<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\StoreBlogPostRequest;
use App\Http\Requests\Blog\UpdateBlogPostRequest;
use App\Http\Resources\Blog\BlogPostResource;
use App\Models\Blog\Category;
use App\Models\Blog\Post;
use App\Models\Blog\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    public function index(Request $request): Response
    {
        $posts = Post::with(['author', 'categories', 'tags'])
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Blog/Posts/Index', [
            'posts' => $posts,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return Inertia::render('Admin/Blog/Posts/Create', [
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $validated['author_id'] = auth()->id();
        $validated['is_featured'] = $request->boolean('is_featured');

        $post = Post::create($validated);

        if (!empty($validated['category_ids'])) {
            $post->categories()->attach($validated['category_ids']);
        }

        if (!empty($validated['tag_ids'])) {
            $post->tags()->attach($validated['tag_ids']);
        }

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Post berhasil ditambahkan');
    }

    public function edit(Post $post): Response
    {
        $post->load(['categories', 'tags']);
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return Inertia::render('Admin/Blog/Posts/Edit', [
            'post' => new BlogPostResource($post),
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function update(UpdateBlogPostRequest $request, Post $post): RedirectResponse
    {
        $validated = $request->validated();

        if (isset($validated['slug']) && $validated['slug'] !== $post->slug) {
            $validated['slug'] = Str::slug($validated['slug']);
        }

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $post->update($validated);

        $post->categories()->sync($validated['category_ids'] ?? []);
        $post->tags()->sync($validated['tag_ids'] ?? []);

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Post berhasil diperbarui');
    }

    public function destroy(Post $post): RedirectResponse
    {
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->categories()->detach();
        $post->tags()->detach();
        $post->delete();

        return redirect()->route('admin.blog.posts.index')
            ->with('success', 'Post berhasil dihapus');
    }

    public function toggleFeatured(Post $post): RedirectResponse
    {
        $post->update(['is_featured' => !$post->is_featured]);

        return redirect()->back()
            ->with('success', $post->is_featured ? 'Post ditambahkan ke featured' : 'Post dihapus dari featured');
    }

    public function toggleStatus(Post $post): RedirectResponse
    {
        if ($post->status === 'draft') {
            $post->publish();
        } else {
            $post->unpublish();
        }

        return redirect()->back()
            ->with('success', $post->isPublished() ? 'Post berhasil dipublikasikan' : 'Post berhasil di-unpublish');
    }
}
