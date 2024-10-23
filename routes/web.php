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



});

require __DIR__.'/auth.php';
