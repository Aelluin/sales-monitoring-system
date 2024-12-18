<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserLogController;
use App\Http\Controllers\UserRoleController;
use App\Models\Sale;
use App\Models\Product;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\LogAllActions;

// Apply to all routes
Route::middleware(['auth', 'verified', LogAllActions::class])->group(function () {
    // Apply authentication, verification, and admin role middleware to the dashboard and subsequent routes
    Route::middleware(['auth', 'verified', RoleMiddleware::class . ':admin'])->group(function () {
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

    // Add stock route (keeping this outside the admin middleware since it's not specifically marked for admin)
    Route::post('products/{product}/addStock', [ProductController::class, 'addStock'])->name('products.addStock');

    // Route to get top products
    Route::get('/products/top', function () {
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
    Route::get('/orders/recent', function () {
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
    Route::get('/sales/total', function () {
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

    Route::get('/sales/recent-orders', [SalesController::class, 'recentOrders']);
    Route::get('/sales/seasonal-trends/{year}', [SalesController::class, 'seasonalTrends']);
    Route::get('/sales/top-performing-products', [SalesController::class, 'topPerformingProducts']);
    Route::get('/sales/revenue-breakdown', [SalesController::class, 'revenueBreakdown']);
    Route::get('/sales/compare/{year}', [SalesController::class, 'compareSales']);
    Route::get('/dashboard', [SalesController::class, 'dashboard'])->name('dashboard');

    // Define routes to fetch seasonal data, total sales, top products, and recent orders
    Route::get('/dashboard/seasonal-data/{year}', [SalesController::class, 'getSeasonalData']);
    Route::get('/dashboard/total-sales/{year}', [SalesController::class, 'getTotalSales']);
    Route::get('/dashboard/top-products/{year}', [SalesController::class, 'getTopProducts']);
    Route::get('/dashboard/recent-orders/{year}', [SalesController::class, 'getRecentOrders']);

    Route::get('/sales/weekly', [SalesController::class, 'weekly'])->name('sales.weekly');
    Route::get('sales/monthly', [SalesController::class, 'monthly'])->name('sales.monthly');

    // Sales staff should be redirected if they try to access the logs route
    Route::middleware(['auth', 'verified', RoleMiddleware::class . ':salesstaff'])->group(function () {
        Route::get('/logs', function () {
            return redirect()->route('dashboard');
        });
    });

    Route::get('/home', function () {
        $user = auth()->user();

        if ($user) {
            if ($user->hasRole('admin')) {
                return app(SalesController::class)->dashboard();  // Admin should access home directly
            }

            if ($user->hasRole('salesstaff')) {
                return redirect()->route('dashboard');  // Sales staff redirected to dashboard
            }
        }

        return app(HomeController::class)->index();
    })->name('home');

    Route::middleware(['auth', 'verified', RoleMiddleware::class . ':admin'])->group(function () {
        // User logs route (admin only)
        Route::get('/logs', [UserLogController::class, 'index'])->name('logs.index');
    });

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');

    Route::post('/assign-role/{user}', [UserController::class, 'assignRole'])->name('assign.role');
});

Route::post('/create-user', [UserController::class, 'store'])->name('users.create');

Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');

Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.delete');

Route::post('/users/{user}/assign-role', [UserRoleController::class, 'assignRole'])->name('admin.users.assignRole');

Route::delete('/users/{user}', [UserRoleController::class, 'delete'])->name('users.delete');

Route::get('/roles', [UserRoleController::class, 'index'])->name('role.index');



// Unarchive a product
Route::post('products/{id}/unarchive', [ProductController::class, 'unarchive'])->name('products.unarchive');

// Add this route to make "archived" accessible at /archived
Route::get('/archived', [ProductController::class, 'archived'])->name('products.archived');


// Routes for users (archiving and unarchiving)
Route::patch('/users/{id}/unarchive', [UserRoleController::class, 'unarchive'])->name('users.unarchive');
Route::patch('/users/{id}/archive', [UserRoleController::class, 'archive'])->name('users.archive');

// Route to view archived users
Route::get('/users/archived', [UserRoleController::class, 'archiveList'])->name('users.archived');


// Admin routes with the 'admin' prefix
Route::prefix('admin')->group(function () {
    Route::get('/users/archived', [UserRoleController::class, 'archiveList'])->name('admin.users.archived');  // List archived users
    Route::patch('/users/{id}/archive', [UserRoleController::class, 'archive'])->name('admin.users.archive');  // Archive user
    Route::patch('/users/{id}/unarchive', [UserRoleController::class, 'unarchive'])->name('admin.users.unarchive');  // Unarchive user
});


require __DIR__ . '/auth.php';
