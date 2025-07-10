
<?php

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CountBarangController;
use App\Http\Controllers\CountCustomerController;
use App\Http\Controllers\CountOrderController;
use App\Http\Controllers\CountUserController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'store']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);
Route::get('/users/count', [CountUserController::class, 'count']);
Route::get('/customer/count', [CountCustomerController::class, 'count']);
Route::get('/barang/count', [CountBarangController::class, 'count']);
Route::get('/order/count', [CountOrderController::class, 'count']);




Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    //Akun
    // Route::controller(UserController::class)->group(function(){
    //     Route::get('/user', 'index');
    //     Route::post('/user/store', 'store');
    //     Route::patch('/user/{id}/update', 'update');
    //     Route::get('/user/{id}','show');
    //     Route::delete('/user/{id}', 'destroy');
    // });

    Route::apiResource('user', UserController::class);
    Route::apiResource('customer', CustomerController::class);
    Route::apiResource('barang', BarangController::class);
    Route::apiResource('stock', StockController::class);
    Route::apiResource('order', OrderController::class);
    
   
});




