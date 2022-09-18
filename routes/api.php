<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('test')->group(function () {
    Route::get('test', 'App\Http\Controllers\test@TestAction');
    Route::get('DBA', 'App\Http\Controllers\test@TestDBAction');
});

Route::prefix('authentication')->group(function() {
    Route::post('login', 'App\Http\Controllers\AuthenticationController@Login')
        ->middleware('authentication');
    Route::post('seller-login', 'App\Http\Controllers\AuthenticationController@SellerLogin')
        ->middleware('authentication');
    Route::get('check', 'App\Http\Controllers\AuthenticationController@CheckLogin')
        ->middleware('authentication.check');
});

Route::prefix('products')->group(function() {
    Route::get('list', 'App\Http\Controllers\ProductController@GetProducts');
    Route::get('detail/{id}', 'App\Http\Controllers\ProductController@GetProduct');
        //->middleware('authentication.check');
    Route::get('image', 'App\Http\Controllers\ProductController@GetProductImage');
});

Route::prefix('orders')->group(function() {
    Route::post('', 'App\Http\Controllers\OrderController@CreateOrder');
        //->middleware('authentication.check');
    Route::get('list/{clientId}', 'App\Http\Controllers\OrderController@OrdersList');
    Route::get('detail/{orderId}', 'App\Http\Controllers\OrderController@OrderDetail');

});
