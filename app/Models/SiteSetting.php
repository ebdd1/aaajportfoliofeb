<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'site_description',
        'google_analytics_id',

        // SEO Fields
        'seo_meta_title',
        'seo_meta_description',
        'seo_canonical_url',
        'seo_robots',
        'seo_sitemap_include',

        'favicon_path',
        'og_image_path',
        'hero_background_path',
        'hero_opacity',
        'maintenance_mode',
        'pakasir_project',
        'pakasir_api_key',
        'pakasir_webhook_secret',
        'pakasir_active',
        'hero_title_font',
        'hero_title_color',
        'hero_title_size',
        'hero_subtitle_font',
        'hero_subtitle_color',
        'hero_subtitle_size',
        'hero_stat_value_font',
        'hero_stat_value_color',
        'hero_stat_label_font',
        'hero_stat_label_color',
        'hero_stat_card_bg_color',
        'hero_stat_card_opacity',
        'hero_stat_card_border',
        'hero_stat_card_border_color',
        'hero_stat_card_backdrop_blur',
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean',
        'pakasir_active' => 'boolean',
        'hero_opacity' => 'integer',
        'hero_stat_card_opacity' => 'integer',
        'hero_stat_card_border' => 'boolean',
        'hero_stat_card_backdrop_blur' => 'boolean',
    ];

    protected $hidden = [
        'pakasir_api_key',
        'pakasir_webhook_secret',
    ];

    protected static function booted(): void
    {
        static::updated(fn () => static::clearCache());
        static::created(fn () => static::clearCache());
    }

    public static function getSingleton(): self
    {
        try {
            $cached = Cache::get('site_setting_singleton');

            if ($cached instanceof self) {
                return $cached;
            }

            // Cache is corrupted or not instance of SiteSetting - clear it
            Cache::forget('site_setting_singleton');
        } catch (\Throwable $e) {
            // Cache read failed - clear corrupted entry
            Cache::forget('site_setting_singleton');
        }

        $setting = self::first();

        if ($setting) {
            Cache::put('site_setting_singleton', $setting, 3600);
            return $setting;
        }

        return self::create([
            'site_name' => 'Portfolio',
            'site_description' => '',
            'maintenance_mode' => false,
            'pakasir_active' => false,
            'hero_title_font' => 'font-serif',
            'hero_title_color' => '#3D3929',
            'hero_title_size' => 'text-6xl',
            'hero_subtitle_font' => 'font-sans',
            'hero_subtitle_color' => '#8C8273',
            'hero_subtitle_size' => 'text-2xl',
            'hero_stat_value_font' => 'font-mono',
            'hero_stat_value_color' => '#3D3929',
            'hero_stat_label_font' => 'font-sans',
            'hero_stat_label_color' => '#8C8273',
            'hero_stat_card_bg_color' => '#F5F1E8',
            'hero_stat_card_opacity' => 100,
            'hero_stat_card_border' => false,
            'hero_stat_card_border_color' => '#E8E2D3',
            'hero_stat_card_backdrop_blur' => false,
        ]);
    }

    public static function clearCache(): void
    {
        Cache::forget('site_setting_singleton');
    }

    public function getFaviconUrlAttribute(): ?string
    {
        return $this->favicon_path
            ? asset('storage/' . $this->favicon_path)
            : null;
    }

    public function getOgImageUrlAttribute(): ?string
    {
        return $this->og_image_path
            ? asset('storage/' . $this->og_image_path)
            : null;
    }

    public function getHeroBackgroundUrlAttribute(): ?string
    {
        return $this->hero_background_path
            ? asset('storage/' . $this->hero_background_path)
            : null;
    }
}
