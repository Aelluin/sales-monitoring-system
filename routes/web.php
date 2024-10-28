<?php

use App\Http\Controllers\ProfileController; // Import ProfileController for managing user profiles
use App\Http\Controllers\ProductController; // Import ProductController for managing products
use App\Http\Controllers\SalesController;   // Import SalesController for managing sales
use App\Models\Sale;                        // Import Sale model to interact with sales data

use Illuminate\Support\Facades\Route;        // Import Route facade for defining routes

// Route for the home page
Route::get('/', function () {
    return view('welcome'); // Return the 'welcome' view when accessing the root URL
});

// Route for the dashboard page, protected by authentication and verification
Route::get('/dashboard', function () {
    return view('dashboard'); // Return the 'dashboard' view when accessing the dashboard URL
})->middleware(['auth', 'verified'])->name('dashboard'); // Apply middleware to check if the user is authenticated and verified

// Group routes that require authentication
Route::middleware('auth')->group(function () {
    // Profile management routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); // Show the profile edit form
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Update the user's profile
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy'); // Delete the user's profile

    // Sales management routes
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create'); // Show the form to create a new sale
    Route::get('/sales/report', [SalesController::class, 'report'])->name('sales.report'); // Show sales report

    // Resource routes for products (CRUD operations)
    Route::resource('products', ProductController::class); // Automatically create routes for standard actions (index, create, store, show, edit, update, destroy)

    // Additional routes for products
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create'); // Show the form to create a new product
    Route::post('products', [ProductController::class, 'store'])->name('products.store'); // Store a new product in the database

    // Resource routes for sales (CRUD operations)
    Route::resource('sales', SalesController::class); // Automatically create routes for sales

    // Additional routes for sales
    Route::resource('sales', SalesController::class)->only(['create', 'store', 'index']); // Limit to create, store, and index actions

    // Route for sales report
    Route::get('/sales/report', [SalesController::class, 'report'])->name('sales.report'); // Show the sales report

    // Dashboard routes for products
    Route::get('/dashboard/products/create', [ProductController::class, 'create'])->name('dashboard.products.create'); // Show product creation form
    Route::get('/dashboard/products/{id}/edit', [ProductController::class, 'edit'])->name('dashboard.products.edit'); // Show product edit form (pass product ID)

    // Dashboard routes for sales
    Route::get('/dashboard/sales/create', [SalesController::class, 'create'])->name('dashboard.sales.create'); // Show sale creation form
    Route::get('/dashboard/sales/{id}/edit', [SalesController::class, 'edit'])->name('dashboard.sales.edit'); // Show sale edit form (pass sale ID)

    // Route for sales report (again for redundancy)
    Route::get('/sales/report', [SalesController::class, 'report'])->name('sales.report'); // Show sales report

    // Route for dashboard with sales data
    Route::get('/dashboard', function () {
        // Get monthly sales data from the database
        $monthlySales = Sale::selectRaw('MONTH(created_at) as month, SUM(total_price) as total_sales')
            ->groupBy('month') // Group by month
            ->orderBy('month') // Order by month
            ->get(); // Execute the query and get results

        // Prepare data for the graph
        $monthlyData = []; // Initialize an empty array for monthly sales data
        foreach ($monthlySales as $sale) {
            $monthlyData[$sale->month] = $sale->total_sales; // Map month to total sales
        }

        return view('dashboard', compact('monthlyData')); // Return the dashboard view with monthly sales data
    })->name('dashboard');

    Route::get('/dashboard', [SalesController::class, 'dashboard'])->name('dashboard');
});

// Include authentication routes
require __DIR__.'/auth.php'; // Load authentication routes from the auth.php file
