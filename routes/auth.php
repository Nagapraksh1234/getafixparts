<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
| If you're on Breeze/Jetstream, merge these into your existing
| routes/auth.php. Otherwise require this file from routes/web.php:
|     require __DIR__.'/auth.php';
*/

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // If you already have forgot/reset-password controllers, keep those
    // routes as-is — the login view just needs a route named
    // 'password.request' to exist.
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Replace these with your real dashboard controllers. They just need
    // to keep the names 'buyer.dashboard' and 'seller.dashboard' — that's
    // what the controllers above redirect to.
    Route::get('/buyer/dashboard', function () {
        return view('buyer.dashboard');
    })->name('buyer.dashboard');

    Route::middleware('role:seller')->group(function () {
        Route::get('/seller/dashboard', function () {
            return view('seller.dashboard');
        })->name('seller.dashboard');
    });
});