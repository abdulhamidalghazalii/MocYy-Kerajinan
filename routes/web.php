<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Admin\PaymentVerificationController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StoreSettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Storefront & Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [ProductController::class, 'index'])->name('products.index');
Route::get('/katalog/{slug}', [ProductController::class, 'show'])->name('products.show');

// Shopping Cart
Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang', [CartController::class, 'store'])->name('cart.store');
Route::patch('/keranjang/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/keranjang/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::post('/keranjang/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

// Customer Portal Routes
Route::middleware(['auth'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    
    // Customer Orders & Payment
    Route::get('/pesanan', [CustomerOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{id}', [CustomerOrderController::class, 'show'])->name('orders.show');
    Route::post('/pesanan/{id}/batal', [CustomerOrderController::class, 'cancel'])->name('orders.cancel');
    
    Route::get('/pesanan/{id}/pembayaran', [CustomerPaymentController::class, 'show'])->name('orders.payment');
    Route::post('/pesanan/{id}/upload-bukti', [CustomerPaymentController::class, 'uploadProof'])->name('orders.upload_proof');
    Route::get('/bukti-bayar/{id}', [CustomerPaymentController::class, 'downloadProof'])->name('proofs.download');
    
    // Profile & Addresses
    Route::get('/profil', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::post('/alamat', [CustomerProfileController::class, 'storeAddress'])->name('address.store');
    Route::delete('/alamat/{id}', [CustomerProfileController::class, 'destroyAddress'])->name('address.destroy');
});

// Admin Portal Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Payment Verification Center
    Route::get('/verifikasi-pembayaran', [PaymentVerificationController::class, 'index'])->name('payments.verification');
    Route::get('/verifikasi-pembayaran/{id}', [PaymentVerificationController::class, 'show'])->name('payments.show');
    Route::post('/verifikasi-pembayaran/{id}/konfirmasi', [PaymentVerificationController::class, 'confirm'])->name('payments.confirm');
    Route::post('/verifikasi-pembayaran/{id}/tolak', [PaymentVerificationController::class, 'reject'])->name('payments.reject');
    Route::get('/bukti-bayar/{id}', [PaymentVerificationController::class, 'viewProof'])->name('payments.view_proof');
    
    // Orders Fulfillment
    Route::get('/pesanan', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/pesanan/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/pesanan/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update_status');
    
    // Products & Categories
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', AdminCategoryController::class);
    
    // Customer Accounts & Email Management
    Route::resource('customers', AdminCustomerController::class);
    Route::post('/customers/{id}/toggle', [AdminCustomerController::class, 'toggleStatus'])->name('customers.toggle');
    
    // Payment & Store Settings
    Route::resource('settings/payments', PaymentSettingController::class, [
        'as' => 'settings',
        'parameters' => ['payments' => 'paymentMethod']
    ]);
    Route::post('/settings/payments/{paymentMethod}/toggle', [PaymentSettingController::class, 'toggleStatus'])->name('settings.payments.toggle');
    Route::get('/settings/store', [StoreSettingController::class, 'index'])->name('settings.store.index');
    Route::post('/settings/store', [StoreSettingController::class, 'update'])->name('settings.store.update');
    
    // Reports & Activity Logs
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export');
    Route::get('/log-aktivitas', [ActivityLogController::class, 'index'])->name('logs.index');
});
