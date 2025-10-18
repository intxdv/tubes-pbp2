<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AddressController;

// Home / Products
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/', [ProductController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Product management (some apps only allow admin)
Route::get('/products/{id}/edit', [ProductController::class, 'edit']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::get('/products/create', [ProductController::class, 'create']);
Route::post('/products', [ProductController::class, 'store']);

// Reviews
Route::post('/products/{id}/review', [ReviewController::class, 'store']);
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

// Auth
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Cart (session-backed simplified flow)
Route::get('/cart', [CartController::class, 'index']);
Route::post('/cart/add/{productId}', [CartController::class, 'add']);
// Use POST for remove to avoid method-override collisions in nested forms
Route::post('/cart/remove/{productId}', [CartController::class, 'remove']);
Route::post('/cart/buy-now/{productId}', [CartController::class, 'buyNow']);
Route::get('/cart/checkout', [CartController::class, 'showCheckout']);
Route::post('/cart/checkout', [CartController::class, 'checkout']);

// Orders / Transactions
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::patch('/orders/cancel/{id}', [OrderController::class, 'cancel']);

Route::get('/transactions', [TransactionController::class, 'index']);
Route::get('/transactions/{id}', [TransactionController::class, 'show']);
Route::post('/transactions/pay/{orderId}', [TransactionController::class, 'pay']);
Route::post('/transactions/ship/{id}', [TransactionController::class, 'ship']);
Route::post('/transactions/confirm/{id}', [TransactionController::class, 'confirm']);

// Dashboard / Address (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/account', [DashboardController::class, 'account'])->name('dashboard.account');
    Route::patch('/dashboard/account', [DashboardController::class, 'updateAccount'])->name('dashboard.account.update');
    Route::get('/dashboard/orders', [DashboardController::class, 'orders'])->name('dashboard.orders');

    // Address management
    Route::get('/address', [AddressController::class, 'index'])->name('address.index');
    Route::get('/address/create', [AddressController::class, 'create'])->name('address.create');
    Route::post('/address', [AddressController::class, 'store'])->name('address.store');
    Route::get('/address/{id}/edit', [AddressController::class, 'edit'])->name('address.edit');
    Route::patch('/address/{id}', [AddressController::class, 'update'])->name('address.update');
    Route::delete('/address/{id}', [AddressController::class, 'destroy'])->name('address.destroy');
});

// Admin area
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/admin_dashboard', [AdminController::class, 'admin_dashboard']);
    Route::get('/admin/transactions', [AdminController::class, 'transactions']);
    Route::get('/admin/categories', [AdminController::class, 'categories']);
    Route::post('/admin/categories', [AdminController::class, 'addCategory']);
    Route::delete('/admin/categories/{id}', [AdminController::class, 'deleteCategory']);
});


