<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.store');
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('boutiques', ShopController::class)
        ->parameters(['boutiques' => 'shop'])
        ->names('shops')
        ->except('show');
    Route::get('/boutiques/{shop}/dashboard', [DashboardController::class, 'shop'])
        ->middleware('shop.access')->name('shops.dashboard');

    Route::prefix('/boutiques/{shop}')->middleware('shop.access')->group(function () {
        Route::resource('produits', ProductController::class)
            ->parameters(['produits' => 'product'])
            ->names('products')
            ->except('show');
        Route::resource('clients', CustomerController::class)
            ->parameters(['clients' => 'customer'])
            ->names('customers')
            ->except('show');
        Route::get('/ventes', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/ventes/nouvelle', [SaleController::class, 'create'])->name('sales.create');
        Route::post('/ventes', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/ventes/{sale}', [SaleController::class, 'show'])->name('sales.show');
    });
});
