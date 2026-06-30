<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('cvs', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('social_links', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('skills', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('cvs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('social_links', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
