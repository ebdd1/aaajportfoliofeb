<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->enum('type', ['income', 'expense', 'both'])->default('expense');
            $table->string('icon', 50)->default('TagIcon');
            $table->string('color', 7)->default('#6b7280');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_categories');
    }
};
