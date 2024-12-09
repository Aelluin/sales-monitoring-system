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
    // Example Controller for saving sales
    public function store(Request $request)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id', // Ensure product exists
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        try {
            // Retrieve the product based on product_id
            $product = Product::find($validatedData['product_id']);

            // Check if there is enough stock for the sale
            if ($product->quantity < $validatedData['quantity']) {
                return back()->with('error', 'Not enough stock available.');
            }

            // Create a new sale entry
            $sale = new Sale();
            $sale->product_id = $validatedData['product_id'];  // Use product_id, not product_name
            $sale->quantity = $validatedData['quantity'];
            $sale->payment_method = $validatedData['payment_method'];

            // Calculate total price based on the product price and quantity
            $sale->total_price = $product->price * $validatedData['quantity'];

            // Save the sale record
            $sale->save();

            // Update the product quantity
            $product->quantity -= $validatedData['quantity'];
            $product->save();

            return redirect()->route('sales.index')->with('success', 'Sale added successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }




    // Show all sales (Optional, for reporting)
    public function index()
    {
        $sales = Sale::with('product')->orderBy('created_at', 'desc')->paginate(15);
        return view('sales.index', compact('sales'));
    }

    // Generate report for monthly sales and other metrics
   // Add this in your report method
public function report()
{
    $monthlySales = Sale::selectRaw('MONTH(created_at) as month, SUM(total_price) as total_sales')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    $monthlyData = $this->fillMissingMonths($monthlySales);
    $totalRevenue = Sale::sum('total_price');

    $bestSellingProducts = Sale::selectRaw('product_id, SUM(quantity) as total_quantity')
        ->groupBy('product_id')
        ->orderByDesc('total_quantity')
        ->with('product')
        ->take(5)
        ->get();

    if ($bestSellingProducts->isEmpty()) {
        $bestSellingProducts = collect([
            (object)[
                'product' => (object)['name' => 'No products sold'],
                'total_quantity' => 0,
            ]
        ]);
    }

    // Get payment method distribution
    $paymentMethods = Sale::select('payment_method', DB::raw('count(*) as count'))
        ->groupBy('payment_method')
        ->get();

    // Prepare data for chart
    $paymentLabels = $paymentMethods->pluck('payment_method')->toArray();
    $paymentCounts = $paymentMethods->pluck('count')->toArray();

    // Prepare best-selling products data
    $productNames = $bestSellingProducts->pluck('product.name')->toArray();
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
        $monthlySales = Sale::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_price) as total_sales')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyData = [];
        foreach ($monthlySales as $sale) {
            $monthlyData[$sale->year][$sale->month] = $sale->total_sales;
        }

        $years = Sale::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year')->pluck('year')->toArray();
        foreach ($years as $year) {
            for ($month = 1; $month <= 12; $month++) {
                if (!isset($monthlyData[$year][$month])) {
                    $monthlyData[$year][$month] = 0;
                }
            }
        }

        $totalRevenue = Sale::sum('total_price');
        $totalSalesCount = Sale::count();
        $totalSales = Sale::sum('total_price');
        $averageOrderValue = $totalSalesCount > 0 ? $totalRevenue / $totalSalesCount : 0;

        return view('dashboard', compact('monthlyData', 'totalRevenue', 'totalSalesCount', 'averageOrderValue', 'totalSales', 'years'));
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

        return view('sales.daily', compact('totalRevenue', 'salesData', 'productNames', 'salesQuantities', 'selectedDate'));
    }

    public function showWeeklySales(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);
        $week = (int) $request->get('week', now()->weekOfYear);

        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        if ($week < 1 || $week > $startOfMonth->copy()->endOfMonth()->weekOfYear) {
            $week = $startOfMonth->weekOfYear;
        }

        $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek();
        $endOfWeek = Carbon::now()->setISODate($year, $week)->endOfWeek();

        if ($startOfWeek->month !== $month) {
            $startOfWeek = $startOfMonth->copy()->startOfWeek();
            $endOfWeek = $startOfMonth->copy()->endOfWeek();

            return redirect()->route('sales.showWeeklySales', [
                'year' => $year,
                'month' => $month,
                'week' => $startOfMonth->weekOfYear,
            ])->with('error', 'The selected week is not in the chosen month. Showing the first week of the month.');
        }

        try {
            $salesData = Sale::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->with('product')
                ->get();
        } catch (\Exception $e) {
            Log::error("Weekly Sales Error [Year: $year, Month: $month, Week: $week]: " . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to fetch weekly sales data.');
        }

        if ($salesData->isEmpty()) {
            return redirect()->back()->with('error', 'No sales data found for the selected week.');
        }

        $totalRevenue = $salesData->sum(fn($sale) => (float) $sale->total_price);
        $weeklyProductSales = $this->aggregateProductSales($salesData);

        $weeklyLabels = [];
        $weeklySales = [];

        foreach (range(0, 6) as $dayOffset) {
            $currentDay = $startOfWeek->copy()->addDays($dayOffset);
            $weeklyLabels[] = $currentDay->format('l');
            $weeklySales[] = $salesData->filter(function ($sale) use ($currentDay) {
                return $sale->created_at->isSameDay($currentDay);
            })->sum(fn($sale) => (float) $sale->total_price);
        }

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
}
