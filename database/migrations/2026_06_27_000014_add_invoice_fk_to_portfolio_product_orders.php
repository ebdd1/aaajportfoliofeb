<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_product_orders', function (Blueprint $table): void {
            $table->foreign('invoice_id')->references('id')->on('invoices')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_product_orders', function (Blueprint $table): void {
            $table->dropForeign(['invoice_id']);
        });
    }
};
