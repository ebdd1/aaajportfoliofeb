<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('pakasir_project')->nullable()->after('og_image_path');
            $table->string('pakasir_api_key')->nullable()->after('pakasir_project');
            $table->boolean('pakasir_active')->default(false)->after('pakasir_api_key');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['pakasir_project', 'pakasir_api_key', 'pakasir_active']);
        });
    }
};
