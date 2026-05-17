<?php

use App\Http\Controllers\Admin\BlackoutDateController;
use App\Http\Controllers\Admin\BookingAdminController;
use App\Http\Controllers\Admin\CheckInController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityAdminController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SeasonalRateController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Webhooks\PaymongoWebhookController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::post('/webhooks/paymongo', PaymongoWebhookController::class)
    ->withoutMiddleware([ValidateCsrfToken::class])
    ->name('webhooks.paymongo');

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirectToProvider'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'handleProviderCallback'])->name('socialite.callback');

Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');
Route::get('/facilities/{facility}', [FacilityController::class, 'show'])->name('facilities.show');
Route::redirect('/cottages', '/facilities')->name('cottages.index');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect()->route('bookings.index'))->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/facilities/{facility}/reviews', [ReviewController::class, 'store'])->name('facilities.reviews.store');

    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/facilities/{facility}/book', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/payment', [BookingController::class, 'recordPayment'])->name('bookings.payment.record');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

Route::middleware(['auth', 'role:staff,admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/check-in', [CheckInController::class, 'index'])->name('checkin.index');
    Route::post('/check-in/lookup', [CheckInController::class, 'lookup'])->name('checkin.lookup');
    Route::post('/check-in/{ticket}/confirm', [CheckInController::class, 'confirm'])->name('checkin.confirm');

    Route::get('/bookings', [BookingAdminController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingAdminController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/status', [BookingAdminController::class, 'updateStatus'])->name('bookings.status');
    Route::patch('/bookings/{booking}/payment', [BookingAdminController::class, 'verifyPayment'])->name('bookings.payment');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('facilities', FacilityAdminController::class)->except('show');
    Route::resource('promotions', PromotionController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('seasonal-rates', SeasonalRateController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('blackout-dates', BlackoutDateController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/users', [UserAdminController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [UserAdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserAdminController::class, 'destroy'])->name('users.destroy');
    Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/logs', [SystemLogController::class, 'index'])->name('logs.index');
});

require __DIR__.'/auth.php';
