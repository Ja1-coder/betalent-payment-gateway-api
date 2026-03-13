<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\TransactionController;

/*
|--------------------------------------------------------------------------
| Rotas Púlicas
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);


Route::get('/products', [ProductController::class, 'index']);
Route::post('/purchase', [CheckoutController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Rotas Privadas
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // User Profile & Logout
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |-- Apenas Admin --|
    */
    Route::middleware('role:admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });

    /*
    |-- Rotas do financeiro (e admin) --|
    */
    Route::middleware('role:admin,finance')->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::post('/transactions/{transaction}/refund', [TransactionController::class, 'refund']);
    });
    
});