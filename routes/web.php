<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\CalendarController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\BookingWizardController;

/*
|--------------------------------------------------------------------------
| Public / Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/ruangan/{id}', [CatalogController::class, 'show'])->name('catalog.show');
Route::get('/jadwal', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/api/rooms/{id}/availability', [CalendarController::class, 'availability'])->name('calendar.availability');
Route::post('/api/booking/lock-slot', [CalendarController::class, 'lockSlot'])->name('calendar.lock');
Route::post('/api/booking/unlock-slot', [CalendarController::class, 'unlockSlot'])->name('calendar.unlock');
Route::post('/api/booking/batch-lock', [CalendarController::class, 'batchLock'])->name('calendar.batch-lock');
Route::post('/api/booking/batch-unlock', [CalendarController::class, 'batchUnlock'])->name('calendar.batch-unlock');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Public User / Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/booking/wizard', [BookingWizardController::class, 'index'])->name('booking.wizard');
    Route::post('/booking/wizard', [BookingWizardController::class, 'store'])->name('booking.store');
    Route::get('/my-bookings', [DashboardController::class, 'index'])->name('my-bookings');
});