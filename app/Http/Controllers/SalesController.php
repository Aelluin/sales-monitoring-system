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

        // Check if the product has enough quantity in stock
        if ($quantity > $product->quantity) {
            return redirect()->back()->with('error', 'Not enough stock for this product.'); // Stock insufficient
        }

        // Check if the stock is low and notify the user, but allow the sale
        if ($product->quantity < $product->low_stock_threshold) {
            // Notify about low stock but still allow the sale to process
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


    public function report()
{
    // Get all sales with product details, ordered by created_at in descending order
    $sales = Sale::with('product')->orderBy('created_at', 'desc')->get();

    // Calculate total revenue
    $totalRevenue = $sales->sum('total_price');

    // Get best-selling products
    $bestSellingProducts = Sale::selectRaw('product_id, SUM(quantity) as total_quantity')
        ->groupBy('product_id')
        ->orderByDesc('total_quantity')
        ->with('product')
        ->take(5)
        ->get();

    return view('sales.report', compact('sales', 'totalRevenue', 'bestSellingProducts'));
}


}
