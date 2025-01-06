<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    // Display the sales form
    public function create()
    {
        $products = Product::paginate(15);
        return view('sales.create', compact('products'));
    }

    // Process the sale and update inventory
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string|max:255',
        ]);

        // Find the product based on the product_id
        $product = Product::find($request->product_id);

        if (!$product) {
            return redirect()->route('sales.create')->with('error', 'Product not found');
        }

        // Debugging: Check stock and requested quantity
        $productStock = $product->quantity ?? 0;  // If quantity is null, default to 0

        // Check if there is enough stock available for the sale
        if ($productStock < $request->quantity) {
            return redirect()->route('sales.create')->with('error', 'Not enough stock available');
        }

        // Calculate the total price for the sale
        $totalPrice = $product->price * $request->quantity;

        // Modify payment_method to change 'credit_card' to 'credit card'
        $paymentMethod = $request->payment_method === 'credit_card' ? 'credit card' : $request->payment_method;

        // Debug: Log the payment method before saving
        Log::info('Payment Method: ' . $paymentMethod);

        // Create the sale record
        $sale = new Sale();
        $sale->product_id = $request->product_id;
        $sale->quantity = $request->quantity;
        $sale->total_price = $totalPrice;
        $sale->payment_method = $paymentMethod;  // Use the modified payment method
        $sale->customer_name = $request->customer_name;
        $sale->customer_email = $request->customer_email;
        $sale->customer_address = $request->customer_address;
        $sale->user_id = auth()->id();
        $sale->save();

        // After the sale is created, reduce the stock for the product
        $product->quantity -= $request->quantity;
        $product->save();

        // Redirect to sales index with a success message
        return redirect()->route('sales.index')->with('success', 'Sale created successfully!');
    }


    // Show all sales
    public function index()
    {
        $sales = Sale::with('product')->orderBy('created_at', 'desc')->paginate(10);
        return view('sales.index', compact('sales'));
    }

    // Generate monthly sales report
    public function report()
    {
        $monthlySales = Sale::selectRaw('MONTH(created_at) as month, SUM(total_price) as total_sales')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $monthlyData = $this->fillMissingMonths($monthlySales);
        $totalRevenue = Sale::sum('total_price');

        $bestSellingProducts = Sale::join('products', 'sales.product_id', '=', 'products.id')
            ->selectRaw('products.name as product_name, SUM(sales.quantity) as total_quantity')
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        if ($bestSellingProducts->isEmpty()) {
            $bestSellingProducts = collect([ (object)[ 'product_name' => 'No products sold', 'total_quantity' => 0 ] ]);
        }

        $paymentMethods = Sale::select('payment_method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->get();

        $paymentLabels = $paymentMethods->isEmpty() ? [] : $paymentMethods->pluck('payment_method')->toArray();
        $paymentCounts = $paymentMethods->isEmpty() ? [] : $paymentMethods->pluck('count')->toArray();

        $productNames = $bestSellingProducts->pluck('product_name')->toArray();
        $salesQuantities = $bestSellingProducts->pluck('total_quantity')->toArray();

        return view('sales.report', compact('monthlyData', 'totalRevenue', 'bestSellingProducts', 'productNames', 'salesQuantities', 'paymentLabels', 'paymentCounts'));
    }

    private function fillMissingMonths($monthlySales)
    {
        $monthlyData = [];
        foreach ($monthlySales as $sale) {
            $monthlyData[$sale->month] = $sale->total_sales;
        }

        // Ensure all months from 1 to 12 have data
        for ($month = 1; $month <= 12; $month++) {
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = 0;
            }
        }

        return $monthlyData;
    }
    public function dashboard()
    {
        // Calculate total revenue
        $totalRevenue = Sale::sum('total_price');

        // Add commas for thousands separators
        $formattedTotalRevenue = number_format($totalRevenue, 2);

        // Get the total number of sales
        $totalSalesCount = Sale::count();

        // Get the top 5 best-selling products
        $bestSellingProducts = Sale::join('products', 'sales.product_id', '=', 'products.id')
            ->selectRaw('products.name as product_name, SUM(sales.quantity) as total_quantity')
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        if ($bestSellingProducts->isEmpty()) {
            $bestSellingProducts = collect([(object)[
                'product_name' => 'No products sold',
                'total_quantity' => 0
            ]]);
        }

        // Get the recent orders
        $recentOrders = Sale::with('product')->orderBy('created_at', 'desc')->take(5)->get();

        // Seasonal sales data by season
        $seasons = [
            "Winter" => [12, 1, 2],
            "Spring" => [3, 4, 5],
            "Summer" => [6, 7, 8],
            "Fall" => [9, 10, 11],
        ];

        $seasonalData = [];
        foreach ($seasons as $season => $months) {
            $seasonalData[$season] = Sale::whereIn(DB::raw('MONTH(created_at)'), $months)
                ->sum('total_price');
        }

        // Generate a list of years starting from 2023
        $currentYear = now()->year;
        $years = range(2023, $currentYear); // Start from 2023

        return view('dashboard', compact(
            'formattedTotalRevenue',
            'totalSalesCount',
            'bestSellingProducts',
            'recentOrders',
            'seasonalData',
            'years' // Pass the years to the view
        ));
    }

    public function dailyReport(Request $request)
{
    // Get the selected date from the request, or default to today if none is provided
    $selectedDate = $request->date ?: now()->toDateString();  // default to today

    // Retrieve all sales for the selected date and eager load the product relationship
    $dailySales = Sale::with('product')->whereDate('created_at', $selectedDate)->get();

    // Calculate the total revenue for the day (use total_price directly)
    $totalRevenue = $dailySales->sum('total_price');  // Sum the total_price for each sale

    // Group sales by product and calculate the total quantity sold per product
    $salesData = $dailySales->groupBy('product_id')->map(function ($group) {
        // Eager loaded product relationship is used here
        return [
            'product' => $group->first()->product,  // Eager loaded product
            'quantity' => $group->sum('quantity'),  // Total quantity sold
           'total_price' => $group->sum('total_price'), // This should give you a sum of total_price
            'payment_method' => $group->first()->payment_method,  // Access payment method from first sale in the group
            'created_at' => $group->first()->created_at,  // Access created_at from first sale in the group
        ];
    });

    // Prepare data for the chart: product names and quantities sold
    $productNames = $salesData->pluck('product.name')->toArray();
    $salesQuantities = $salesData->pluck('quantity')->toArray();

    // Group sales by payment method and count the occurrences
    $paymentMethods = $dailySales->groupBy('payment_method')->map(function ($group) {
        return $group->count();  // Count sales per payment method
    });

    // Prepare payment methods and counts for chart
    $paymentLabels = $paymentMethods->keys()->toArray();
    $paymentCounts = $paymentMethods->values()->toArray();

    $totalSales = $dailySales->sum('quantity');  // Total quantity sold for the day

    // Pass the data to the view
    return view('sales.daily', compact(
        'totalRevenue',   // Total revenue for the day
        'salesData',      // Sales data (product name and quantity sold)
        'productNames',   // For the chart: product names
        'salesQuantities',// For the chart: quantities sold
        'selectedDate',   // The selected date for the report
        'paymentLabels',  // Payment method labels for the chart
        'paymentCounts'   // Payment method counts for the chart
    ));
}

    // Show weekly form
    public function showWeeklyForm(Request $request)
{
    $selectedYear = $request->get('year', now()->year);
    $selectedMonth = $request->get('month', now()->month);
    $selectedWeek = $request->get('week', now()->weekOfYear);

    $years = range(Carbon::now()->year - 5, Carbon::now()->year);
    $months = range(1, 12);

    // Calculate total revenue for the selected week
    $startOfWeek = Carbon::create($selectedYear, $selectedMonth, 1)
        ->startOfMonth()
        ->addWeeks($selectedWeek - 1)
        ->startOfWeek();
    $endOfWeek = $startOfWeek->copy()->endOfWeek();

    $totalRevenue = Sale::whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('total_price');

    return view('sales.weekly', compact('years', 'months', 'selectedYear', 'selectedMonth', 'selectedWeek', 'totalRevenue'));
}


    // Show weekly sales report
    public function showWeeklySales($year, $month, $week)
    {
        $startOfWeek = Carbon::create($year, $month, 1)
            ->startOfMonth()
            ->addWeeks($week - 1)
            ->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $weeklySales = Sale::whereBetween('created_at', [$startOfWeek, $endOfWeek])->get();

        $weeklyLabels = [];
        $dailySales = [];
        foreach (range(0, 6) as $dayOffset) {
            $currentDay = $startOfWeek->copy()->addDays($dayOffset);
            $dailySales[] = $weeklySales->whereBetween('created_at', [
                $currentDay->startOfDay(),
                $currentDay->endOfDay(),
            ])->sum('total_price');
            $weeklyLabels[] = $currentDay->format('l'); // Full weekday name
        }

        return view('sales.weekly', compact(
            'weeklyLabels',
            'dailySales'
        ));
    }

    public function generatePDF()
    {
        $sales = Sale::with('product')->get();

        $pdf = PDF::loadView('sales.pdf', compact('sales'))
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isPhpEnabled', true);

        return $pdf->download('sales_report.pdf');
    }

    public function previewPDF()
    {
        $sales = Sale::with('product')->get();

        $pdf = PDF::loadView('sales.pdf', compact('sales'))
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isPhpEnabled', true);

        $pdfOutput = $pdf->output();
        $pdfBase64 = base64_encode($pdfOutput);

        return view('sales.preview', compact('pdfBase64'));
    }

    public function showMonthlySales($year, $month)
    {
        $monthlySales = Sale::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $totalRevenue = $monthlySales->sum('total_price');
        $dailySalesData = $monthlySales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->day;
        });

        $dailySalesLabels = [];
        $dailySalesValues = [];

        foreach (range(1, Carbon::parse("$year-$month-01")->daysInMonth) as $day) {
            $dailySalesLabels[] = $day;
            $dailySalesValues[] = $dailySalesData->get($day, collect())->sum('total_price');
        }

        return view('sales.monthly', compact(
            'totalRevenue',
            'dailySalesLabels',
            'dailySalesValues',
            'year',
            'month'
        ));
    }
    public function getSeasonalData($year)
    {
        $seasons = [
            'Winter' => [12, 1, 2], // December, January, February
            'Spring' => [3, 4, 5],  // March, April, May
            'Summer' => [6, 7, 8],  // June, July, August
            'Fall' => [9, 10, 11],  // September, October, November
        ];

        $seasonalData = [];

        foreach ($seasons as $season => $months) {
            $totalSales = Sale::whereYear('created_at', $year)
                ->whereIn(DB::raw('MONTH(created_at)'), $months)
                ->sum('total_price');

            $seasonalData[] = [
                'season' => $season,
                'total_sales' => $totalSales,
            ];
        }

        return response()->json($seasonalData);
    }

    public function getTotalSales($year)
    {
        $totalSales = Sale::whereYear('created_at', $year)->sum('total_price');
        return response()->json([
            'total_sales' => $totalSales, // Return raw number
        ]);
    }


    public function getTopProducts($year)
    {
        $topProducts = Sale::whereYear('created_at', $year)
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5) // Adjust the number of top products as needed
            ->get();

        $products = $topProducts->map(function ($product) {
            return [
                'name' => $product->product->name,
                'sales_count' => $product->total_quantity,
            ];
        });

        return response()->json($products);
    }

    public function getRecentOrders($year)
    {
        $recentOrders = Sale::whereYear('created_at', $year)
            ->orderByDesc('created_at')
            ->limit(5) // Adjust the number of recent orders as needed
            ->get();

        $orders = $recentOrders->map(function ($order) {
            return [
                'id' => $order->id,
                'product' => $order->product,
                'created_at' => $order->created_at,
            ];
        });

        return response()->json($orders);
    }
   // Monthly sales data with improved logic for newly recorded sales
   public function monthly(Request $request)
   {
       $month = $request->input('month', Carbon::now()->format('m')); // Default to current month (numeric)
       $year = $request->input('year', Carbon::now()->format('Y')); // Default to current year

       // Create a Carbon instance for the first and last day of the selected month
       $startDate = Carbon::createFromFormat('Y-m', "$year-$month")->startOfMonth();
       $endDate = Carbon::createFromFormat('Y-m', "$year-$month")->endOfMonth();

       // Fetch paginated sales data for the selected month, sorted by created_at in descending order
       $monthlySales = Sale::with(['product']) // Eager load product details
           ->whereBetween('created_at', [$startDate, $endDate])
           ->orderBy('created_at', 'desc')  // Sort by latest sales first
           ->paginate(15);  // Paginate with 15 items per page

       // Calculate total sales and total quantity sold
       $totalSales = $monthlySales->sum('total_price');
       $totalQuantity = $monthlySales->sum('quantity');

       // Prepare data for the chart (sales by product)
       $productSales = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
           ->whereBetween('created_at', [$startDate, $endDate])
           ->groupBy('product_id')
           ->with('product')
           ->get();

       $productNames = $productSales->pluck('product.name'); // Product names
       $quantities = $productSales->pluck('total_quantity'); // Quantities sold for each product

       // Prepare data for the payment methods chart
       $paymentMethods = Sale::select('payment_method', DB::raw('count(*) as method_count'))
           ->whereBetween('created_at', [$startDate, $endDate])
           ->groupBy('payment_method')
           ->get();

       $paymentLabels = $paymentMethods->pluck('payment_method'); // Payment method names (e.g., 'Cash', 'Credit Card')
       $paymentCounts = $paymentMethods->pluck('method_count');   // Counts for each payment method

       // Get the full month name for display
       $monthName = $startDate->format('F Y');

       // Get all years with sales data for the year selector
       $years = Sale::selectRaw('YEAR(created_at) as year')
           ->distinct()
           ->orderBy('year', 'desc')
           ->pluck('year');

       // Pass the data to the view
       return view('sales.monthly', compact(
           'monthlySales',
           'totalSales',
           'totalQuantity',
           'productNames',
           'quantities',
           'paymentLabels',    // New data for chart.js
           'paymentCounts',    // New data for chart.js
           'month',
           'year',
           'monthName',
           'years'
       ));
   }


