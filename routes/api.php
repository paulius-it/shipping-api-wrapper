<?php

use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\Api\ShippingApiController;
use App\Http\Controllers\Api\ShippingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('shipping-create', [ShippingApiController::class, 'createShippingItem']);
    Route::post('logout', [UserAccessController::class, 'logout'])->name('user.logout');
    route::apiResource('shippings', ShippingController::class);
});

Route::post('register', [UserAccessController::class, 'register'])->name('user.register');
Route::post('login', [UserAccessController::class, 'login'])->name('user.login');

route::get('/test', [ShippingApiController::class, 'createShippingItem']);
