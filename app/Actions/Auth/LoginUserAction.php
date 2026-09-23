<?php

namespace App\Actions\Auth;

use App\DataTransferObjects\LoginUserData;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginUserAction
{
    /**
     * Execute user authentication, session regeneration, and suspension invariant check.
     *
     * @throws ValidationException
     */
    public function execute(LoginUserData $data): User
    {
        $credentials = [
            'email' => $data->email,
            'password' => $data->password,
        ];

        if (! Auth::attempt($credentials, $data->remember)) {
            throw ValidationException::withMessages([
                'email' => ['Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        // Guard: Check if user is suspended by admin
        if ($user->isSuspended()) {
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();

            $reason = $user->suspension_reason ? ": {$user->suspension_reason}" : '.';
            throw ValidationException::withMessages([
                'email' => ["Akun Anda telah dinonaktifkan oleh administrator MCC{$reason}"],
            ]);
        }

        // Regenerate session to prevent session fixation attacks if in HTTP context
        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        return $user;
    }
}
