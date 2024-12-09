<php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserLogController;

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

        // Weekly sales route with parameters for year, month, and week
        Route::get('weekly/{year}/{month}/{week}', [SalesController::class, 'showWeeklySales'])
            ->name('showWeeklySales');

        // Monthly sales route
        Route::get('monthly', [SalesController::class, 'showMonthlySales'])->name('monthly');

        // Index route to display all sales
        Route::get('/', [SalesController::class, 'index'])->name('index');

        // Store route for creating a new sale
        Route::post('/', [SalesController::class, 'store'])->name('store');
    });

    // Sales PDF routes
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/pdf', [SalesController::class, 'generatePDF'])->name('pdf');
        Route::get('/preview', [SalesController::class, 'previewPDF'])->name('preview');
        Route::get('/download', [SalesController::class, 'generatePDF'])->name('pdf.download');
    });

    // User logs route
    Route::get('/logs', [UserLogController::class, 'index'])->name('logs.index');
});

// Include authentication routes
require __DIR__ . '/auth.php';
