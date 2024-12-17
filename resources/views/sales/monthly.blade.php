<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Monthly Sales Report</title>
    <style>
        /* Sidebar Styling */
        .active {
            background-color: #1d4ed8;
            color: white;
        }

        /* Adjust sidebar to always span the full height */
        .sidebar {
            height: 164vh !important;
            overflow-y: auto !important;
            background-color: #15151D;
            color: #ffffff;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            font-size: 14px;
        }

        table th,
        table td {
            padding: 8px 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        footer {
            margin-top: 20px;
            font-size: 14px;
        }

        /* Card Styling for Main Content */
        .content-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Header Styling */
        .header {
            background-color: #f8f9fa;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }

        .chart-container {
            position: relative;
            height: 400px;
            margin-top: 20px;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <x-app-layout>
        <div x-data="{ sidebarOpen: true, dropdownOpen: false }" class="flex h-screen">
            <!-- Sidebar -->
            <div :class="sidebarOpen ? 'w-64' : 'w-20'" class="sidebar flex flex-col transition-all duration-300">
                <div class="flex items-center justify-between p-4 border-b border-blue-700">
                    <div class="flex justify-center w-full">
                        <img x-show="sidebarOpen" src="{{ asset('img/gg.png') }}" alt="My Dashboard"
                            class="h-14 w-50 object-contain mx-auto" />
                    </div>
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-white focus:outline-none">
                        <span class="material-icons text-2xl">menu</span>
                    </button>
                </div>

                <nav class="flex-1 mt-4 space-y-2 px-2">
                    <a href="/dashboard" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200"
                        :class="{'active': window.location.pathname == '/dashboard'}">
                        <span class="material-icons mr-4 text-xl">dashboard</span>
                        <span x-show="sidebarOpen" class="flex-1 text-base">Dashboard</span>
                    </a>
                    <a href="/products" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200"
                        :class="{'active': window.location.pathname == '/products'}">
                        <span class="material-icons mr-4 text-xl">inventory</span>
                        <span x-show="sidebarOpen" class="flex-1 text-base">Products</span>
                    </a>
                    <a href="/sales" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200"
                        :class="{'active': window.location.pathname == '/sales'}">
                        <span class="material-icons mr-4 text-xl">show_chart</span>
                        <span x-show="sidebarOpen" class="flex-1 text-base">Sales</span>
                    </a>

                    <!-- Collapsible Report Menu -->
                    <div x-data="{ dropdownOpen: true }" class="relative">
                        <a @click="dropdownOpen = !dropdownOpen" href="#" class="flex items-center py-3 px-4 rounded-md text-lg text-white hover:bg-blue-700 transition-all duration-200">
                            <span class="material-icons mr-4 text-xl">assessment</span>
                            <span x-show="sidebarOpen" class="flex-1 text-base">Report</span>
                            <span class="material-icons ml-auto">arrow_drop_down</span>
                        </a>
                        <div x-show="dropdownOpen" x-transition @click.outside="dropdownOpen = false" class="pl-12 mt-2 space-y-2">
                            <a href="/sales/daily" class="flex items-center py-2 px-4 text-sm rounded-md text-gray-200 hover:bg-blue-600 transition-all duration-200"
                                :class="{'active': window.location.pathname == '/sales/daily'}">
                                <span class="material-icons mr-2">event</span>
                                Daily Sales
                            </a>
                            <a href="/sales/weekly" class="flex items-center py-2 px-4 text-sm rounded-md text-gray-200 hover:bg-blue-600 transition-all duration-200"
                                :class="{'active': window.location.pathname == '/sales/weekly'}">
                                <span class="material-icons mr-2">calendar_view_week</span>
                                Weekly Sales
                            </a>
                            <a href="/sales/monthly" class="flex items-center py-2 px-4 text-sm rounded-md text-gray-200 hover:bg-blue-600 transition-all duration-200"
                                :class="{'active': window.location.pathname == '/sales/monthly'}">
                                <span class="material-icons mr-2">date_range</span>
                                Monthly Sales
                            </a>
                        </div>
                    </div>
                    @if(Auth::user() && Auth::user()->hasRole('admin'))
                    <!-- Logs Button -->
                    <a href="/logs" class="flex items-center py-3 px-4 rounded-md text-lg text-white hover:bg-blue-700 transition-all duration-200 logs-button"
                        :class="{'logs-button-open': dropdownOpen}">
                        <span class="material-icons mr-4 text-xl">history</span>
                        <span x-show="sidebarOpen" class="flex-1 text-base">Logs</span>
                    </a>
                    <a href="/admin/users"
                        class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                        <span class="material-icons mr-4 text-xl">people</span>
                        <span x-show="sidebarOpen" class="flex-1 text-base">User Management</span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col">
                <header class="header flex justify-between items-center py-4 px-6 shadow mb-4">
                    <div class="text-lg font-bold text-gray-800">Monthly Sales Report</div>
                    <div x-data="{ open: false }" class="relative">
                        <div @click="open = !open" class="flex items-center cursor-pointer text-gray-800">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white">
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
                </header>

               <!-- Select Year and Month Moved to Top -->
<div class="flex justify-between mb-6 px-6">
    <form method="GET" action="{{ route('sales.monthly') }}" class="flex items-end space-x-4 w-full max-w-4xl">
        <div class="flex-1">
            <label for="year" class="block text-sm font-medium">Select Year:</label>
            <select name="year" id="year" class="w-full border-gray-300 rounded-md">
                @foreach ($years as $yearOption)
                    <option value="{{ $yearOption }}" {{ $yearOption == $year ? 'selected' : '' }}>{{ $yearOption }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label for="month" class="block text-sm font-medium">Select Month:</label>
            <select name="month" id="month" class="w-full border-gray-300 rounded-md">
                <option value="01" {{ $month == '01' ? 'selected' : '' }}>January</option>
                <option value="02" {{ $month == '02' ? 'selected' : '' }}>February</option>
                <option value="03" {{ $month == '03' ? 'selected' : '' }}>March</option>
                <option value="04" {{ $month == '04' ? 'selected' : '' }}>April</option>
                <option value="05" {{ $month == '05' ? 'selected' : '' }}>May</option>
                <option value="06" {{ $month == '06' ? 'selected' : '' }}>June</option>
                <option value="07" {{ $month == '07' ? 'selected' : '' }}>July</option>
                <option value="08" {{ $month == '08' ? 'selected' : '' }}>August</option>
                <option value="09" {{ $month == '09' ? 'selected' : '' }}>September</option>
                <option value="10" {{ $month == '10' ? 'selected' : '' }}>October</option>
                <option value="11" {{ $month == '11' ? 'selected' : '' }}>November</option>
                <option value="12" {{ $month == '12' ? 'selected' : '' }}>December</option>
            </select>
        </div>
        <div class="flex-shrink-0">
            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-md">Get Report</button>
        </div>
    </form>
</div>

                <!-- Main Report Section -->
                <main class="flex-1 p-6 space-y-6 content-card">
                    <h2 class="text-center text-xl font-semibold">Sales Report for: {{ $monthName }}</h2>

                    @if($monthlySales->isEmpty())
                        <p>No sales for the selected month.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table border="1" class="min-w-full bg-white rounded-lg shadow-md">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Quantity Sold</th>
                                        <th>Total Price (₱)</th>
                                        <th>Payment Method</th>
                                        <th>Sale Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($monthlySales as $sale)
                                        <tr>
                                            <td>{{ $sale->product->name }}</td>
                                            <td>{{ $sale->quantity }}</td>
                                            <td>₱{{ number_format($sale->total_price, 2) }}</td>
                                            <td>{{ $sale->payment_method }}</td>
                                            <td>{{ $sale->created_at->format('F j, Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
       <!-- Summary of Sales -->
       <div class="flex items-center justify-center">
        <div class="bg-white shadow-md rounded-lg p-6 max-w-sm text-center">
            <footer>
                <p class="text-lg  mb-2">
                    <strong>Total Sales:</strong> ₱{{ number_format($totalSales, 2) }}
                </p>
                <p class="text-lg">
                    <strong>Total Quantity Sold:</strong> {{ $totalQuantity }}
                </p>
            </footer>
        </div>
    </div>
                        <!-- Pagination Links -->
                        <div class="mt-4 ">
                            {{ $monthlySales->links() }}
                        </div>

                    @endif

                    <!-- Chart.js Bar Chart and Payment Methods Chart -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Payment Methods Chart Card -->
                        <div class="content-card">
                            <h3 class="text-center font-semibold text-lg mb-4">Payment Methods</h3>
                            <div class="chart-container">
                                <canvas id="paymentChart"></canvas>
                            </div>
                        </div>
                        <!-- Product Sales Chart Card -->
                        <div class="content-card">
                            <h3 class="text-center font-semibold text-lg mb-4">Product Sales</h3>
                            <div class="chart-container">
                                <canvas id="productSalesChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <script>
                        const paymentLabels = @json($paymentLabels);
                        const paymentCounts = @json($paymentCounts);

                        const paymentData = {
                            labels: paymentLabels,
                            datasets: [{
                                label: 'Payment Methods',
                                data: paymentCounts,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.6)',
                                    'rgba(54, 162, 235, 0.6)',
                                    'rgba(255, 206, 86, 0.6)',
                                    'rgba(75, 192, 192, 0.6)'
                                ]
                            }]
                        };

                        const paymentChartConfig = {
                            type: 'pie',
                            data: paymentData,
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        align: 'center',
                                        labels: {
                                            boxWidth: 20,
                                            padding: 10
                                        }
                                    },
                                    tooltip: {
                                        enabled: true
                                    }
                                }
                            }
                        };

                        const paymentChart = new Chart(document.getElementById('paymentChart'), paymentChartConfig);

                        // Data for the chart
                        var productNames = @json($productNames);
                        var quantities = @json($quantities);

                        // Create the chart
                        var ctx = document.getElementById('productSalesChart').getContext('2d');
                        var productSalesChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: productNames,
                                datasets: [{
                                    label: 'Products Sold',
                                    data: quantities,
                                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    </script>

                </main>
            </div>
        </div>
    </x-app-layout>
</body>

</html>
