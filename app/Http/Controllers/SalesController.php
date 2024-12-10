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

        // Create the sale record
        $sale = new Sale();
        $sale->product_id = $request->product_id;
        $sale->quantity = $request->quantity;
        $sale->total_price = $totalPrice;
        $sale->payment_method = $request->payment_method;
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
            $bestSellingProducts = collect([ (object)[ 'product_name' => 'No products sold', 'total_quantity' => 0 ] ]);
        }

        // Get the recent orders
        $recentOrders = Sale::with('product')->orderBy('created_at', 'desc')->take(5)->get();

        // Seasonal sales data by season (mapped to months)
        $seasons = [
            "Winter" => [12, 1, 2], // December, January, February
            "Spring" => [3, 4, 5],  // March, April, May
            "Summer" => [6, 7, 8],  // June, July, August
            "Fall" => [9, 10, 11],  // September, October, November
        ];

        $seasonalData = [];
        foreach ($seasons as $season => $months) {
            $seasonalData[$season] = Sale::whereIn(DB::raw('MONTH(created_at)'), $months)
                ->sum('total_price');
        }

        return view('dashboard', compact(
            'totalRevenue',
            'totalSalesCount',
            'bestSellingProducts',
            'recentOrders',
            'seasonalData'
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
        return response()->json(['total_sales' => $totalSales]);
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

}
