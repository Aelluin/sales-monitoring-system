<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Import the Product model

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all products from the database
        $products = Product::all();
 // Fetch products with pagination, 10 products per page
 $products = Product::paginate(10);


        // Pass the products to the view
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.create'); // This will render the create product form
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'description' => 'nullable|string',
        ]);

        // Create the product
        Product::create($request->all());

        // Redirect to the products index page with a success message
        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::findOrFail($id); // Find the product by ID
    return view('products.edit', compact('product')); // Pass the product to the edit view
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ]);

    $product->update($validated);

    return redirect()->route('products.index')->with('success', 'Product updated successfully!');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id); // Find the product by ID
        $product->delete(); // Delete the product

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!'); // Redirect with success message
    }
    public function addStock(Request $request, Product $product)
    {
        // Validate the quantity input
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Add the stock to the current product's quantity
        $product->quantity += $request->input('quantity');
        $product->save();

        // Redirect back with a success message
        return redirect()->route('products.index')->with('success', 'Stock added successfully!');
    }
}
