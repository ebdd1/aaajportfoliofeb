<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $maintenanceEnabled = SiteSetting::getSingleton()->maintenance_mode;

        if (!$maintenanceEnabled) {
            return $next($request);
        }

        // Allow admin users to bypass
        if ($request->user()?->isAdmin()) {
            return $next($request);
        }

        // Allow login/register pages
        if ($request->is('login') || $request->is('register') || $request->is('forgot-password')) {
            return $next($request);
        }

        // Show maintenance page
        return response()->view('maintenance', [
            'message' => 'Portfolio sedang dalam maintenance. Silakan kembali nanti.',
        ], 503);
    }
}
