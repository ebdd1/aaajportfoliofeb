<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('price_locked_amount')->nullable()->after('total_amount');
            $table->timestamp('price_locked_at')->nullable()->after('price_locked_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['price_locked_amount', 'price_locked_at']);
        });
    }
};
