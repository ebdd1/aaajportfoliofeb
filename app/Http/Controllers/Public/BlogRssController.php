<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Blog\Post;
use Illuminate\Http\Response;

class BlogRssController extends Controller
{
    public function __invoke(): Response
    {
        $posts = Post::published()
            ->with(['author', 'categories', 'tags'])
            ->orderByDesc('published_at')
            ->limit(20)
            ->get();

        $siteUrl = config('app.url');
        $siteName = 'Febryanus Tambing';
        $siteDescription = 'Blog tentang programming, teknologi, dan pengembangan web';

        return response()->view('blog.rss', [
            'posts' => $posts,
            'siteUrl' => $siteUrl,
            'siteName' => $siteName,
            'siteDescription' => $siteDescription,
        ], 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }
}
