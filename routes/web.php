<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderHistoryController;



Route::middleware('auth')->group(function () {
    Route::get('/orders/history', [OrderHistoryController::class,'index'])->name('orders.history');
    Route::post('/orders/{order}/cancel', [OrderHistoryController::class,'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/pay', [OrderHistoryController::class,'pay'])->name('orders.pay');
});


Route::resource('cart', CartController::class)
    ->only(['index','create','store','edit','update','destroy'])
    ->middleware('auth');


Route::get('/me/photo', [ProfilePhotoController::class, 'me'])
    ->middleware('auth')
    ->name('me.photo');


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/photo/update', [UserController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    Route::get('/profile/photo/{filename}', [UserController::class, 'showProfilePhoto'])->where('filename', '.*')->name('user.photo');
    // routes/web.php(Cart)
    Route::get('/cart',[CartController::class,'index'])->name('cart.index');
    Route::post('/cart',[CartController::class,'store'])->name('cart.store');
    Route::patch('/cart/{item}',[CartController::class,'update'])->name('cart.update');
    Route::delete('/cart/{item}',[CartController::class,'destroy'])->name('cart.destroy');
    Route::patch('/cart/{item}/toggle', [CartController::class,'toggle'])->name('cart.toggle');
    Route::get('/orders/history', [OrderHistoryController::class,'index'])
    ->name('orders.history');

});

require __DIR__.'/auth.php';
