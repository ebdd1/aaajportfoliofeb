<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('hero_stat_card_bg_color')->nullable();
            $table->integer('hero_stat_card_opacity')->nullable();
            $table->boolean('hero_stat_card_border')->default(false);
            $table->string('hero_stat_card_border_color')->nullable();
            $table->boolean('hero_stat_card_backdrop_blur')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_stat_card_bg_color',
                'hero_stat_card_opacity',
                'hero_stat_card_border',
                'hero_stat_card_border_color',
                'hero_stat_card_backdrop_blur',
            ]);
        });
    }
};