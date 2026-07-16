<?php

namespace App\Http\Controllers;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $sitemap = Sitemap::create();

        // Always add static pages first (doesn't need DB)
        $this->addStaticPages($sitemap);

        // Try to add dynamic content, but don't fail if DB is down
        $this->addDynamicContent($sitemap);

        return $sitemap->toResponse(request());
    }

    protected function addStaticPages(Sitemap $sitemap): void
    {
        $sitemap->add(Url::create('/')->setLastModificationDate(now())->setChangeFrequency('weekly')->setPriority(1.0));
        $sitemap->add(Url::create('/products')->setLastModificationDate(now())->setChangeFrequency('daily')->setPriority(0.9));
        $sitemap->add(Url::create('/blog')->setLastModificationDate(now())->setChangeFrequency('daily')->setPriority(0.9));
        $sitemap->add(Url::create('/cv/download')->setLastModificationDate(now())->setChangeFrequency('monthly')->setPriority(0.8));
    }

    protected function addDynamicContent(Sitemap $sitemap): void
    {
        try {
            // Only add dynamic content if we can connect to DB
            $this->addBlogContent($sitemap);
            $this->addProjectContent($sitemap);
            $this->addProductContent($sitemap);
            $this->addCertificateContent($sitemap);
        } catch (\Throwable $e) {
            Log::warning('Sitemap: DB unavailable, serving static pages only', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function addBlogContent(Sitemap $sitemap): void
    {
        $posts = \App\Models\Blog\Post::published()
            ->whereNotNull('published_at')
            ->get();

        foreach ($posts as $post) {
            $sitemap->add(Url::create("/blog/{$post->slug}")->setLastModificationDate($post->updated_at)->setChangeFrequency('weekly')->setPriority(0.8));
        }

        foreach (\App\Models\Blog\Category::all() as $category) {
            $sitemap->add(Url::create("/blog?category={$category->slug}")->setLastModificationDate($category->updated_at)->setChangeFrequency('weekly')->setPriority(0.6));
        }

        foreach (\App\Models\Blog\Tag::all() as $tag) {
            $sitemap->add(Url::create("/blog?tag={$tag->slug}")->setLastModificationDate($tag->updated_at)->setChangeFrequency('weekly')->setPriority(0.5));
        }
    }

    protected function addProjectContent(Sitemap $sitemap): void
    {
        $projects = \App\Models\Project::where('is_active', true)->get();

        foreach ($projects as $project) {
            $sitemap->add(Url::create("/#proyek")->setLastModificationDate($project->updated_at)->setChangeFrequency('monthly')->setPriority(0.6));
        }
    }

    protected function addProductContent(Sitemap $sitemap): void
    {
        $products = \App\Models\Product::where('is_active', true)->get();

        foreach ($products as $product) {
            $sitemap->add(Url::create("/products/{$product->slug}")->setLastModificationDate($product->updated_at)->setChangeFrequency('weekly')->setPriority(0.7));
        }
    }

    protected function addCertificateContent(Sitemap $sitemap): void
    {
        $certificates = \App\Models\Certificate::where('is_active', true)->get();

        foreach ($certificates as $certificate) {
            $sitemap->add(Url::create("/certificates/{$certificate->id}")->setLastModificationDate($certificate->updated_at)->setChangeFrequency('yearly')->setPriority(0.5));
        }
    }
}
