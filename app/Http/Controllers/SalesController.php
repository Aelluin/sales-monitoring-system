<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\Log;

class SalesController extends Controller
{
    // Display the sales form
    public function create()
    {
        // Paginate products for efficiency (can be adjusted based on your needs)
        $products = Product::paginate(15);
        return view('sales.create', compact('products'));
    }

    // Process the sale and update inventory
    public function store(Request $request)
    {
        try {
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
                return redirect()->back()->with('error', 'Not enough stock for this product.');
            }

            // Notify user if stock is low
            if ($product->quantity < $product->low_stock_threshold) {
                session()->flash('warning', 'Stock is low for this product!');
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
            $product->decrement('quantity', $quantity);

            return redirect()->route('sales.index')->with('success', 'Sale processed successfully!');
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
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

        // Prepare data for the graph (fill missing months with 0 sales)
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

        // Handle empty best-selling products scenario
        if ($bestSellingProducts->isEmpty()) {
            $bestSellingProducts = collect([ // fallback if no best-selling products are found
                (object)[
                    'product' => (object)['name' => 'No products sold'],
                    'total_quantity' => 0,
                ]
            ]);
        }

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
                    $monthlyData[$year][$month] = 0;
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

    // Daily Report
    public function dailyReport(Request $request)
    {
        // Get today's sales or filter by selected date
        $selectedDate = $request->date ?: now()->toDateString(); // Default to today's date if no date is selected
        $dailySales = Sale::whereDate('created_at', $selectedDate)->get();

        // Calculate total revenue
        $totalRevenue = $dailySales->sum('total_price');

        // Group by product to get quantities sold
        $salesData = $dailySales->groupBy('product_id')->map(function ($group) {
            return [
                'name' => $group->first()->product->name, // Adjust for product relationship
                'quantity' => $group->sum('quantity'),
            ];
        });

        $productNames = $salesData->pluck('name')->toArray();
        $salesQuantities = $salesData->pluck('quantity')->toArray();

        // Pass the necessary data to the view
        return view('sales.daily', compact('totalRevenue', 'salesData', 'productNames', 'salesQuantities', 'selectedDate'));
    }

    // Show Weekly Sales Report
    public function showWeeklySales(Request $request)
    {
        // Get the year, month, and week from the request or use defaults
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);
        $week = (int) $request->get('week', now()->weekOfYear);

        // Ensure the selected week is within the selected month
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // If the requested week is out of bounds, set it to the first week of the month
        if ($week < 1 || $week > $startOfMonth->copy()->endOfMonth()->weekOfYear) {
            $week = $startOfMonth->weekOfYear; // Default to the first week of the month
        }

        // Fetch the start and end date of the selected week within the given month
        $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $endOfWeek = Carbon::now()->setISODate($year, $week)->endOfWeek();

        // Ensure that the start and end of the week are within the selected month
        if ($startOfWeek->month !== $month) {
            // Instead of redirecting back, set a default valid week (first week of the month)
            $startOfWeek = $startOfMonth->copy()->startOfWeek();
            $endOfWeek = $startOfMonth->copy()->endOfWeek();

            // Optionally, add a message indicating the issue
            return redirect()->route('sales.showWeeklySales', [
                'year' => $year,
                'month' => $month,
                'week' => $startOfMonth->weekOfYear,
            ])->with('error', 'The selected week is not in the chosen month. Showing the first week of the month.');
        }

        try {
            // Fetch sales data for the selected week
            $salesData = Sale::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                             ->with('product')
                             ->get();
        } catch (\Exception $e) {
            Log::error("Weekly Sales Error [Year: $year, Month: $month, Week: $week]: " . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to fetch weekly sales data.');
        }

        // Ensure there's sales data
        if ($salesData->isEmpty()) {
            return redirect()->back()->with('error', 'No sales data found for the selected week.');
        }

        // Total revenue for the selected week
        $totalRevenue = $salesData->sum(fn($sale) => (float) $sale->total_price);

        // Aggregate sales by product
        $weeklyProductSales = $this->aggregateProductSales($salesData);

        // Labels for chart (the days of the week)
        $weeklyLabels = [];
        $weeklySales = [];

        // Populate the chart data
        foreach (range(0, 6) as $dayOffset) {
            $currentDay = $startOfWeek->copy()->addDays($dayOffset);
            $weeklyLabels[] = $currentDay->format('l'); // Day name
            $weeklySales[] = $salesData->filter(function ($sale) use ($currentDay) {
                return $sale->created_at->isSameDay($currentDay);
            })->sum(fn($sale) => (float) $sale->total_price);
        }

        // Pass the data to the view
        return view('sales.weekly', compact(
            'weeklyProductSales',
            'weeklyLabels',
            'weeklySales',
            'totalRevenue',
            'week',
            'year',
            'month'
        ));
    }

    // Aggregate product sales by quantity and revenue
    private function aggregateProductSales($salesData)
    {
        // Aggregate sales by product
        $productSales = [];
        foreach ($salesData as $sale) {
            $productName = $sale->product->name;
            if (!isset($productSales[$productName])) {
                $productSales[$productName] = ['quantity_sold' => 0, 'total_revenue' => 0];
            }
            $productSales[$productName]['quantity_sold'] += $sale->quantity;
            $productSales[$productName]['total_revenue'] += (float) $sale->total_price;
        }
        return $productSales;
    }

    // Generate PDF Report
    public function generatePDF()
    {
        // Retrieve sales data with associated product information
        $sales = Sale::with('product')->get();

        // Load the PDF view with the data
        $pdf = PDF::loadView('sales.pdf', compact('sales'))
                  ->setOption('isHtml5ParserEnabled', true)
                  ->setOption('isPhpEnabled', true);

        // Download the generated PDF
        return $pdf->download('sales_report.pdf');
    }

    // Preview PDF Report
    public function previewPDF()
    {
        // Retrieve sales data with associated product information
        $sales = Sale::with('product')->get();

        // Generate the PDF content without immediate download
        $pdf = PDF::loadView('sales.pdf', compact('sales'))
                  ->setOption('isHtml5ParserEnabled', true)
                  ->setOption('isPhpEnabled', true);

        // Output the PDF and encode it as base64 for embedding
        $pdfOutput = $pdf->output();
        $pdfBase64 = base64_encode($pdfOutput);

        // Return the preview view with the PDF data
        return view('sales.preview', compact('pdfBase64'));
    }

    // Show Monthly Sales
    public function showMonthlySales(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        // Fetch sales for the specified month and year
        $monthlySales = Sale::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->with('product')
                            ->get();

        // Check if there are no sales data for the selected month
        if ($monthlySales->isEmpty()) {
            return redirect()->back()->with('error', 'No sales data found for the selected month.');
        }

        // Calculate the total revenue for the month
        $totalRevenue = $monthlySales->sum('total_price');

        // Aggregate product sales by quantity and revenue
        $monthlyProductSales = $this->aggregateProductSales($monthlySales);

        return view('sales.monthly', compact('monthlyProductSales', 'totalRevenue', 'year', 'month'));
    }
}
