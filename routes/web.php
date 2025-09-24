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

Route::resource('/categories', CategoryController::class)->except(['create', 'edit']);
Route::resource('/assets', AssetController::class)->except(['create', 'edit']);
Route::resource('/locations', LocationController::class)->except(['create', 'edit']);
Route::resource('/manufacturers', ManufacturerController::class)->except(['create', 'edit']);

/* Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
    });

    //super admin and inventory manager
    Route::middleware('role:super_admin,inventory_manager')->group(function () {
    });

    // super admin inventory user
    Route::middleware('role:super_admin,inventory_user')->group(function () {
    });
}); */

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
