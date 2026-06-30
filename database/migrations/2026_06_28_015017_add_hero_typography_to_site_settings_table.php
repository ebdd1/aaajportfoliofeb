<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('hero_title_font')->nullable();
            $table->string('hero_title_color')->nullable();
            $table->string('hero_title_size')->nullable();
            
            $table->string('hero_subtitle_font')->nullable();
            $table->string('hero_subtitle_color')->nullable();
            $table->string('hero_subtitle_size')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title_font',
                'hero_title_color',
                'hero_title_size',
                'hero_subtitle_font',
                'hero_subtitle_color',
                'hero_subtitle_size',
            ]);
        });
    }
};