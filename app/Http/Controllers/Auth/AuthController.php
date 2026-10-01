<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\DataTransferObjects\LoginUserData;
use App\DataTransferObjects\RegisterUserData;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin(Request $request): View
    {
        return view('auth.login');
    }

    /**
     * Handle user login authentication with Anti Brute-Force Rate Limiting.
     */
    public function login(LoginRequest $request, LoginUserAction $action): RedirectResponse
    {
        // 1. Guard Anti Brute-Force (Throttle Check)
        $request->ensureIsNotRateLimited();

        try {
            $dto = LoginUserData::fromRequest($request);
            $user = $action->execute($dto);

            // 2. Clear rate limiter on successful authentication
            $request->clearRateLimiter();

            if ($dto->redirectSlot) {
                return redirect('/booking/wizard?' . $dto->redirectSlot)
                    ->with('success', "Selamat datang kembali, {$user->full_name}! Slot reservasi Anda siap dilanjutkan.");
            }

            if ($user->role === UserRole::MARKETING) {
                return redirect()->intended(route('marketing.dashboard'))
                    ->with('success', "Selamat datang di Portal Kurasi & Marketing, {$user->full_name}!");
            }

            if ($user->role === UserRole::FRONT_OFFICE) {
                return redirect()->intended(route('front_office.manifest'))
                    ->with('success', "Selamat datang di Manifes Front Office, {$user->full_name}!");
            }

            if ($user->role === UserRole::DUTY_OFFICER) {
                return redirect()->intended(route('duty_officer.dashboard'))
                    ->with('success', "Selamat datang di Dasbor Tugas Duty Officer, {$user->full_name}!");
            }

            return redirect()->intended(route('dashboard'))
                ->with('success', "Selamat datang kembali, {$user->full_name}!");
        } catch (ValidationException $e) {
            // 3. Increment failed attempts counter
            $request->hitRateLimiter();
            throw $e;
        }
    }

    /**
     * Show the registration page.
     */
    public function showRegister(Request $request): View
    {
        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request, RegisterUserAction $action): RedirectResponse
    {
        $dto = RegisterUserData::fromRequest($request);
        $user = $action->execute($dto);

        // Auto-login user after successful registration with fresh session
        Auth::login($user);
        $request->session()->regenerate();

        if ($request->filled('redirect_slot')) {
            return redirect('/booking/wizard?' . $request->input('redirect_slot'))
                ->with('success', 'Pendaftaran berhasil! Silakan lengkapi formulir reservasi Anda.');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Pendaftaran berhasil! Selamat datang di Portal Reservasi MCC.');
    }

    /**
     * Handle user logout securely.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}
