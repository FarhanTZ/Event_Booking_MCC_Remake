<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\LoginUserAction;
use App\Actions\Auth\RegisterUserAction;
use App\DataTransferObjects\LoginUserData;
use App\DataTransferObjects\RegisterUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Handle user login authentication.
     */
    public function login(LoginRequest $request, LoginUserAction $action): RedirectResponse
    {
        $dto = LoginUserData::fromRequest($request);
        $user = $action->execute($dto);

        if ($dto->redirectSlot) {
            return redirect('/booking/wizard?' . $dto->redirectSlot)
                ->with('success', "Selamat datang kembali, {$user->full_name}! Slot reservasi Anda siap dilanjutkan.");
        }

        return redirect()->intended(route('dashboard'))
            ->with('success', "Selamat datang kembali, {$user->full_name}!");
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

        // Auto-login user after successful registration
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
     * Handle user logout.
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
