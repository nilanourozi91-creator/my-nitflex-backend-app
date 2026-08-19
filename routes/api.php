<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Api\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\PouplerProdects;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/test', function () {
            return response()->json([
                'success' => true,
                'message' => 'Welcome to FreshStock Admin.',
            ]);
        });

    });


// private routes
// Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'store']);
    Route::put('/cart/items/{item}', [CartController::class, 'update']);
    Route::delete('/cart/items/{item}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);
// });

Route::middleware('auth:sanctum')->group(function () {
Route::get('/orders', [OrderController::class, 'index']);
Route::get('/orders/{order}', [OrderController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);
Route::post('/payments', [ApiPaymentController::class, 'store']);
Route::post( '/wishlist', [WishlistController::class, 'store'] );
});

Route::get(
    '/dashboard',
    [DashboardController::class, 'index']
);
Route::get(
    '/dashboard/sales',
    [DashboardController::class, 'sales']
);
Route::get(
    '/dashboard/recent-orders',
    [DashboardController::class, 'recentOrders']
);
Route::get(
    '/dashboard/low-stock',
    [DashboardController::class, 'lowStock']
);
Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::apiResource(
            'products',
            AdminProductController::class
        );

        Route::put(
            '/products/{product}/stock',
            [AdminProductController::class, 'updateStock']
        );
    });

    Route::middleware(['auth:sanctum', 'admin'])
    ->prefix('admin')
    ->group(function () {

        Route::apiResource(
            'products',
            AdminProductController::class
        );

        Route::put(
            '/products/{product}/stock',
            [AdminProductController::class, 'updateStock']
        );

        Route::apiResource(
            'categories',
            AdminCategoryController::class
        );
    });

    Route::apiResource(
    'payments',
    AdminPaymentController::class
)->only([
    'index',
    'show',
]);

Route::put(
    '/payments/{payment}/status',
    [AdminPaymentController::class, 'updateStatus']
);
Route::get('/prodata',[ProductController::class,'GetData']);
Route::apiResource('/products',ProductController::class);
Route::apiResource('/reviews',ReviewController::class);
Route::apiResource('/catagory',CategoryController::class);
// Route::apiResource('/User',UserController::class);
// Route::apiResource('/a',AuthController::class);
Route::post('/login',[AuthController::class, 'login']);
Route::post('/register',[AuthController::class, 'register']);
Route::get('/check_token',[AuthController::class, 'show']);