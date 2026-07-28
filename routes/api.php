<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\PardakhteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProdectController;
use App\Http\Controllers\QarseController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/user',UserController::class);
Route::apiResource('/payments',PaymentController::class);
Route::apiResource('/user',UserController::class);
Route::apiResource('/qars',QarseController::class);
Route::apiResource('/prodect',ProdectController::class);
// route::prefix('dashbored')->middleware('auth:sanctum')