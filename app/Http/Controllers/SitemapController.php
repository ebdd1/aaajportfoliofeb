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

class SitemapController extends Controller
{
    public function __invoke()
    {
        $settings = SiteSetting::getSingleton();

        // Check if sitemap inclusion is enabled
        if (!$settings->seo_sitemap_include) {
            return response()->xml([
                'error' => 'Sitemap inclusion disabled',
            ], 404);
        }

        $sitemap = Sitemap::create()
            ->add(Url::create('/')
                ->setLastModificationDate(now())
                ->setChangeFrequency('weekly')
                ->setPriority(1.0))
            ->add(Url::create('/products')
                ->setLastModificationDate(now())
                ->setChangeFrequency('daily')
                ->setPriority(0.9))
            ->add(Url::create('/blog')
                ->setLastModificationDate(now())
                ->setChangeFrequency('daily')
                ->setPriority(0.9))
            ->add(Url::create('/cv/download')
                ->setLastModificationDate(now())
                ->setChangeFrequency('monthly')
                ->setPriority(0.8));

        // Add blog posts to sitemap
        $posts = Post::published()
            ->whereNotNull('published_at')
            ->get();
        foreach ($posts as $post) {
            $sitemap->add(Url::create("/blog/{$post->slug}")
                ->setLastModificationDate($post->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.8));
        }

        // Add blog categories to sitemap
        $categories = Category::all();
        foreach ($categories as $category) {
            $sitemap->add(Url::create("/blog?category={$category->slug}")
                ->setLastModificationDate($category->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.6));
        }

        // Add blog tags to sitemap
        $tags = Tag::all();
        foreach ($tags as $tag) {
            $sitemap->add(Url::create("/blog?tag={$tag->slug}")
                ->setLastModificationDate($tag->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.5));
        }

        // Add projects to sitemap
        $projects = Project::where('is_published', true)->get();
        foreach ($projects as $project) {
            $sitemap->add(Url::create("/#proyek")
                ->setLastModificationDate($project->updated_at)
                ->setChangeFrequency('monthly')
                ->setPriority(0.6));
        }

        // Add products to sitemap
        $products = Product::where('is_active', true)->get();
        foreach ($products as $product) {
            $sitemap->add(Url::create("/products/{$product->slug}")
                ->setLastModificationDate($product->updated_at)
                ->setChangeFrequency('weekly')
                ->setPriority(0.7));
        }

        // Add certificates to sitemap
        $certificates = Certificate::where('is_published', true)->get();
        foreach ($certificates as $certificate) {
            $sitemap->add(Url::create("/certificates/{$certificate->id}")
                ->setLastModificationDate($certificate->updated_at)
                ->setChangeFrequency('yearly')
                ->setPriority(0.5));
        }

        return $sitemap->toResponse(request());
    }
}
