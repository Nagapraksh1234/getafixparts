<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\WishlistController;
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

    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/{product}', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/item/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/item/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Listings (sellers only — enforced inside ListingController)
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');

    // Checkout & Orders
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout.show');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});