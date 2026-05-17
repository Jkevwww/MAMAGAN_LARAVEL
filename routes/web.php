<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CottageController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCottageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirectToProvider'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'handleProviderCallback'])->name('socialite.callback');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cottage routes for users
    Route::get('/cottages', [CottageController::class, 'index'])->name('cottages.index');
    Route::get('/cottages/{cottage}', [CottageController::class, 'show'])->name('cottages.show');

    // Reservation routes for users
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/cottages/{cottage}/book', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}/payment/success', [ReservationController::class, 'paymentSuccess'])->name('reservations.payment_success');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Manage Cottages
    Route::resource('cottages', AdminCottageController::class);
    
    // Manage Reservations
    Route::get('/reservations', [AdminController::class, 'reservations'])->name('reservations');
    Route::get('/reservations/{reservation}', [AdminController::class, 'showReservation'])->name('reservations.show');
    Route::post('/reservations/{reservation}/status', [AdminController::class, 'updateReservationStatus'])->name('reservations.update_status');
});

require __DIR__.'/auth.php';
