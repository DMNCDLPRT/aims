<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ManufacturerController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    Route::get('/about', function () {
        return Inertia::render('About');
    })->name('about');

    // super admin
    Route::middleware('role:super_admin')->group(function () {

        // Admin Routes
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', function () {
                return 'Admin Dashboard';
            })->name('dashboard');

            Route::get('/users', function () {
                $users = User::all();
                return 'Manage Admin User Page <br> ' . $users;
            })->name('settings');
        });

        Route::post('/categories/list', [CategoryController::class, 'list'])->name('category.list');
        Route::resource('/categories', CategoryController::class)->except(['create', 'edit']);
    });

    //super admin and inventory manager
    Route::middleware('role:super_admin,inventory_manager')->group(function () {
        Route::post('/manufacturers/list',[ManufacturerController::class, 'list'])->name('manufacturer.list');
        Route::resource('/manufacturers', ManufacturerController::class)->except(['create', 'edit']);

        Route::post('/locations/list',[LocationController::class, 'list'])->name('location.list');
        Route::resource('/locations', LocationController::class)->except(['create', 'edit']);
    });

    // super admin inventory user
    Route::middleware('role:super_admin,inventory_user')->group(function () {
        Route::post('/assets/list',[AssetController::class, 'list'])->name('asset.list');
        Route::resource('/assets', AssetController::class)->except(['create', 'edit']);
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
