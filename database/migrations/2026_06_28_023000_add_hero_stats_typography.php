<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('hero_stat_value_font')->nullable();
            $table->string('hero_stat_value_color')->nullable();
            
            $table->string('hero_stat_label_font')->nullable();
            $table->string('hero_stat_label_color')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_stat_value_font',
                'hero_stat_value_color',
                'hero_stat_label_font',
                'hero_stat_label_color',
            ]);
        });
    }
};