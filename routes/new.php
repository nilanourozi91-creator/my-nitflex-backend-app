
// Route::post('/register', [AuthController::class, 'register']);
// // Route::post('/login', [AuthController::class, 'login']);
// Route::middleware('auth:sanctum')->group(function () {
// Route::post('/logout', [AuthController::class, 'logout']);
// });

// Route::middleware(['auth:sanctum', 'admin'])
//     ->prefix('admin')
//     ->group(function () {
//         Route::get('/test', function () {
//             return response()->json([
//                 'success' => true,
//                 'message' => 'Welcome to FreshStock Admin.',
//             ]);
//         });

//     });
// // private routes
Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/items', [CartController::class, 'store']);
    Route::put('/cart/items/{item}', [CartController::class, 'update']);
    Route::delete('/cart/items/{item}', [CartController::class, 'destroy']);
    Route::apiResource('cart',CartController::class);
    Route::delete('/cart', [CartController::class, 'clear']);
});

// Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
// Route::get('/orders', [OrderController::class, 'index']);
// Route::get('/orders/{order}', [OrderController::class, 'show']);
// Route::post('/orders', [OrderController::class, 'store']);
// Route::post('/payments', [ApiPaymentController::class, 'store']);
// Route::post( '/wishlist', [WishlistController::class, 'store'] );
// });

// Route::get(
//     '/dashboard',
//     [DashboardController::class, 'index']
// );
// Route::get(
//     '/dashboard/sales',
//     [DashboardController::class, 'sales']
// );
// Route::get(
//     '/dashboard/recent-orders',
//     [DashboardController::class, 'recentOrders']
// );
// Route::get(
//     '/dashboard/low-stock',
//     [DashboardController::class, 'lowStock']
// );
// Route::middleware(['auth:sanctum', 'admin'])
//     ->prefix('admin')
//     ->group(function () {

//         Route::apiResource(
//             'products',
//             AdminProductController::class
//         );

//         Route::put(
//             '/products/{product}/stock',
//             [AdminProductController::class, 'updateStock']
//         );
//     });

//     Route::middleware(['auth:sanctum', 'admin'])
//     ->prefix('admin')
//     ->group(function () {

//         Route::apiResource(
//             'products',
//             AdminProductController::class
//         );

//         Route::put(
//             '/products/{product}/stock',
//             [AdminProductController::class, 'updateStock']
//         );

//         Route::apiResource(
//             'categories',
//             AdminCategoryController::class
//         );
//     });

//     Route::apiResource(
//     'payments',
//     AdminPaymentController::class
// )->only([
//     'index',
//     'show',
// ]);

// Route::put(
//     '/payments/{payment}/status',
//     [AdminPaymentController::class, 'updateStatus']
// );
// Route::get('/prodata',[ProductController::class,'GetData']);
// Route::apiResource('/products',ProductController::class);
// Route::apiResource('/reviews',ReviewController::class);
// Route::apiResource('/catagory',CategoryController::class);
// Route::post('/login',[AuthController::class,'login']);
// Route::post('/register',[AuthController::class, 'register']);
// Route::get('/check_token',[AuthController::class, 'show']);