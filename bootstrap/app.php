<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Custom error pages for Inertia
        $exceptions->render(function (\Illuminate\Http\Exceptions\HttpResponseException $e, Request $request) {
            // Let Inertia handle the response
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => '404 - Tidak ada halaman disini'], 404);
            }

            return Inertia::render('Error/404', [
                'status' => 404,
            ])->toResponse($request)->setStatusCode(404);
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => '403 - Akses ditolak'], 403);
            }

            return Inertia::render('Error/404', [
                'status' => 403,
                'message' => '403 - Akses ditolak',
            ])->toResponse($request)->setStatusCode(403);
        });

        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => '401 - Tidak terotorisasi'], 401);
            }

            return Inertia::render('Error/404', [
                'status' => 401,
                'message' => '401 - Tidak terotorisasi',
            ])->toResponse($request)->setStatusCode(401);
        });
    })->create();
