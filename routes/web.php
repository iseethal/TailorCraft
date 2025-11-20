<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'loginSubmit'])->name('login.submit');
Route::get('/admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:web', 'App\Http\Middleware\UserAccess:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
        Route::resource('products', App\Http\Controllers\Admin\ProductController::class);

        Route::post('products/{id}/image-order', [App\Http\Controllers\Admin\ProductController::class, 'updateImageOrder'])->name('admin.products.update-image-order');
        Route::post('variant-image/{id}', [App\Http\Controllers\Admin\ProductController::class, 'deleteVariantImage'])->name('admin.products.delete-variant-image');

        Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

    });
