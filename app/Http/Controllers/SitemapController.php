<?php

namespace App\Http\Controllers;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $sitemap = Sitemap::create();

        // Minimal: just static pages
        $sitemap->add(Url::create('/')->setLastModificationDate(now()));
        $sitemap->add(Url::create('/products')->setLastModificationDate(now()));
        $sitemap->add(Url::create('/blog')->setLastModificationDate(now()));

        return $sitemap->toResponse(request());
    }
}
