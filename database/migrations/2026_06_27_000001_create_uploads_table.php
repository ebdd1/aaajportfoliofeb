<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // File info
            $table->string('filename', 255);
            $table->string('original_filename', 255);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->string('file_hash', 64)->index();

            // Storage
            $table->string('disk', 50)->default('local');
            $table->string('path', 500);

            // Upload metadata
            $table->string('upload_type', 50)->default('certificate'); // certificate, avatar, document, product
            $table->string('ip_address', 45)->nullable();

            // Processing status
            $table->string('status', 50)->default('pending')->index(); // pending, processing, completed, failed
            $table->string('ocr_status', 50)->default('pending')->index(); // pending, processing, completed, failed
            $table->string('virus_scan_status', 50)->default('pending')->index(); // pending, scanning, clean, infected

            // OCR results (JSON)
            $table->json('ocr_result')->nullable();

            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['user_id', 'upload_type']);
            $table->index(['disk', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