public function weekly(Request $request)
{
    // Get selected year and week or default to current year and null for week
    $year = $request->input('year', Carbon::now()->year);
    $week = (int)$request->input('week', null); // Force it to be an integer, default to null

    // Get distinct years available in the sales data
    $years = Sale::selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    // Generate weeks for the selected year
    $weeksInYear = collect();
    for ($i = 1; $i <= 53; $i++) {
        try {
            $startOfWeek = Carbon::now()->setISODate($year, $i)->startOfWeek();
            $endOfWeek = Carbon::now()->setISODate($year, $i)->endOfWeek();
            $weeksInYear->push([
                'start' => $startOfWeek,
                'end' => $endOfWeek,
            ]);
        } catch (\Exception $e) {
            break; // Stop if the week number exceeds the valid range for the year
        }
    }

    // Initialize variables for storing weekly sales and totals
    $weeklySales = collect();
    $totalSales = 0;
    $totalQuantity = 0;
    $productNames = collect();
    $quantities = collect();
    $selectedWeekStart = null;
    $selectedWeekEnd = null;

    // Initialize paymentLabels and paymentCounts to empty arrays in case no valid week is selected
    $paymentLabels = [];
    $paymentCounts = [];

    // If a valid week is selected, fetch sales data
    if ($week && $week >= 1 && $week <= count($weeksInYear)) {
        $selectedWeekStart = $weeksInYear[$week - 1]['start'];
        $selectedWeekEnd = $weeksInYear[$week - 1]['end'];

        // Fetch weekly sales data sorted by created_at in descending order
        $weeklySales = Sale::with(['product'])
            ->whereBetween('created_at', [$selectedWeekStart, $selectedWeekEnd])
            ->orderBy('created_at', 'desc') // Sort by latest sales first
            ->get();

        $totalSales = $weeklySales->sum('total_price');
        $totalQuantity = $weeklySales->sum('quantity');

        // Product sales data
        $productSales = Sale::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereBetween('created_at', [$selectedWeekStart, $selectedWeekEnd])
            ->groupBy('product_id')
            ->with('product')
            ->get();

        $productNames = $productSales->pluck('product.name');
        $quantities = $productSales->pluck('total_quantity');

        // Payment method statistics for the week
        $paymentMethodCounts = Sale::select('payment_method', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$selectedWeekStart, $selectedWeekEnd])
            ->groupBy('payment_method')
            ->get();

        // Prepare payment methods and counts for chart
        $paymentLabels = $paymentMethodCounts->pluck('payment_method')->toArray();
        $paymentCounts = $paymentMethodCounts->pluck('count')->toArray();
    }

    // Pass data to the view
    return view('sales.weekly', compact(
        'weeklySales', 'totalSales', 'totalQuantity',
        'productNames', 'quantities', 'week', 'year', 'weeksInYear', 'years',
        'selectedWeekStart', 'selectedWeekEnd', 'paymentLabels', 'paymentCounts'
    ));
}


}
