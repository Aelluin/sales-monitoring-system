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
        // Validate incoming data
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
        ]);

        try {
            $product = Product::find($validatedData['product_id']);

            if ($product->quantity < $validatedData['quantity']) {
                return back()->with('error', 'Not enough stock available.');
            }

            $sale = new Sale();
            $sale->product_id = $validatedData['product_id'];
            $sale->quantity = $validatedData['quantity'];
            $sale->payment_method = $validatedData['payment_method'];
            $sale->total_price = $product->price * $validatedData['quantity'];
            $sale->save();

            $product->quantity -= $validatedData['quantity'];
            $product->save();

            return redirect()->route('sales.index')->with('success', 'Sale added successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
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

        // Calculate total revenue, handle cases where no sales exist
        $totalRevenue = Sale::sum('total_price') ?? 0;
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



}
