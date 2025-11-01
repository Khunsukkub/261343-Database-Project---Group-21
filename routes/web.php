<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MembershipController;


Route::get('/membership', [MembershipController::class, 'index'])
    ->middleware(['auth'])
    ->name('membership');


Route::get('/', [HomeController::class, 'index'])->name('home');


    /* ---------- Auth required ---------- */
    Route::middleware('auth')->group(function () {
    /* Cart */
    // กัน GET /cart/{id} ไม่ให้ 403 -> ส่งกลับ index
    Route::get('/cart/{item}', fn () => redirect()->route('cart.index'))
        ->whereNumber('item');

    Route::resource('cart', CartController::class)
        ->only(['index','create','store','edit','update','destroy']);

    /* Orders */
    Route::post('/orders/checkout', [OrderHistoryController::class,'checkout'])
        ->name('orders.checkout');
    Route::get('/orders/history', [OrderHistoryController::class,'index'])
        ->name('orders.history');
    Route::post('/orders/{order}/pay', [OrderHistoryController::class,'pay'])
        ->name('orders.pay');
    Route::post('/orders/{order}/cancel', [OrderHistoryController::class,'cancel'])
        ->name('orders.cancel');

    /* Products */
    Route::get('/products/{product}', [ProductController::class,'show'])
        ->name('products.show');

    /* Profile */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/photo/update', [UserController::class, 'updateProfilePhoto'])
        ->name('profile.photo.update');
    Route::get('/profile/photo/{filename}', [UserController::class, 'showProfilePhoto'])
        ->where('filename', '.*')->name('user.photo');

    /* Me photo */
    Route::get('/me/photo', [ProfilePhotoController::class, 'me'])
        ->name('me.photo');

    /* Dashboard */
    Route::get('/dashboard', [ProductController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');
    });



require __DIR__.'/auth.php';
