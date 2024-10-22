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
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;

        // Check if the product has enough quantity in stock
        if ($quantity > $product->quantity) {
            return redirect()->back()->with('error', 'Not enough stock for this product.');
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
        $product->update([
            'quantity' => $product->quantity - $quantity,
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale processed successfully!');
    }

    // Show all sales (Optional, for reporting)
    public function index()
    {
        $sales = Sale::with('product')->get();
        return view('sales.index', compact('sales'));
    }
}
