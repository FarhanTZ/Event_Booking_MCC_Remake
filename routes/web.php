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

/*
|--------------------------------------------------------------------------
| Duty Officer Field Routes (Role: duty_officer, super_admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:duty_officer,super_admin'])
    ->prefix('duty-officer')
    ->name('duty_officer.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\DutyOfficer\DashboardController::class, 'index'])->name('dashboard');
        Route::post('/bookings/{booking}/confirm-opening', [\App\Http\Controllers\DutyOfficer\DashboardController::class, 'confirmRoomOpening'])->name('confirm_opening');
        Route::post('/bookings/{booking}/clearance', [\App\Http\Controllers\DutyOfficer\DashboardController::class, 'submitClearance'])->name('clearance');
        Route::post('/bookings/{booking}/incident', [\App\Http\Controllers\DutyOfficer\DashboardController::class, 'reportIncident'])->name('incident');
        Route::post('/bookings/{booking}/incident/resolve', [\App\Http\Controllers\DutyOfficer\DashboardController::class, 'resolveIncident'])->name('incident.resolve');
        Route::post('/bookings/{booking}/abandoned/clean', [\App\Http\Controllers\DutyOfficer\DashboardController::class, 'confirmAbandonedClean'])->name('abandoned.clean');
        Route::post('/bookings/{booking}/abandoned/incident', [\App\Http\Controllers\DutyOfficer\DashboardController::class, 'reportAbandonedIncident'])->name('abandoned.incident');
    });

/*
|--------------------------------------------------------------------------
| Super Admin Routes (Role: super_admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // FR-ADM-01 Master Rooms Management
        Route::get('/rooms', [\App\Http\Controllers\Admin\RoomController::class, 'index'])->name('rooms.index');
        Route::get('/rooms/create', [\App\Http\Controllers\Admin\RoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [\App\Http\Controllers\Admin\RoomController::class, 'store'])->name('rooms.store');
        Route::get('/rooms/{room}', [\App\Http\Controllers\Admin\RoomController::class, 'show'])->name('rooms.show');
        Route::get('/rooms/{room}/edit', [\App\Http\Controllers\Admin\RoomController::class, 'edit'])->name('rooms.edit');
        Route::put('/rooms/{room}', [\App\Http\Controllers\Admin\RoomController::class, 'update'])->name('rooms.update');
        Route::delete('/rooms/{room}', [\App\Http\Controllers\Admin\RoomController::class, 'destroy'])->name('rooms.destroy');
        Route::patch('/rooms/{room}/toggle-status', [\App\Http\Controllers\Admin\RoomController::class, 'toggleStatus'])->name('rooms.toggle_status');

        // FR-ADM-02 Room Facilities Inventory Management
        Route::post('/rooms/{room}/facilities', [\App\Http\Controllers\Admin\RoomFacilityController::class, 'store'])->name('rooms.facilities.store');
        Route::put('/rooms/{room}/facilities/{facility}', [\App\Http\Controllers\Admin\RoomFacilityController::class, 'update'])->name('rooms.facilities.update');
        Route::delete('/rooms/{room}/facilities/{facility}', [\App\Http\Controllers\Admin\RoomFacilityController::class, 'destroy'])->name('rooms.facilities.destroy');

        // FR-ADM-03 Cross-Division Staff Accounts Management
        Route::get('/staff', [\App\Http\Controllers\Admin\StaffManagementController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [\App\Http\Controllers\Admin\StaffManagementController::class, 'create'])->name('staff.create');
        Route::post('/staff', [\App\Http\Controllers\Admin\StaffManagementController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [\App\Http\Controllers\Admin\StaffManagementController::class, 'edit'])->name('staff.edit');
        Route::put('/staff/{staff}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{staff}', [\App\Http\Controllers\Admin\StaffManagementController::class, 'destroy'])->name('staff.destroy');
        Route::patch('/staff/{staff}/reset-password', [\App\Http\Controllers\Admin\StaffManagementController::class, 'resetPassword'])->name('staff.reset_password');
        Route::patch('/staff/{staff}/toggle-status', [\App\Http\Controllers\Admin\StaffManagementController::class, 'toggleStatus'])->name('staff.toggle_status');

        // FR-ADM-04 Penegakan Sanksi & Blacklist Akun Pemohon
        Route::get('/sanctions', [\App\Http\Controllers\Admin\SanctionManagementController::class, 'index'])->name('sanctions.index');
        Route::patch('/sanctions/{user}/suspend', [\App\Http\Controllers\Admin\SanctionManagementController::class, 'suspend'])->name('sanctions.suspend');
        Route::patch('/sanctions/{user}/lift', [\App\Http\Controllers\Admin\SanctionManagementController::class, 'lift'])->name('sanctions.lift');

        // FR-ADM-05 Intervensi Reservasi & Pembatalan Paksa Darurat (Super Override)
        Route::get('/interventions', [\App\Http\Controllers\Admin\EmergencyInterventionController::class, 'index'])->name('interventions.index');
        Route::post('/interventions/bookings/{booking}/cancel', [\App\Http\Controllers\Admin\EmergencyInterventionController::class, 'cancel'])->name('interventions.cancel');

        // FR-ADM-06 Dasbor Eksekutif & Analitik Utilisasi Gedung
        Route::get('/analytics', [\App\Http\Controllers\Admin\ExecutiveAnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/analytics/export-pdf', [\App\Http\Controllers\Admin\ExecutiveAnalyticsController::class, 'exportPdf'])->name('analytics.export.pdf');
        Route::get('/analytics/export-csv', [\App\Http\Controllers\Admin\ExecutiveAnalyticsController::class, 'exportCsv'])->name('analytics.export.csv');

        // FR-ADM-07 Audit Trail Sistem Menyeluruh
        Route::get('/audit-trail', [\App\Http\Controllers\Admin\SystemAuditTrailController::class, 'index'])->name('audit.index');
    });