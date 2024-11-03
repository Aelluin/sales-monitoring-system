<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    // Display the sales form
    public function create()
    {
        $products = Product::all(); // Get all products
        return view('sales.create', compact('products')); // Pass products to the view
    }

    // Process the sale and update inventory
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Fetch the product with the specified product_id
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;

        // Check stock levels
        if ($quantity > $product->quantity) {
            return redirect()->back()->with('error', 'Not enough stock for this product.'); // Stock insufficient
        }

        // Notify user if stock is low
        if ($product->quantity < $product->low_stock_threshold) {
            session()->flash('warning', 'Stock is low for this product!'); // Flash a warning message
        }

        // Calculate total price
        $totalPrice = $product->price * $quantity;

        // Create the sale record
        Sale::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'total_price' => $totalPrice,
        ]);

        // Update the product quantity (subtract the sold quantity)
        $product->decrement('quantity', $quantity); // More efficient way to update quantity

        return redirect()->route('sales.index')->with('success', 'Sale processed successfully!'); // Success message
    }

    // Show all sales (Optional, for reporting)
    public function index()
    {
        // Retrieve sales data with pagination, including product details, and sort by latest sales first
        $sales = Sale::with('product')->orderBy('created_at', 'desc')->paginate(15);
        return view('sales.index', compact('sales'));
    }

    // Generate report for monthly sales and other metrics
    public function report()
    {
        // Get monthly sales data
        $monthlySales = Sale::selectRaw('MONTH(created_at) as month, SUM(total_price) as total_sales')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Prepare data for the graph
        $monthlyData = $this->fillMissingMonths($monthlySales);

        // Calculate total revenue
        $totalRevenue = Sale::sum('total_price');

        // Get best-selling products
        $bestSellingProducts = Sale::selectRaw('product_id, SUM(quantity) as total_quantity')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->take(5)
            ->get();

        // Prepare product names and quantities for the chart
        $productNames = $bestSellingProducts->pluck('product.name')->toArray();
        $salesQuantities = $bestSellingProducts->pluck('total_quantity')->toArray();

        // Pass the monthly data for the chart and other report data to the view
        return view('sales.report', compact('monthlyData', 'totalRevenue', 'bestSellingProducts', 'productNames', 'salesQuantities'));
    }

    // Fill in missing months with zero sales
    private function fillMissingMonths($monthlySales)
    {
        $monthlyData = [];
        foreach ($monthlySales as $sale) {
            $monthlyData[$sale->month] = $sale->total_sales;
        }

        // Fill in missing months with zero sales
        for ($month = 1; $month <= 12; $month++) {
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = 0; // Default to 0 if no sales for that month
            }
        }

        return $monthlyData;
    }

    // Dashboard Metrics
    public function dashboard()
    {
        // Fetch monthly sales data for all years
        $monthlySales = Sale::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_price) as total_sales')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Prepare monthly data for each year
        $monthlyData = [];
        foreach ($monthlySales as $sale) {
            $monthlyData[$sale->year][$sale->month] = $sale->total_sales;
        }

        // Fill in missing months for each year with zero sales
        $years = Sale::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year')->pluck('year')->toArray();
        foreach ($years as $year) {
            for ($month = 1; $month <= 12; $month++) {
                if (!isset($monthlyData[$year][$month])) {
                    $monthlyData[$year][$month] = 0; // Default to 0 if no sales for that month
                }
            }
        }

        // Calculate total revenue and other metrics
        $totalRevenue = Sale::sum('total_price');
        $totalSalesCount = Sale::count(); // Total number of sales
        $totalSales = Sale::sum('total_price'); // Calculate total sales
        $averageOrderValue = $totalSalesCount > 0 ? $totalRevenue / $totalSalesCount : 0;

        // Pass data to the view, including total sales count and years
        return view('dashboard', compact('monthlyData', 'totalRevenue', 'totalSalesCount', 'averageOrderValue', 'totalSales', 'years'));
    }
}
