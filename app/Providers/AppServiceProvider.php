<?php

namespace App\Providers;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\RoomAvailabilityCalculatorInterface::class,
            \App\Services\RoomAvailabilityService::class
        );

        $this->app->bind(
            \App\Contracts\SlotLockServiceInterface::class,
            \App\Services\SlotLockService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Standarisasi Password Policy Kuat (Min 8 Karakter, Kombinasi Huruf & Angka)
        Password::defaults(function () {
            $rule = Password::min(8)->letters()->numbers();

            return app()->isProduction()
                ? $rule->mixedCase()->symbols()->uncompromised()
                : $rule;
        });
    }
}
