<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_product_metrics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('portfolio_products')->cascadeOnDelete();
            $table->string('metric_key', 50);
            $table->string('metric_label', 100);
            $table->decimal('value', 15, 2);
            $table->string('unit', 50)->nullable();
            $table->date('recorded_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'metric_key', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_product_metrics');
    }
};
