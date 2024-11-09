<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;


// 

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//login api
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);

//logout
Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');
//products api
Route::apiResource('/api-products', App\Http\Controllers\Api\ProductController::class)->middleware('auth:sanctum');
//category api
Route::apiResource('/api-categories', App\Http\Controllers\Api\CategoryController::class)->middleware('auth:sanctum');
//order api
Route::post('/save-order', [App\Http\Controllers\Api\OrderController::class, 'saveOrder'])->middleware('auth:sanctum');
//discount api
Route::get('/api-discounts', [App\Http\Controllers\Api\DiscountController::class, 'index'])->middleware('auth:sanctum');
//add discount
Route::post('/api-discounts', [App\Http\Controllers\Api\DiscountController::class, 'store'])->middleware('auth:sanctum');
//discount api
Route::get('/api-orders', [App\Http\Controllers\Api\OrderController::class, 'index'])->middleware('auth:sanctum');
//tax api
Route::get('/api-taxs', [App\Http\Controllers\Api\TaxController::class, 'index'])->middleware('auth:sanctum');
//add tax
Route::post('/api-taxs', [App\Http\Controllers\Api\TaxController::class, 'store'])->middleware('auth:sanctum');
