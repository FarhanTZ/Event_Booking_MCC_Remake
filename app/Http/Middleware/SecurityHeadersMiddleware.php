<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and apply OWASP Security Headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // 1. Anti-Clickjacking: Mencegah web di-embed pada iframe web phising lain
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Anti-MIME Sniffing: Mencegah browser mengeksekusi file non-executable sebagai executable
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. XSS Filter Protection (Legacy browser support)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Referrer Policy: Melindungi kebocoran path URL internal ke domain luar
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Permissions Policy: Membatasi akses hardware yang tidak dibutuhkan (kamera, mikrofon otomatis)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // 6. Force HTTPS HSTS jika dijalankan pada protokol secure HTTPS
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }
}
