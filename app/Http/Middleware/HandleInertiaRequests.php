<?php

namespace App\Http\Middleware;

use App\Models\Product\ProductOrder;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * The current SEO settings.
     */
    protected ?array $seoSettings = null;

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Get SEO settings from SiteSetting.
     */
    protected function getSeoSettings(): array
    {
        if ($this->seoSettings === null) {
            $settings = SiteSetting::getSingleton();
            $this->seoSettings = [
                'seo_meta_title' => $settings->seo_meta_title,
                'seo_meta_description' => $settings->seo_meta_description,
                'seo_canonical_url' => $settings->seo_canonical_url,
                'seo_robots' => $settings->seo_robots ?? 'index, follow',
                'og_image_url' => $settings->og_image_url,
            ];
        }

        return $this->seoSettings;
    }

    /**
     * Define the props that are shared by default.
     */
    public function share(Request $request): array
    {
        $unreadOrdersCount = 0;
        if ($request->user()?->isAdmin()) {
            $unreadOrdersCount = ProductOrder::where('status', 'new')->count();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'unread_orders_count' => $unreadOrdersCount,
            'seo' => $this->getSeoSettings(),
        ];
    }
}
