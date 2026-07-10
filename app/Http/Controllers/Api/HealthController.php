<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PakasirService;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function __construct(
        protected PakasirService $pakasir
    ) {}

    public function check(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'app' => 'portfolio-febryanus',
        ]);
    }

    public function pakasir(): JsonResponse
    {
        $isConfigured = $this->pakasir->isConfigured();

        if (!$isConfigured) {
            return response()->json([
                'status' => 'error',
                'configured' => false,
                'message' => 'Pakasir not configured',
            ], 503);
        }

        return response()->json([
            'status' => 'ok',
            'configured' => true,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
