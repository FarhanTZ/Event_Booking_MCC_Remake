<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CatalogController;
use App\Http\Controllers\Public\CalendarController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\BookingWizardController;
use App\Http\Controllers\Marketing\DashboardController as MarketingDashboardController;

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
    Route::get('/my-bookings/{booking}/revise', [DashboardController::class, 'showRevision'])->name('bookings.revise.show');
    Route::post('/my-bookings/{booking}/revise', [DashboardController::class, 'submitRevision'])->name('bookings.revise.submit');
    Route::get('/my-bookings/{booking}/epass', [DashboardController::class, 'showEpass'])->name('bookings.epass');
    Route::get('/my-bookings/{booking}/epass/download', [DashboardController::class, 'downloadEpassPdf'])->name('bookings.epass.download');
});

/*
|--------------------------------------------------------------------------
| Marketing & Curation Routes (Role: marketing, super_admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:marketing,super_admin'])
    ->prefix('marketing')
    ->name('marketing.')
    ->group(function () {
        Route::get('/dashboard', [MarketingDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings/{booking}/proposal-preview', [MarketingDashboardController::class, 'streamProposal'])
            ->name('proposal.preview')
            ->middleware('signed');
        Route::get('/bookings/{booking}/poster-preview', [MarketingDashboardController::class, 'streamPoster'])
            ->name('poster.preview')
            ->middleware('signed');
        Route::post('/bookings/{booking}/approve', [MarketingDashboardController::class, 'approve'])
            ->name('bookings.approve');
        Route::post('/bookings/{booking}/revise', [MarketingDashboardController::class, 'revise'])
            ->name('bookings.revise');
        Route::post('/bookings/{booking}/reject', [MarketingDashboardController::class, 'reject'])
            ->name('bookings.reject');
        Route::post('/bookings/{booking}/confirm-payment', [MarketingDashboardController::class, 'confirmPayment'])
            ->name('bookings.confirm-payment');
        Route::get('/schedule/export-pdf', [MarketingDashboardController::class, 'exportSchedulePdf'])
            ->name('schedule.export.pdf');
        Route::get('/schedule/export-csv', [MarketingDashboardController::class, 'exportScheduleCsv'])
            ->name('schedule.export.csv');
    });

/*
|--------------------------------------------------------------------------
| Front Office Routes (Role: front_office, super_admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:front_office,super_admin'])
    ->prefix('front-office')
    ->name('front_office.')
    ->group(function () {
        Route::get('/manifest', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'index'])->name('manifest');
        Route::get('/checkin', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'showCheckinPage'])->name('checkin');
        Route::post('/checkin/inspect', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'inspectCheckin'])->name('checkin.inspect');
        Route::post('/checkin/process', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'processCheckin'])->name('checkin.process');
        Route::post('/duty-officer/assign', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'assignDutyOfficer'])->name('assign_duty_officer');
        Route::post('/duty-officer/notify', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'notifyDutyOfficer'])->name('notify_duty_officer');
        Route::post('/checkout/initiate-do', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'initiateCheckout'])->name('checkout.initiate_do');
        Route::post('/checkout/notify-do', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'notifyCheckoutDutyOfficer'])->name('checkout.notify_do');
        Route::post('/checkout/release-ktp', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'releaseKtp'])->name('checkout.release_ktp');
        Route::post('/abandoned/mark', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'markAbandoned'])->name('abandoned.mark');
        Route::get('/unclaimed-ktp', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'unclaimedKtpIndex'])->name('unclaimed_ktp');
        Route::post('/unclaimed-ktp/handover', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'handoverUnclaimedKtp'])->name('unclaimed_ktp.handover');
        Route::get('/overdue/notify-pic/{booking}', [\App\Http\Controllers\FrontOffice\ManifestController::class, 'notifyOverduePic'])->name('overdue.notify_pic');
    });