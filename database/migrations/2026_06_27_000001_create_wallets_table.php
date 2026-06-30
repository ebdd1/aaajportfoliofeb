<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->enum('type', ['bank', 'ewallet', 'cash', 'savings'])->default('cash');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 3)->default('IDR');
            $table->string('color', 7)->default('#6b7280');
            $table->string('icon', 50)->default('WalletIcon');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
