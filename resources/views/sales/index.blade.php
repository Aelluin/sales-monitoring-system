<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Sales Management</title>
    <style>
        /*  Success and Error Messages */
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

        /* Button Styling */
        .button {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .create-button {
            background-color: #27ae60; /* Green */
        }

        .create-button:hover {
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

        /* Custom Pagination Styling */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            flex-direction: column; /* Added for stacked layout */
        }

        .pagination .page-link {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 4px;
            background-color: #e0e0e0;
            color: #333;
            margin: 0 5px;
            text-decoration: none;
            transition: background-color 0.3s, color 0.3s;
            font-size: 18px;
        }

        .pagination .page-link:hover {
            background-color: #3498db; /* Blue */
            color: white;
        }

        .pagination .disabled {
            background-color: #f0f0f0;
            color: #999;
            pointer-events: none;
        }

        .pagination .active {
            background-color: #27ae60;
            color: white;
        }

        .bullet {
            display: inline-block;
            height: 10px;
            width: 10px;
            margin: 0 5px;
            border-radius: 50%;
            background-color: #e0e0e0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .bullet:hover {
            background-color: #3498db; /* Blue */
        }

        .bullet.active {
            background-color: #27ae60; /* Active color */
        }

        .arrow {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin: 0 10px;
            background-color: #e0e0e0;
            border-radius: 50%;
            transition: background-color 0.3s;
        }

        .arrow:hover {
            background-color: #3498db; /* Blue */
        }

        .arrow.disabled {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<x-app-layout>
    <div class="flex h-screen font-sans">

        <!-- Collapsible Sidebar -->
        <div x-data="{ open: true }" :class="open ? 'w-64' : 'w-20'" class="flex flex-col h-full" style="background-color: #15151D; color: #ffffff;">
            <div class="flex items-center justify-between p-4 border-b border-blue-700">
                <div class="flex justify-center w-full">
                    <img x-show="open" src="{{ asset('img/gg.png') }}" alt="My Dashboard" class="h-14 w-50 object-contain mx-auto" />
                </div>
                <button @click="open = !open" class="text-gray-400 hover:text-white focus:outline-none">
                    <span class="material-icons text-2xl">menu</span>
                </button>
            </div>

            <nav class="flex-1 mt-4 space-y-2 px-2">
                <a href="/dashboard" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">dashboard</span>
                    <span x-show="open" class="flex-1 text-base">Dashboard</span>
                </a>
                <a href="/products" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">inventory</span>
                    <span x-show="open" class="flex-1 text-base">Products</span>
                </a>
                <a href="/sales" class="flex items-center py-3 px-4 rounded-md text-lg bg-blue-700 text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">show_chart</span>
                    <span x-show="open" class="flex-1 text-base">Sales</span>
                </a>
                <a href="/sales/report" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">assessment</span>
                    <span x-show="open" class="flex-1 text-base">Report</span>
                </a>
            </nav>
        </div>

        <div class="flex-1 flex flex-col">
            <header class="shadow px-6 py-2 border-b border-gray-200 flex justify-end items-center h-16">
                <div class="flex items-center space-x-4">
                    <div x-data="{ open: false }" class="relative">
                        <div @click="open = !open" class="flex items-center cursor-pointer text-gray-800">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

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

            <main class="flex-1 p-6 bg-white">
                <div class="max-w-8xl mx-auto">
                    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                        <div class="p-6">
                            <!-- Header for All Sales -->
                            <h2 class="text-2xl font-semibold mb-4">All Sales</h2>

                            <!-- Success/Error Message Section -->
                            @if (session('success'))
                                <div class="success">{{ session('success') }}</div>
                            @endif
                            @if (session('error'))
                                <div class="error">{{ session('error') }}</div>
                            @endif

                            <!-- Sales Table -->
                            <table>
                                <thead>
                                    <tr>
                                        <th class="p-2 border-b">Product Name</th>
                                        <th class="p-2 border-b">Quantity Sold</th>
                                        <th class="p-2 border-b">Total Price</th>
                                        <th class="p-2 border-b">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td class="p-2 border-b">{{ $sale->product->name }}</td>
                                            <td class="p-2 border-b">{{ $sale->quantity }}</td>
                                            <td class="p-2 border-b">₱{{ number_format($sale->total_price, 2) }}</td>
                                            <td class="p-2 border-b">{{ $sale->created_at->format('F j, Y, g:i A') }}</td> <!-- Updated Date Format -->
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-4">
                                <a href="{{ route('sales.create') }}" class="button create-button">Create New Sale</a>
                                <!-- Download PDF Button -->
                                <a href="{{ route('sales.preview') }}" class="button create-button bg-blue-500 hover:bg-blue-600">
                                    Download PDF
                                </a>
                            </div>

                            <!-- Bullet Pagination -->
                            <div class="pagination">
                                <!-- Arrows and Bullets -->
                                <div class="flex items-center">
                                    <div class="arrow {{ $sales->onFirstPage() ? 'disabled' : '' }}">
                                        <a href="{{ $sales->previousPageUrl() }}" class="page-link">
                                            <span class="material-icons">arrow_back</span>
                                        </a>
                                    </div>

                                    @foreach ($sales->getUrlRange(1, $sales->lastPage()) as $page => $url)
                                        @if ($page == $sales->currentPage())
                                            <span class="bullet active"></span>
                                        @else
                                            <a href="{{ $url }}" class="bullet"></a>
                                        @endif
                                    @endforeach

                                    <div class="arrow {{ $sales->hasMorePages() ? '' : 'disabled' }}">
                                        <a href="{{ $sales->nextPageUrl() }}" class="page-link">
                                            <span class="material-icons">arrow_forward</span>
                                        </a>
                                    </div>
                                </div>
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
