<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Blog\Category;
use App\Models\Blog\Post;
use App\Models\Blog\Tag;
use App\Models\Profile;
use App\Models\SocialLink;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
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

    public function index(Request $request): Response
    {
        $featuredPosts = Post::published()
            ->featured()
            ->with(['author', 'categories'])
            ->limit(3)
            ->get();

        $posts = Post::published()
            ->with(['author', 'categories', 'tags'])
            ->when($request->category, fn($q) => $q->whereHas('categories', fn($cq) => $cq->where('slug', $request->category)))
            ->when($request->tag, fn($q) => $q->whereHas('tags', fn($tq) => $tq->where('slug', $request->tag)))
            ->orderByDesc('published_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::withCount(['posts' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get();

        $tags = Tag::withCount(['posts' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get();

        return Inertia::render('Public/Blog/Index', array_merge([
            'posts' => $posts,
            'featuredPosts' => $featuredPosts,
            'categories' => $categories,
            'tags' => $tags,
            'filters' => [
                'category' => $request->category,
                'tag' => $request->tag,
            ],
        ], $this->getSharedData()));
    }

    public function show(string $slug): Response
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->with(['author', 'categories', 'tags'])
            ->firstOrFail();

        $post->incrementViews();

        $relatedPosts = Post::published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', fn($q) => $q->whereIn('blog_categories.id', $post->categories->pluck('id')))
            ->with(['author', 'categories'])
            ->limit(3)
            ->get();

        return Inertia::render('Public/Blog/Show', array_merge([
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ], $this->getSharedData()));
    }
}
