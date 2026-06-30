<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('from_wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->foreignId('to_wallet_id')->constrained('wallets')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->index(['from_wallet_id', 'date']);
            $table->index(['to_wallet_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
