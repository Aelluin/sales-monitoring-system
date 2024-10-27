<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::get('/sales/report', [SalesController::class, 'report'])->name('sales.report');


    Route::resource('products', ProductController::class);
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');


    Route::resource('sales', SalesController::class);
    Route::resource('sales', SalesController::class)->only(['create', 'store', 'index']);

    Route::get('/sales/report', [SalesController::class, 'report'])->name('sales.report');

// In web.php (or your routing file)

// Dashboard and main pages


// Product creation and editing
Route::get('/dashboard/products/create', [ProductController::class, 'create'])->name('dashboard.products.create');
Route::get('/dashboard/products/{id}/edit', [ProductController::class, 'edit'])->name('dashboard.products.edit'); // Pass the product ID

// Sales creation and editing
Route::get('/dashboard/sales/create', [SalesController::class, 'create'])->name('dashboard.sales.create');
Route::get('/dashboard/sales/{id}/edit', [SalesController::class, 'edit'])->name('dashboard.sales.edit'); // Pass the sales ID
Route::get('/sales/report', [SalesController::class, 'report'])->name('sales.report');


});

require __DIR__.'/auth.php';
