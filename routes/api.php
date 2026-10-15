<?php

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\CheckoutApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\StoreApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RESTful API routes for the Next.js Storefront & 3D Interactive Client
|
*/

// Products & Catalog
Route::get('/products', [ProductApiController::class, 'index']);
Route::get('/products/{slug}', [ProductApiController::class, 'show']);

// Categories
Route::get('/categories', [CategoryApiController::class, 'index']);

// Store configuration, shipping & payment methods
Route::get('/store/settings', [StoreApiController::class, 'settings']);
Route::get('/store/shipping-methods', [StoreApiController::class, 'shippingMethods']);
Route::get('/store/payment-methods', [StoreApiController::class, 'paymentMethods']);

// Checkout & Order Tracking
Route::post('/checkout', [CheckoutApiController::class, 'checkout']);
Route::get('/orders/track/{orderNumber}', [CheckoutApiController::class, 'track']);
