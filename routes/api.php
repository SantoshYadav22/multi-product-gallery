<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('products', ProductController::class);

Route::post('/add-cart', [CartController::class, 'store']);  
Route::get('/cart-list', [CartController::class, 'index']);  
Route::put('/cart/update', [CartController::class, 'updateByCart']);
Route::delete('/cart/delete', [CartController::class, 'deleteByCart']);
