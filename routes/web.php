<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

// Route for the home page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard route, protected by authentication and verification
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [SalesController::class, 'dashboard'])->name('dashboard');

    // Profile management routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Resource routes for products (CRUD operations)
    Route::resource('products', ProductController::class);

    // Sales management routes
    Route::prefix('sales')->name('sales.')->group(function () {
        // Route for creating a sale
        Route::get('create', [SalesController::class, 'create'])->name('create');

        // Report route for sales
        Route::get('report', [SalesController::class, 'report'])->name('report');

        // Daily sales route
        Route::get('daily', [SalesController::class, 'dailyReport'])->name('daily');  // Correct daily report route

        // Index route to display all sales
        Route::get('/', [SalesController::class, 'index'])->name('index');

        // Store route for creating a new sale
        Route::post('/', [SalesController::class, 'store'])->name('store');
    });
});

// Include authentication routes
require __DIR__ . '/auth.php';
