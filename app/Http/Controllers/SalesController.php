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
    // Find the product based on the product_id
    $product = Product::find($request->product_id);

    if (!$product) {
        return redirect()->route('sales.create')->with('error', 'Product not found');
    }

    // Debugging: Check stock and requested quantity
    // dd($product->quantity, $request->quantity); // Uncomment for debugging if needed

    // Handle null stock by treating it as 0 (although it shouldn't happen due to default(0) in migration)
    $productStock = $product->quantity ?? 0;  // If quantity is null, default to 0

    // Check if there is enough stock available for the sale
    if ($productStock < $request->quantity) {
        return redirect()->route('sales.create')->with('error', 'Not enough stock available');
    }

    // Calculate the total price for the sale
    $totalPrice = $product->price * $request->quantity;

    // Create the sale record
    $sale = new Sale();
    $sale->product_id = $request->product_id;
    $sale->quantity = $request->quantity;
    $sale->total_price = $totalPrice; // Set the total price
    $sale->payment_method = $request->payment_method;
    $sale->customer_name = $request->customer_name;
    $sale->customer_email = $request->customer_email;
    $sale->customer_address = $request->customer_address;
    $sale->user_id = auth()->id(); // Set the user_id as the authenticated user
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
        $sales = Sale::with('product')->orderBy('created_at', 'desc')->paginate(15);
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

        // Calculate total number of sales
        $totalSalesCount = Sale::count();

        // Calculate the total sales value
        $totalSales = Sale::sum('total_price');

        // Calculate average order value
        $averageOrderValue = $totalSalesCount > 0 ? $totalRevenue / $totalSalesCount : 0;

        // Get the top 5 best-selling products
        $bestSellingProducts = Sale::join('products', 'sales.product_id', '=', 'products.id')
            ->selectRaw('products.name as product_name, SUM(sales.quantity) as total_quantity')
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // Get the recent orders
        $recentOrders = Sale::with('product')->orderBy('created_at', 'desc')->take(5)->get();

        return view('dashboard', compact(
            'totalRevenue',
            'totalSalesCount',
            'totalSales',
            'averageOrderValue',
            'bestSellingProducts',
            'recentOrders'
        ));
    }
    public function dailyReport(Request $request)
    {
        $selectedDate = $request->date ?: now()->toDateString();

        $dailySales = Sale::whereDate('created_at', $selectedDate)->get();

        $totalRevenue = $dailySales->sum('total_price');

        $salesData = $dailySales->groupBy('product_id')->map(function ($group) {
            return [
                'name' => $group->first()->product->name,
                'quantity' => $group->sum('quantity'),
            ];
        });

        $productNames = $salesData->pluck('name')->toArray();
        $salesQuantities = $salesData->pluck('quantity')->toArray();

        $paymentMethods = $dailySales->groupBy('payment_method')->map(function ($group) {
            return $group->count();
        });

        $paymentLabels = $paymentMethods->keys()->toArray();
        $paymentCounts = $paymentMethods->values()->toArray();

        return view('sales.daily', compact('totalRevenue', 'salesData', 'productNames', 'salesQuantities', 'selectedDate', 'paymentLabels', 'paymentCounts'));
    }

    // Show weekly form
    public function showWeeklyForm(Request $request)
    {
        $selectedYear = $request->get('year', now()->year);
        $selectedMonth = $request->get('month', now()->month);
        $selectedWeek = $request->get('week', now()->weekOfYear);

        $years = range(Carbon::now()->year - 5, Carbon::now()->year);
        $months = range(1, 12);

        return view('sales.weekly', compact('years', 'months', 'selectedYear', 'selectedMonth', 'selectedWeek'));
    }

    // Show weekly sales report
    public function showWeeklySales($year, $month, $week)
    {
        $selectedYear = $year;
        $selectedMonth = $month;
        $selectedWeek = $week;

        $startOfWeek = Carbon::create($year, $month, 1)
            ->startOfMonth()
            ->addWeeks($week - 1)
            ->startOfWeek();
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $weeklySales = Sale::whereBetween('created_at', [$startOfWeek, $endOfWeek])->get();

        $totalRevenue = $weeklySales->sum('total_price');

        $weeklyLabels = [];
        $dailySales = [];
        foreach (range(0, 6) as $dayOffset) {
            $currentDay = $startOfWeek->copy()->addDays($dayOffset);
            $dailySales[] = $weeklySales->whereBetween('created_at', [
                $currentDay->startOfDay(),
                $currentDay->endOfDay(),
            ])->sum('total_price');
            $weeklyLabels[] = $currentDay->format('l');
        }

        $weeklyProductSales = $weeklySales->groupBy('product_id')->map(function ($sales) {
            $product = $sales->first()->product;
            return [
                'product_name' => $product->name,
                'quantity_sold' => $sales->sum('quantity'),
                'total_revenue' => $sales->sum('total_price'),
            ];
        })->values();

        return view('sales.weekly', compact(
            'totalRevenue',
            'weeklySales',
            'weeklyLabels',
            'dailySales',
            'weeklyProductSales',
            'selectedYear',
            'selectedMonth',
            'selectedWeek'
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

    public function showMonthlySales(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $monthlySales = Sale::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with('product')
            ->get();

        if ($monthlySales->isEmpty()) {
            return redirect()->back()->with('error', 'No sales data found for the selected month.');
        }

        $totalRevenue = $monthlySales->sum('total_price');
        $monthlyProductSales = $this->aggregateProductSales($monthlySales);

        return view('sales.monthly', compact('monthlyProductSales', 'totalRevenue', 'year', 'month'));
    }

    // Private method to aggregate product sales
    private function aggregateProductSales($salesData)
    {
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
    // Method to get seasonal trends
    public function getSeasonalTrends($year)
    {
        // Ensure that we are filtering by the selected year
        $sales = Sale::selectRaw('
                SUM(quantity) as total_items_sold,
                CASE
                    WHEN MONTH(created_at) BETWEEN 12 AND 2 THEN "Winter"
                    WHEN MONTH(created_at) BETWEEN 3 AND 5 THEN "Spring"
                    WHEN MONTH(created_at) BETWEEN 6 AND 8 THEN "Summer"
                    WHEN MONTH(created_at) BETWEEN 9 AND 11 THEN "Fall"
                END as season
            ')
            ->whereYear('created_at', $year)  // Filter by the selected year
            ->groupBy('season')
            ->get();

        // Check if the data exists
        if ($sales->isEmpty()) {
            return response()->json(['message' => 'No sales data found for this year.'], 404);
        }

        // Return the data in a structure the frontend can use
        return response()->json($sales);
    }
    public function topProducts()
    {
        $topProducts = Sale::join('products', 'sales.product_id', '=', 'products.id')
            ->selectRaw('products.name as product_name, SUM(sales.quantity) as total_quantity')
            ->groupBy('products.name')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // Check if products are found
        if ($topProducts->isEmpty()) {
            return response()->json(['message' => 'No top products found.'], 404);
        }

        return response()->json($topProducts);
    }

    public function recentOrders()
{
    $recentOrders = Sale::with('product')->orderBy('created_at', 'desc')->take(5)->get();

    // Check if orders are found
    if ($recentOrders->isEmpty()) {
        return response()->json(['message' => 'No recent orders found.'], 404);
    }

    return response()->json($recentOrders);
}

}
