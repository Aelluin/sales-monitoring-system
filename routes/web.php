<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserLogController;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

// Route for the home page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard route, protected by authentication and verification
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard route
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
        Route::get('daily', [SalesController::class, 'dailyReport'])->name('daily');

        // Monthly sales route
        Route::get('monthly', [SalesController::class, 'showMonthlySales'])->name('monthly');

        // Index route to display all sales
        Route::get('/', [SalesController::class, 'index'])->name('index');

        // Store route for creating a new sale
        Route::post('/', [SalesController::class, 'store'])->name('store');

        // Sales PDF routes
        Route::get('/pdf', [SalesController::class, 'generatePDF'])->name('pdf');
        Route::get('/preview', [SalesController::class, 'previewPDF'])->name('preview');
        Route::get('/download', [SalesController::class, 'generatePDF'])->name('pdf.download');

        // Weekly sales routes
        Route::get('weekly', [SalesController::class, 'showWeeklyForm'])->name('showWeeklyForm');
        Route::get('weekly/{year}/{month}/{week}', [SalesController::class, 'showWeeklySales'])->name('showWeeklySales');

        // Weekly redirect route
        Route::post('weekly-redirect', [SalesController::class, 'redirectToWeeklySales'])->name('weeklyRedirect');
    });

    // User logs route
    Route::get('/logs', [UserLogController::class, 'index'])->name('logs.index');
});

// Add stock route
Route::post('products/{product}/addStock', [ProductController::class, 'addStock'])->name('products.addStock');

// In routes/web.php or routes/api.php
Route::get('/sales/seasonal-trends/{year}', [SalesController::class, 'getSeasonalTrends']);

// Route to get top products
Route::get('/products/top', function() {
    try {
        // Fetch top 5 products based on the sales count
        $topProducts = Product::withCount('sales') // Assuming 'sales' is a relationship
            ->orderBy('sales_count', 'desc')
            ->take(5)
            ->get(['name', DB::raw('count(*) as sales')]);

        // Log the top products
        Log::info('Top Products:', $topProducts->toArray());

        // Check if there are no products
        if ($topProducts->isEmpty()) {
            return response()->json(['message' => 'No top products found'], 404);
        }

        return response()->json($topProducts);
    } catch (\Exception $e) {
        // Log the error and return response
        Log::error('Error fetching top products: ' . $e->getMessage());
        return response()->json(['error' => 'Error fetching top products'], 500);
    }
});

// Route to get recent orders
Route::get('/orders/recent', function() {
    try {
        // Fetch recent 5 orders along with product details
        $recentOrders = Sale::with('product') // Ensure 'product' is a relationship
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Check if there are no orders
        if ($recentOrders->isEmpty()) {
            return response()->json(['message' => 'No recent orders found'], 404);
        }

        // Map to make sure the product name and price are included
        $formattedOrders = $recentOrders->map(function ($order) {
            return [
                'order_number' => $order->id,
                'product_name' => $order->product->name ?? 'No Product',
                'total_price' => $order->total_price ?? 'No Price',
            ];
        });

        // Log the recent orders
        Log::info('Recent Orders:', $formattedOrders->toArray());

        return response()->json($formattedOrders);
    } catch (\Exception $e) {
        // Log the error and return response
        Log::error('Error fetching recent orders: ' . $e->getMessage());
        return response()->json(['error' => 'Error fetching recent orders'], 500);
    }
});

// Route to get total sales
Route::get('/sales/total', function() {
    try {
        // Calculate total sales
        $totalSales = Sale::sum('total_price');

        // Return the total sales
        return response()->json(['total' => $totalSales]);
    } catch (\Exception $e) {
        // Log the error and return response
        Log::error('Error fetching total sales: ' . $e->getMessage());
        return response()->json(['error' => 'Error fetching total sales'], 500);
    }
});

Route::get('/sales/top-products', [SalesController::class, 'topProducts']);


// Include authentication routes
require __DIR__ . '/auth.php';
