<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleSecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add headers to HTML responses
        if (!$this->shouldAddHeaders($response)) {
            return $response;
        }

        // Content Security Policy
        $response->headers->set(
            'Content-Security-Policy',
            $this->getContentSecurityPolicy()
        );

        // HTTP Strict Transport Security (force HTTPS)
        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains; preload'
        );

        // Referrer Policy
        $response->headers->set(
            'Referrer-Policy',
            'strict-origin-when-cross-origin'
        );

        // X-XSS-Protection (legacy but still useful for older browsers)
        $response->headers->set(
            'X-XSS-Protection',
            '1; mode=block'
        );

        // Permissions Policy (disable unnecessary browser features)
        $response->headers->set(
            'Permissions-Policy',
            'accelerometer=(), camera=(), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()'
        );

        return $response;
    }

    /**
     * Determine if headers should be added to this response.
     */
    protected function shouldAddHeaders(Response $response): bool
    {
        // Only add to HTML responses
        $contentType = $response->headers->get('Content-Type', '');
        return str_contains($contentType, 'text/html');
    }

    /**
     * Get Content Security Policy.
     */
    protected function getContentSecurityPolicy(): string
    {
        $policy = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tailwindcss.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.tailwindcss.com",
            "img-src 'self' data: https: blob:",
            "font-src 'self' https://fonts.gstatic.com https://fonts.googleapis.com",
            "connect-src 'self' https://fonts.googleapis.com https://fonts.gstatic.com",
            "frame-ancestors 'self'",
            "form-action 'self'",
            "base-uri 'self'",
            "object-src 'none'",
        ];

        return implode('; ', $policy);
    }
}
