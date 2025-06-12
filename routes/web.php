<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use App\Models\Cart;
use App\Models\User;


Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
         return view('dashboard', [
            'productCount' => Product::withoutTrashed()->count(),
            'cartCount' => Cart::count(),
            'userCount' => User::count(),
        ]);
    })->name('dashboard');

    Route::resource('products', ProductController::class);
    Route::delete('/products/images/{id}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
    Route::get('/cart-items', [ProductController::class, 'showCartItems'])->name('cart-items');

});
