<?php

use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminsAuthController;
use App\Http\Controllers\Admin\ItemsController;
use App\Http\Controllers\Admin\MenusCategoryController;

// use App\Http\Controllers\PaymentController;
// use App\Http\Controllers\user\PaymentConrtoller;


use App\Http\Controllers\user\CartController;
use App\Http\Controllers\user\UsersAuthController;
use App\Http\Controllers\user\OrderController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('admin')->group(function () {
    Route::post('/register', [AdminsAuthController::class, 'register'])->name('admins.register');
    Route::post('/login', [AdminsAuthController::class, 'login'])->name('admins.login');

});
Route::middleware('admin')->prefix('admin')->group(function () {
    // Routes for MenuCategories
    Route::get('/menuCategories', [MenusCategoryController::class, 'index']);
    Route::post('/menuCategories', [MenusCategoryController::class, 'store']);
    Route::get('/menuCategories/{menuCategory}', [MenusCategoryController::class, 'show']);
    Route::post('/menuCategories/{menuCategory}', [MenusCategoryController::class, 'update']);
    Route::delete('/menuCategories/{menuCategory}', [MenusCategoryController::class, 'destroy']);

    // Routes for Items
    Route::get('/items', [ItemsController::class, 'index']);
    Route::post('/items', [ItemsController::class, 'store']);
    Route::get('/items/{item}', [ItemsController::class, 'show']);
    Route::post('/items/{item}', [ItemsController::class, 'update']);
    Route::delete('/items/{item}', [ItemsController::class, 'destroy']);
    
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('dashboard.orders');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('dashboard.order');
    Route::get('/orders/today', [OrderController::class, 'todayOrders']);

    Route::post('/logout', [AdminsAuthController::class, 'logout'])->name('admins.logout');
});







Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::prefix('user')->group(function () {
    Route::post('/register', [UsersAuthController::class, 'register'])->name('users.register');
    Route::post('/login', [UsersAuthController::class, 'login'])->name('users.login');
    
    
    Route::apiResource('/menuCategories', MenusCategoryController::class)->only(['index', 'show']);

    Route::apiResource('/items', ItemsController::class)->only(['index', 'show']);    

});
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    // Route::delete('/cart', [CartController::class, 'clearCart']);

    Route::get('/all-cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::post('/cart/{item_id}', [CartController::class, 'update']);
    Route::delete('/cart/{item_id}', [CartController::class, 'destroy']);

    Route::post('/orders', [OrderController::class, 'placeOrder']);
    Route::get('/orders/', [OrderController::class, 'index']);
    
    Route::post('/logout', [UsersAuthController::class, 'logout'])->name('users.logout');
    
    Route::post('{user}/payment/process', [PaymentController::class, 'paymentProcess'])->name('payment.process');
    Route::get('payment/callback', [PaymentController::class, 'callBack'])->name('payment.callback');
    Route::get('payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');
// Route::post('/{user}/payment/process', [PaymentController::class, 'paymentProcess']);
// Route::match(['GET','POST'],'/payment/callback', [PaymentController::class, 'callBack']);    
});

