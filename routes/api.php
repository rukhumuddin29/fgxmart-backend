<?php

use App\Http\Controllers\Api\V1\Admin\Auth\AdminAuthController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // User Auth
    Route::post('/login', [AuthController::class , 'login'])->middleware('throttle:5,1');
    Route::post('/register', [AuthController::class , 'register'])->middleware('throttle:5,1');

    // Public Data Routes
    Route::get('/countries', [\App\Http\Controllers\Api\V1\Admin\CountryController::class , 'index']);
    Route::get('/categories', [\App\Http\Controllers\Api\V1\Admin\CategoryController::class , 'index']);
    Route::get('/categories/{slug}', [\App\Http\Controllers\Api\V1\Admin\CategoryController::class , 'showBySlug']);
    Route::get('/products', [\App\Http\Controllers\Api\V1\Admin\ProductController::class , 'index']);
    Route::get('/products/{product}', [\App\Http\Controllers\Api\V1\Admin\ProductController::class , 'showBySlug']);
    Route::get('/hero-slides', [\App\Http\Controllers\Api\V1\HeroSlideController::class , 'index']);
    Route::get('/company-info', [\App\Http\Controllers\Api\V1\CompanyInfoController::class , 'index']);
    Route::get('/faqs', [\App\Http\Controllers\Api\V1\FaqController::class, 'index']);
    Route::get('/pages', [\App\Http\Controllers\Api\V1\Admin\PageController::class , 'index']);
    Route::get('/pages/{slug}', [\App\Http\Controllers\Api\V1\Admin\PageController::class , 'showBySlug']);
    Route::get('/orders/view/{orderNumber}', [\App\Http\Controllers\Api\V1\OrderController::class , 'publicShow']);

    // Admin Routes
    Route::prefix('admin')->group(function () {
            // Public Admin Routes
            Route::post('/secure-admin-fgx', [AdminAuthController::class , 'login']);

            // Protected Admin Routes
            Route::middleware('auth:sanctum')->group(function () {
                    Route::apiResource('roles', \App\Http\Controllers\Api\V1\Admin\RoleController::class);
                    Route::apiResource('permissions', \App\Http\Controllers\Api\V1\Admin\PermissionController::class);
                    Route::apiResource('categories', \App\Http\Controllers\Api\V1\Admin\CategoryController::class);
                    Route::apiResource('brands', \App\Http\Controllers\Api\V1\Admin\BrandController::class);
                    Route::apiResource('attributes', \App\Http\Controllers\Api\V1\Admin\AttributeController::class);
                    Route::apiResource('countries', \App\Http\Controllers\Api\V1\Admin\CountryController::class);
                    Route::get('products/generate-sku', [\App\Http\Controllers\Api\V1\Admin\ProductController::class , 'generateSku']);
                    Route::apiResource('products', \App\Http\Controllers\Api\V1\Admin\ProductController::class);
                    
                    Route::get('/hero-slides', [\App\Http\Controllers\Api\V1\HeroSlideController::class , 'adminIndex']);
                    Route::apiResource('hero-slides', \App\Http\Controllers\Api\V1\HeroSlideController::class)->except(['index']);

                    Route::apiResource('bank-accounts', \App\Http\Controllers\Api\V1\Admin\BankAccountController::class);

                    // Company Settings
                    Route::get('/company-settings', [\App\Http\Controllers\Api\V1\Admin\CompanySettingController::class, 'show']);
                    Route::post('/company-settings', [\App\Http\Controllers\Api\V1\Admin\CompanySettingController::class, 'update']);
                    
                    // Admin Orders
                    Route::get('/orders', [\App\Http\Controllers\Api\V1\OrderController::class , 'adminIndex']);
                    Route::get('/orders/{orderNumber}', [\App\Http\Controllers\Api\V1\OrderController::class , 'adminShow']);
                    Route::put('/orders/{orderNumber}/status', [\App\Http\Controllers\Api\V1\OrderController::class , 'updateStatus']);
                    Route::apiResource('faqs', \App\Http\Controllers\Api\V1\FaqController::class);
                    Route::apiResource('pages', \App\Http\Controllers\Api\V1\Admin\PageController::class);
                    Route::post('/media/upload', [\App\Http\Controllers\Api\V1\Admin\MediaController::class, 'upload']);
                });
            });

            // Protected User/Shared Routes
            Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user', function (Request $request) {
                    return $request->user();
                }
                );

                Route::post('/logout', [AuthController::class, 'logout']);
                Route::post('/update-password', [AuthController::class, 'updatePassword']);
                Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);

                // Cart Routes
                Route::get('/cart', [\App\Http\Controllers\Api\V1\CartController::class , 'index']);
                Route::post('/cart/sync', [\App\Http\Controllers\Api\V1\CartController::class , 'sync']);
                Route::post('/cart', [\App\Http\Controllers\Api\V1\CartController::class , 'store']);
                Route::put('/cart/{cartItem}', [\App\Http\Controllers\Api\V1\CartController::class , 'update']);
                Route::delete('/cart/{cartItem}', [\App\Http\Controllers\Api\V1\CartController::class , 'destroy']);
                // Wishlist Routes
                Route::get('/wishlist', [\App\Http\Controllers\Api\V1\WishlistController::class , 'index']);
                Route::post('/wishlist/toggle', [\App\Http\Controllers\Api\V1\WishlistController::class , 'toggle']);
                Route::post('/wishlist/sync', [\App\Http\Controllers\Api\V1\WishlistController::class , 'sync']);

                // Address Routes
                Route::get('/addresses', [\App\Http\Controllers\Api\V1\AddressController::class , 'index']);
                Route::post('/addresses', [\App\Http\Controllers\Api\V1\AddressController::class , 'store']);
                Route::put('/addresses/{address}', [\App\Http\Controllers\Api\V1\AddressController::class , 'update']);
                Route::delete('/addresses/{address}', [\App\Http\Controllers\Api\V1\AddressController::class , 'destroy']);
                Route::post('/addresses/{address}/default', [\App\Http\Controllers\Api\V1\AddressController::class , 'setDefault']);

                // Order Routes
                Route::get('/orders', [\App\Http\Controllers\Api\V1\OrderController::class , 'index']);
                Route::post('/orders', [\App\Http\Controllers\Api\V1\OrderController::class , 'store']);
                Route::get('/orders/{orderNumber}', [\App\Http\Controllers\Api\V1\OrderController::class , 'show']);
            }
            );
        });
