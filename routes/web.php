<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryAttributeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\CategoryAttributeApiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'shop'])
    ->name('shop.index');

Route::get('/shop/products/{product:slug}', [ProductController::class, 'shopShow'])
    ->name('shop.products.show');


Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])
    ->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login'])
    ->name('admin.login.submit');
Route::post('admin/logout', [AdminAuthController::class, 'logout'])
    ->name('admin.logout');

Route::prefix('admin')
    ->name('admin.')
    ->middleware('admin')
    ->group(function () {
        
        Route::get('/', fn () => redirect()->route('admin.products.index'))
            ->name('dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);

        Route::resource(
            'categories.attributes',
            CategoryAttributeController::class
        )->except(['show']);
    });

Route::get(
    '/api/categories/{category}/attributes',
    [CategoryAttributeApiController::class, 'show']
)->name('api.categories.attributes');
