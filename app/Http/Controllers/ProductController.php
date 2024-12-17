<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // Import the Product model

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get filter parameters from the request
        $stockStatus = $request->input('stock_status', 'all'); // Default to 'all'
        $searchTerm = $request->input('search', '');
        $sortOrder = $request->input('sort_order', 'desc'); // Default is descending
        $archivedStatus = $request->input('archived_status', 'all'); // Optionally filter by archived status

        // Build query to filter products based on stock status
        $query = Product::query();

        // Apply stock filter
        if ($stockStatus !== 'all') {
            if ($stockStatus === 'in_stock') {
                $query->where('quantity', '>', 10); // Products with stock > 10
            } elseif ($stockStatus === 'low_stock') {
                $query->whereBetween('quantity', [1, 10]); // Products with stock between 1 and 10
            } elseif ($stockStatus === 'out_of_stock') {
                $query->where('quantity', 0); // Products with 0 stock
            }
        }

        // Apply archived filter (if any)
        if ($archivedStatus !== 'all') {
            $query->where('archived', $archivedStatus === 'archived' ? true : false); // Show archived or active products
        }

        // Apply search filter
        if (!empty($searchTerm)) {
            $query->where('name', 'like', '%' . $searchTerm . '%'); // Filter by product name
        }

        // Apply sorting by quantity (either ascending or descending)
        $query->orderBy('quantity', $sortOrder);

        // Fetch filtered products with pagination (9 products per page)
        $products = $query->paginate(9);

        // Pass the products to the view with the current filter parameters
        return view('products.index', compact('products', 'stockStatus', 'searchTerm', 'sortOrder', 'archivedStatus'));
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
    // Find the product by ID
    $product = Product::findOrFail($id);

    // Mark the product as archived (instead of deleting it)
    $product->archived = true;  // Set 'archived' to true
    $product->save();  // Save the changes

    // Redirect back with a success message
    return redirect()->route('products.index')->with('success', 'Product archived successfully!');
}    public function addStock(Request $request, Product $product)
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
    public function archived() {
        // Fetch all archived products without pagination
        $archivedProducts = Product::where('archived', true)->get(); // Use `get()` instead of `paginate()`

        return view('products.archived', compact('archivedProducts'));
    }

    public function unarchive($id) {
        // Find the product and unarchive it
        $product = Product::findOrFail($id);
        $product->archived = false;
        $product->save();

        // Redirect back with a success message
        return redirect()->route('products.archived')->with('success', 'Product unarchived successfully!');
    }

}
