<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Sales Report</title>
    <style>
        .highlighted-section {
            background-color: #e2f0f9;
            border: 1px solid #a0d3e8;
            border-radius: 0.5rem;
        }
        .product-card {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: scale(1.05);
        }
        .chart-container {
            position: relative;
            height: 400px;
            max-width: 1200px; /* Increased max-width for more space */
            width: 100%;
            margin: 0 auto;
        }
        .chart-card {
            background-color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 1rem;
            max-width: 95%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">

<x-app-layout>
    <div x-data="{ sidebarOpen: true }" class="flex h-screen">
        <div :class="sidebarOpen ? 'w-64' : 'w-20'" class="flex flex-col h-full transition-all duration-300" style="background-color: #15151D; color: #ffffff;">
            <div class="flex items-center justify-between p-4 border-b border-blue-700">
                <div class="flex justify-center w-full">
                    <img x-show="sidebarOpen" src="{{ asset('img/gg.png') }}" alt="My Dashboard" class="h-14 w-50 object-contain mx-auto" />
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white focus:outline-none">
                    <span class="material-icons text-2xl">menu</span>
                </button>
            </div>
            <nav class="flex-1 mt-4 space-y-2 px-2">
                <a href="/dashboard" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">dashboard</span>
                    <span x-show="sidebarOpen" class="flex-1 text-base">Dashboard</span>
                </a>
                <a href="/products" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">inventory</span>
                    <span x-show="sidebarOpen" class="flex-1 text-base">Products</span>
                </a>
                <a href="/sales" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">show_chart</span>
                    <span x-show="sidebarOpen" class="flex-1 text-base">Sales</span>
                </a>
                <a href="/sales/report" class="flex items-center py-3 px-4 rounded-md text-lg bg-blue-700 text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">assessment</span>
                    <span x-show="sidebarOpen" class="flex-1 text-base">Report</span>
                </a>
            </nav>
        </div>

        <div class="flex-1 flex flex-col">
            <header class="shadow px-6 py-2 border-b border-gray-200 flex justify-end items-center h-16">
                <div class="flex items-center space-x-4">
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <div @click="dropdownOpen = !dropdownOpen" class="flex items-center cursor-pointer text-gray-800">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white">
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

            <main class="flex-1 p-6 space-y-6">
                <h2 class="text-center text-xl font-semibold">Total Revenue: ₱{{ number_format($totalRevenue, 2) }}</h2>

                <div class="highlighted-section p-4 mb-6">
                    <h3 class="text-lg text-center font-semibold text-gray-800">Best-Selling Products</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($bestSellingProducts as $product)
                            <div class="product-card">
                                <p class="text-center font-bold text-lg">{{ $product->product->name }}</p>
                                <p class="text-center text-gray-600">Quantity Sold: <strong>{{ $product->total_quantity }}</strong></p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="chart-card">
                    <h3 class="text-center text-lg font-semibold text-gray-800">Sales Chart</h3>
                    <div class="chart-container flex justify-center">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="text-center">
                    <a class="inline-block px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-200" href="{{ route('sales.index') }}">Back to Sales</a>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>

<!-- Chart.js Script -->
<script>
    const labels = @json($productNames);
    const data = {
        labels: labels,
        datasets: [{
            data: @json($salesQuantities),
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Quantity Sold',
                        font: {
                            size: 16,
                        }
                    },
                    ticks: {
                        color: '#333',
                        font: {
                            size: 14,
                        }
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Products',
                        font: {
                            size: 16,
                        }
                    },
                    ticks: {
                        color: '#333',
                        font: {
                            size: 14,
                        },
                        autoSkip: false,
                        maxRotation: 0,
                        minRotation: 0
                    }
                }
            }
        }
    };

    const salesChart = new Chart(
        document.getElementById('salesChart'),
        config
    );
</script>
</body>
</html>
