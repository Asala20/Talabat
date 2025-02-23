<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CategoryController;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::post('/admin/dashboard', [AdminController::class, 'index']);
});


// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Protected Routes (using Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});




Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
});



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('order-items', OrderItemController::class);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('payments', PaymentController::class);
});




Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('carts', CartController::class);
});



Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('orders', OrderController::class);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class);
});




