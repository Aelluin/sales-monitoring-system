<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script> <!-- Include Alpine.js -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> <!-- Include Tailwind CSS -->
    <title>Dashboard</title>
    <style>
        /* General Styling for Success and Error Messages */
        .success, .error {
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 4px;
        }

        .success {
            background-color: #2ecc71;
            color: white;
        }

        .error {
            background-color: #e74c3c;
            color: white;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ccc; /* Lighter border color */
        }

        table th {
            background-color: #f0f0f0;
            color: #333;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #e0e0e0;
        }

        /* Action Button Styling */
        .action-buttons a, .action-buttons button {
            text-decoration: none;
            padding: 8px 12px;
            margin: 5px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Button colors */
        .edit-button {
            background-color: #3498db; /* Blue */
        }

        .edit-button:hover {
            background-color: #2980b9; /* Darker Blue */
        }

        .delete-button {
            background-color: #e74c3c; /* Red */
        }

        .delete-button:hover {
            background-color: #c0392b; /* Darker Red */
        }

        .create-product {
            display: inline-block;
            padding: 10px 20px;
            background-color: #27ae60; /* Green */
            color: white;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .create-product:hover {
            background-color: #219150; /* Darker Green */
        }

        /* General Styles */
        body {
            background-color: #ffffff; /* White background */
            color: #333333; /* Dark text color for readability */
        }

        header {
            background-color: #f8f8f8; /* Light header */
            color: #333333; /* Dark text color */
        }
    </style>
</head>
<body>
<x-app-layout>
    <!-- Full-Page Container with Sidebar and Content -->
    <div class="flex h-screen font-sans">

        <!-- Collapsible Sidebar -->
        <div x-data="{ open: true }" :class="open ? 'w-64' : 'w-20'" class="flex flex-col h-full" style="background-color: #15151D; color: #ffffff;">
            <!-- Sidebar Header with Collapse Button -->
            <div class="flex items-center justify-between p-4 border-b border-blue-700">
                <div class="flex justify-center w-full"> <!-- Center the content here -->
                    <img
                        x-show="open"
                        src="{{ asset('img/gg.png') }}"
                        alt="My Dashboard"
                        class="h-14 w-50 object-contain mx-auto" /> <!-- Add mx-auto for centering -->
                </div>
                <button @click="open = !open" class="text-gray-400 hover:text-white focus:outline-none">
                    <span class="material-icons text-2xl">menu</span> <!-- Increased icon size -->
                </button>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="flex-1 mt-4 space-y-2 px-2">
                <a href="/dashboard" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">dashboard</span>
                    <span x-show="open" class="flex-1 text-base">Dashboard</span>
                </a>
                <a href="/products" class="flex items-center py-3 px-4 rounded-md text-lg bg-blue-700 text-white transition-all duration-200"> <!-- Changed to active -->
                    <span class="material-icons mr-4 text-xl">inventory</span>
                    <span x-show="open" class="flex-1 text-base">Products</span>
                </a>
                <a href="/sales" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">show_chart</span>
                    <span x-show="open" class="flex-1 text-base">Sales</span>
                </a>
                <a href="/sales/report" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">assessment</span>
                    <span x-show="open" class="flex-1 text-base">Report</span>
                </a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- User Info Header Only -->
            <header class="shadow px-6 py-2 border-b border-gray-200 flex justify-end items-center h-16">
                <!-- User Info Section - Keeps the Laravel Breeze functionality -->
                <div class="flex items-center space-x-4">
                    <div x-data="{ open: false }" class="relative">
                        <!-- User Info Trigger -->
                        <div @click="open = !open" class="flex items-center cursor-pointer text-gray-800">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-transparent">
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">
                                    {{ __('Profile') }}
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">
                                        {{ __('Log Out') }}
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6 bg-white">
                <div class="max-w-8xl mx-auto"> <!-- Updated max-width -->
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6 text-gray-900">

                            <h1 class="text-2xl font-bold mb-4">All Products</h1>

                            <!-- Success and Error Messages -->
                            @if(session('success'))
                                <div class="success">{{ session('success') }}</div>
                            @endif

                            @if(session('error'))
                                <div class="error">{{ session('error') }}</div>
                            @endif

                            <!-- Products Table -->
                            <table class="w-full border-collapse bg-white shadow-md">
                                <thead>
                                    <tr>
                                        <th class="p-2 border-b">Name</th>
                                        <th class="p-2 border-b">Price</th>
                                        <th class="p-2 border-b">Quantity</th>
                                        <th class="p-2 border-b">Description</th>
                                        <th class="p-2 border-b">Stock Status</th>
                                        <th class="p-2 border-b">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                        <tr>
                                            <td class="p-2 border-b">{{ $product->name }}</td>
                                            <td class="p-2 border-b">₱{{ number_format($product->price, 2) }}</td>
                                            <td class="p-2 border-b">{{ $product->quantity }}</td>
                                            <td class="p-2 border-b">{{ $product->description }}</td>
                                            <td class="p-2 border-b">
                                                @if($product->quantity <= $product->low_stock_threshold)
                                                    <span class="text-red-400">Low Stock</span>
                                                @else
                                                    <span class="text-green-400">In Stock</span>
                                                @endif
                                            </td>
                                            <td class="p-2 border-b action-buttons">
                                                <a href="{{ route('products.edit', $product->id) }}" class="edit-button">Edit</a>
                                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this product?')" class="delete-button">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-4">
                                <a href="{{ route('products.create') }}" class="create-product">Create New Product</a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
</body>
</html>
