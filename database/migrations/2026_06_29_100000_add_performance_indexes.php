<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Orders table indexes for better query performance
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status', 'idx_orders_status');
            $table->index('expired_at', 'idx_orders_expired_at');
            $table->index(['status', 'expired_at'], 'idx_orders_status_expired');
        });

        // Messages table for unread query optimization
        Schema::table('messages', function (Blueprint $table) {
            $table->index('is_read', 'idx_messages_is_read');
        });

        // Portfolio products for public listing queries
        Schema::table('portfolio_products', function (Blueprint $table) {
            $table->index('status', 'idx_portfolio_products_status');
            $table->index('is_public', 'idx_portfolio_products_is_public');
            $table->index(['status', 'is_public'], 'idx_portfolio_products_status_public');
        });

        // Invoices for payment queries
        Schema::table('invoices', function (Blueprint $table) {
            $table->index('status', 'idx_invoices_status');
            $table->index('paid_at', 'idx_invoices_paid_at');
        });

        // Transactions for finance queries
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('type', 'idx_transactions_type');
            $table->index('transaction_date', 'idx_transactions_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_expired_at');
            $table->dropIndex('idx_orders_status_expired');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('idx_messages_is_read');
        });

        Schema::table('portfolio_products', function (Blueprint $table) {
            $table->dropIndex('idx_portfolio_products_status');
            $table->dropIndex('idx_portfolio_products_is_public');
            $table->dropIndex('idx_portfolio_products_status_public');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_status');
            $table->dropIndex('idx_invoices_paid_at');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_type');
            $table->dropIndex('idx_transactions_date');
        });
    }
};