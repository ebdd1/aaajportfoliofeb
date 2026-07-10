<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class SiteSettingController extends Controller
{
    public function edit(): Response
    {
        $settings = SiteSetting::getSingleton();

        // SECURITY: Don't expose secrets to frontend - only send safe fields
        return Inertia::render('Admin/Settings/Web', [
            'settings' => [
                'site_name' => $settings->site_name,
                'site_description' => $settings->site_description,
                'google_analytics_id' => $settings->google_analytics_id,
                'maintenance_mode' => $settings->maintenance_mode,
                'favicon_url' => $settings->favicon_url,
                'og_image_url' => $settings->og_image_url,
                'hero_background_url' => $settings->hero_background_url,
                'hero_opacity' => $settings->hero_opacity ?? 30,
                'hero_title_font' => $settings->hero_title_font ?? 'font-serif',
                'hero_title_color' => $settings->hero_title_color ?? '#3D3929',
                'hero_title_size' => $settings->hero_title_size ?? 'text-6xl',
                'hero_subtitle_font' => $settings->hero_subtitle_font ?? 'font-sans',
                'hero_subtitle_color' => $settings->hero_subtitle_color ?? '#8C8273',
                'hero_subtitle_size' => $settings->hero_subtitle_size ?? 'text-2xl',
                'hero_stat_value_font' => $settings->hero_stat_value_font ?? 'font-mono',
                'hero_stat_value_color' => $settings->hero_stat_value_color ?? '#3D3929',
                'hero_stat_label_font' => $settings->hero_stat_label_font ?? 'font-sans',
                'hero_stat_label_color' => $settings->hero_stat_label_color ?? '#8C8273',
                'hero_stat_card_bg_color' => $settings->hero_stat_card_bg_color ?? '#F5F1E8',
                'hero_stat_card_opacity' => $settings->hero_stat_card_opacity ?? 100,
                'hero_stat_card_border' => $settings->hero_stat_card_border ?? false,
                'hero_stat_card_border_color' => $settings->hero_stat_card_border_color ?? '#E8E2D3',
                'hero_stat_card_backdrop_blur' => $settings->hero_stat_card_backdrop_blur ?? false,
                // SEO fields
                'seo_meta_title' => $settings->seo_meta_title,
                'seo_meta_description' => $settings->seo_meta_description,
                'seo_canonical_url' => $settings->seo_canonical_url,
                'seo_robots' => $settings->seo_robots ?? 'index, follow',
                'seo_sitemap_include' => $settings->seo_sitemap_include ?? true,
                // Payment settings - don't expose secrets
                'pakasir_project' => $settings->pakasir_project,
                'has_api_key' => !empty($settings->pakasir_api_key),
                'has_webhook_secret' => !empty($settings->pakasir_webhook_secret),
                'pakasir_active' => $settings->pakasir_active,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
            'maintenance_mode' => ['boolean'],
        ]);

        SiteSetting::getSingleton()->update($validated);

        return redirect()
            ->route('admin.settings.web')
            ->with('success', 'Pengaturan web berhasil disimpan.');
    }

    public function updatePayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pakasir_project' => ['nullable', 'string', 'max:255'],
            'pakasir_api_key' => ['nullable', 'string', 'max:255'],
            'pakasir_webhook_secret' => ['nullable', 'string', 'max:255'],
            'pakasir_active' => ['boolean'],
        ]);

        if (empty($validated['pakasir_api_key'])) {
            $validated['pakasir_api_key'] = null;
            $validated['pakasir_webhook_secret'] = null;
        } elseif (empty($validated['pakasir_webhook_secret'])) {
            return back()->withErrors(['pakasir_webhook_secret' => 'Webhook secret diperlukan jika API key diisi.']);
        }

        if (empty($validated['pakasir_project'])) {
            $validated['pakasir_project'] = null;
        }

        if (empty($validated['pakasir_webhook_secret'])) {
            $validated['pakasir_webhook_secret'] = null;
        }

        SiteSetting::getSingleton()->update($validated);

        return redirect()
            ->route('admin.settings.web')
            ->with('success', 'Pengaturan pembayaran berhasil disimpan.');
    }

    public function updateFavicon(Request $request): RedirectResponse
    {
        $request->validate([
            'favicon' => ['required', 'image', 'max:512', 'mimes:png,jpg,webp'],
        ]);

        $setting = SiteSetting::getSingleton();

        if ($setting->favicon_path) {
            Storage::disk('public')->delete($setting->favicon_path);
        }

        $path = $request->file('favicon')->store('site', 'public');
        $setting->update(['favicon_path' => $path]);

        return redirect()
            ->route('admin.settings.web')
            ->with('success', 'Favicon berhasil diperbarui.');
    }

    public function updateOgImage(Request $request): RedirectResponse
    {
        $request->validate([
            'og_image' => ['required', 'image', 'max:2048', 'mimes:png,jpg,jpeg,webp'],
        ]);

        $setting = SiteSetting::getSingleton();

        if ($setting->og_image_path) {
            Storage::disk('public')->delete($setting->og_image_path);
        }

        $path = $request->file('og_image')->store('site', 'public');
        $setting->update(['og_image_path' => $path]);

        return redirect()
            ->route('admin.settings.web')
            ->with('success', 'OG Image berhasil diperbarui.');
    }

    public function updateHero(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_background' => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
            'hero_opacity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'hero_title_font' => ['nullable', 'string'],
            'hero_title_color' => ['nullable', 'string'],
            'hero_title_size' => ['nullable', 'string'],
            'hero_subtitle_font' => ['nullable', 'string'],
            'hero_subtitle_color' => ['nullable', 'string'],
            'hero_subtitle_size' => ['nullable', 'string'],
            'hero_stat_value_font' => ['nullable', 'string'],
            'hero_stat_value_color' => ['nullable', 'string'],
            'hero_stat_label_font' => ['nullable', 'string'],
            'hero_stat_label_color' => ['nullable', 'string'],
            'hero_stat_card_bg_color' => ['nullable', 'string'],
            'hero_stat_card_opacity' => ['nullable', 'integer', 'min:0', 'max:100'],
            'hero_stat_card_border' => ['nullable', 'boolean'],
            'hero_stat_card_border_color' => ['nullable', 'string'],
            'hero_stat_card_backdrop_blur' => ['nullable', 'boolean'],
        ]);

        $setting = SiteSetting::getSingleton();

        if ($request->hasFile('hero_background')) {
            if ($setting->hero_background_path) {
                Storage::disk('public')->delete($setting->hero_background_path);
            }
            $path = $request->file('hero_background')->store('site', 'public');
            $setting->update(['hero_background_path' => $path]);
        }

        if (isset($validated['hero_opacity'])) {
            $setting->update(['hero_opacity' => (int) $validated['hero_opacity']]);
        }
        
        $typographyFields = [
            'hero_title_font', 'hero_title_color', 'hero_title_size',
            'hero_subtitle_font', 'hero_subtitle_color', 'hero_subtitle_size',
            'hero_stat_value_font', 'hero_stat_value_color',
            'hero_stat_label_font', 'hero_stat_label_color',
            'hero_stat_card_bg_color', 'hero_stat_card_opacity', 'hero_stat_card_border',
            'hero_stat_card_border_color', 'hero_stat_card_backdrop_blur'
        ];
        
        foreach ($typographyFields as $field) {
            if (array_key_exists($field, $validated)) {
                $setting->update([$field => $validated[$field]]);
            }
        }

        return redirect()
            ->route('admin.settings.web')
            ->with('success', 'Pengaturan hero berhasil disimpan.');
    }

    public function deleteHeroBackground(): RedirectResponse
    {
        $setting = SiteSetting::getSingleton();

        if ($setting->hero_background_path) {
            Storage::disk('public')->delete($setting->hero_background_path);
            $setting->update(['hero_background_path' => null]);
        }

        return redirect()
            ->route('admin.settings.web')
            ->with('success', 'Background hero berhasil dihapus.');
    }

    public function updateSeo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'seo_meta_title' => ['nullable', 'string', 'max:255'],
            'seo_meta_description' => ['nullable', 'string', 'max:500'],
            'seo_canonical_url' => ['nullable', 'string', 'url', 'max:255'],
            'seo_robots' => ['nullable', 'string', 'max:100'],
            'seo_sitemap_include' => ['boolean'],
            'google_analytics_id' => ['nullable', 'string', 'max:50'],
        ]);

        SiteSetting::getSingleton()->update($validated);

        return redirect()
            ->route('admin.settings.web')
            ->with('success', 'Pengaturan SEO berhasil disimpan.');
    }
}
