<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_product_orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('portfolio_products')->cascadeOnDelete();
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone')->nullable();
            $table->string('client_company')->nullable();
            $table->enum('status', ['new', 'in_discussion', 'in_progress', 'completed', 'cancelled'])->default('new');
            $table->text('notes')->nullable();
            $table->decimal('quoted_price', 15, 2)->nullable();
            $table->decimal('agreed_price', 15, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_product_orders');
    }
};
