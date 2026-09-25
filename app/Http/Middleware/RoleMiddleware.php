<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request and ensure the authenticated user has one of the required roles.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user is suspended
        if ($user->isSuspended()) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda dinonaktifkan oleh administrator MCC.']);
        }

        $userRoleValue = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        // Check if user has one of the allowed roles
        if (! in_array($userRoleValue, $roles, true)) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki hak akses untuk membuka modul ini.');
        }

        return $next($request);
    }
}
