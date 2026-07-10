<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PakasirWebhookController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\Auth\RegisterApiController;
use App\Http\Controllers\Api\Auth\LoginApiController;
use App\Http\Controllers\Api\Auth\LogoutApiController;
use App\Http\Controllers\Api\Auth\MeApiController;

/*
|--------------------------------------------------------------------------
| API Routes - No CSRF protection
|--------------------------------------------------------------------------
*/

// ─── HEALTH CHECK ─────────────────────────────────────────────────────────────
Route::get('/health', [HealthController::class, 'check'])->name('api.health');
Route::get('/health/pakasir', [HealthController::class, 'pakasir'])->name('api.health.pakasir');

// ─── PAKASIR WEBHOOK ───────────────────────────────────────────────────────────
Route::post('/pakasir/webhook', [PakasirWebhookController::class, 'handle'])
    ->middleware('throttle:60,1')
    ->name('pakasir.webhook');

// ─── AUTH API ───────────────────────────────────────────────────────────────────
Route::prefix('auth')->middleware('throttle:5,1')->group(function () {
    // Public auth routes - rate limited: 5 attempts per minute
    Route::post('/register', RegisterApiController::class)->name('api.auth.register');
    Route::post('/login', LoginApiController::class)->name('api.auth.login');

    // Protected auth routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', MeApiController::class)->name('api.auth.me');
        Route::post('/logout', LogoutApiController::class)->name('api.auth.logout');
    });
});