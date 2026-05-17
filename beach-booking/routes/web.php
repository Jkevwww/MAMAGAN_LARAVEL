<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\Admin\BookingAdminController;
use App\Http\Controllers\Admin\RoomTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RoomController::class, 'index'])->name('home');

Route::get('/book', [BookingController::class, 'create'])->name('booking.start');
Route::post('/book', [BookingController::class, 'store'])->name('booking.store');
Route::get('/my-bookings', [BookingController::class, 'index'])->name('booking.index');

Route::get('/payments/{booking}/gcash', [PaymentController::class, 'create'])->name('payments.gcash');
Route::get('/payments/mock/{payment}', [PaymentController::class, 'mockSuccess'])->name('payments.mock');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.bookings.index');
    })->name('dashboard');

    Route::resource('room-types', RoomTypeController::class);
    Route::resource('bookings', BookingAdminController::class)->only(['index', 'show', 'update', 'destroy']);
});
    