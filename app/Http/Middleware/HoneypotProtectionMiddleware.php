<?php

namespace App\Http\Middleware;

use App\Services\SecurityAuditLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HoneypotProtectionMiddleware
{
    /**
     * Field name used as a honeypot trap.
     * Real humans will not see or fill this field, but automated spam bots will.
     */
    protected const HONEYPOT_FIELD = 'mcc_security_trap';

    /**
     * Handle an incoming request and silently discard spam bot submissions.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && $request->filled(self::HONEYPOT_FIELD)) {
            Log::channel('single')->alert("Security Audit [HONEYPOT_TRAP_TRIGGERED]: Bot spam blocked.", [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'trap_value' => $request->input(self::HONEYPOT_FIELD),
                'target_url' => $request->fullUrl(),
                'timestamp' => now()->toIso8601String(),
            ]);

            // Return fake success or abort to trap and waste bot's time without database insertion
            return redirect('/')->with('success', 'Permintaan Anda telah diproses.');
        }

        return $next($request);
    }
}
