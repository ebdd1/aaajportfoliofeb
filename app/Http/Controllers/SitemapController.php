<?php

namespace App\Http\Controllers;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Project;
use App\Models\Certificate;
use App\Models\Product;
use App\Models\Blog\Post;
use App\Models\Blog\Category;
use App\Models\Blog\Tag;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;

class SitemapController extends Controller
{
    public function __invoke()
    {
        // Check if sitemap is enabled in settings
        $sitemapEnabled = true;
        try {
            $settings = SiteSetting::getSingleton();
            $sitemapEnabled = $settings->seo_sitemap_include ?? true;
        } catch (\Throwable $e) {
            Log::warning('Sitemap: Could not load site settings', ['error' => $e->getMessage()]);
        }

        if (!$sitemapEnabled) {
            return response()->xml(['error' => 'Sitemap disabled'], 404);
        }

        $sitemap = Sitemap::create();

        // Static pages
        $sitemap->add(Url::create('/')->setLastModificationDate(now())->setChangeFrequency('weekly')->setPriority(1.0));
        $sitemap->add(Url::create('/products')->setLastModificationDate(now())->setChangeFrequency('daily')->setPriority(0.9));
        $sitemap->add(Url::create('/blog')->setLastModificationDate(now())->setChangeFrequency('daily')->setPriority(0.9));
        $sitemap->add(Url::create('/cv/download')->setLastModificationDate(now())->setChangeFrequency('monthly')->setPriority(0.8));

        try {
            // Blog posts
            Post::published()->whereNotNull('published_at')->get()->each(function ($post) use ($sitemap) {
                $sitemap->add(Url::create("/blog/{$post->slug}")->setLastModificationDate($post->updated_at)->setChangeFrequency('weekly')->setPriority(0.8));
            });

            // Blog categories
            Category::all()->each(function ($category) use ($sitemap) {
                $sitemap->add(Url::create("/blog?category={$category->slug}")->setLastModificationDate($category->updated_at)->setChangeFrequency('weekly')->setPriority(0.6));
            });

            // Blog tags
            Tag::all()->each(function ($tag) use ($sitemap) {
                $sitemap->add(Url::create("/blog?tag={$tag->slug}")->setLastModificationDate($tag->updated_at)->setChangeFrequency('weekly')->setPriority(0.5));
            });

            // Projects
            Project::where('is_active', true)->get()->each(function ($project) use ($sitemap) {
                $sitemap->add(Url::create("/#proyek")->setLastModificationDate($project->updated_at)->setChangeFrequency('monthly')->setPriority(0.6));
            });

            // Products
            Product::where('is_active', true)->get()->each(function ($product) use ($sitemap) {
                $sitemap->add(Url::create("/products/{$product->slug}")->setLastModificationDate($product->updated_at)->setChangeFrequency('weekly')->setPriority(0.7));
            });

            // Certificates
            Certificate::where('is_active', true)->get()->each(function ($certificate) use ($sitemap) {
                $sitemap->add(Url::create("/certificates/{$certificate->id}")->setLastModificationDate($certificate->updated_at)->setChangeFrequency('yearly')->setPriority(0.5));
            });
        } catch (\Throwable $e) {
            Log::warning('Sitemap: Some DB queries failed', ['error' => $e->getMessage()]);
        }

        return $sitemap->toResponse(request());
    }
}
