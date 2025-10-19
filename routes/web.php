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

// Public routes
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

// Admin routes
Route::prefix('admin')->group(function () {
    // Public admin routes
    Route::middleware('guest')->group(function() {
        Route::get('/login', [AdminController::class, 'loginPage'])->name('admin.login');
        Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post');
    });
    
    // Protected admin routes that require auth and admin role
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('admin.transactions');
        Route::get('/statistics', [AdminController::class, 'statistics'])->name('admin.statistics');
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        
        // Categories management
        Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
        Route::post('/categories', [AdminController::class, 'addCategory'])->name('admin.categories.add');
        Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');
        
        // Products management
        Route::post('/products', [AdminController::class, 'addProduct'])->name('admin.products.add');
        Route::get('/products/{id}', [AdminController::class, 'getProduct'])->name('admin.products.get');
        Route::put('/products/{id}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
        Route::delete('/products/{id}', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');
        
        // Transaction management 
        Route::get('/transactions/{id}', [AdminController::class, 'getTransaction'])->name('admin.transactions.get');
        Route::post('/transactions/{id}/status', [AdminController::class, 'updateTransactionStatus'])->name('admin.transactions.updateStatus');
        
                // Statistics API
        Route::get('/api/statistics/summary', [AdminController::class, 'getStatisticsSummary']);
        Route::get('/api/statistics/revenue-chart', [AdminController::class, 'getRevenueChart']);
        Route::get('/api/statistics/category-chart', [AdminController::class, 'getCategoryChart']);
    });
}); // End admin routes


