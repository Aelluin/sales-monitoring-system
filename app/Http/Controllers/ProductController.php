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
        // You will implement this later
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // You will implement this later
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // You will implement this later
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // You will implement this later
    }
}
