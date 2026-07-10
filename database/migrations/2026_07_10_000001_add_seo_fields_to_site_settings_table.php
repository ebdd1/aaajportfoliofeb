<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('seo_meta_title')->nullable()->after('google_analytics_id');
            $table->text('seo_meta_description')->nullable()->after('seo_meta_title');
            $table->string('seo_canonical_url')->nullable()->after('seo_meta_description');
            $table->string('seo_robots')->default('index, follow')->after('seo_canonical_url');
            $table->boolean('seo_sitemap_include')->default(true)->after('seo_robots');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'seo_meta_title',
                'seo_meta_description',
                'seo_canonical_url',
                'seo_robots',
                'seo_sitemap_include',
            ]);
        });
    }
};
