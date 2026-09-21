<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLogin']);

// Login
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Register
Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth')->group(function () {
    // Dashboard — LoginController@dashboard sends buyers and sellers to
    // different views based on $user->role.
    Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('dashboard');

    // Home — search + browse sale items, shared by buyers and sellers.
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});