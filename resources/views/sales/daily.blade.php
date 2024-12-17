<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Daily Sales Report</title>
    <style>
        /* General Styling for Success and Error Messages */
        .success,
        .error {
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 4px;
        }

        .success {
            background-color: #2ecc71;
            /* Green */
            color: white;
        }

        .error {
            background-color: #e74c3c;
            /* Red */
            color: white;
        }

        /* General Styles */
        body {
            background-color: #ffffff;
            color: #333333;
        }

        header {
            background-color: #f8f8f8;
            color: #333333;
        }

        /* Form Styling */
        form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        /* Action Button Styling */
        .create-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #27ae60;
            /* Green */
            color: white;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .create-button:hover {
            background-color: #219150;
            /* Darker Green */
        }

        .back-link {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #3498db;
            /* Blue */
            color: white;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .back-link:hover {
            background-color: #2980b9;
            /* Darker Blue */
        }

        /* Chart Container Styling */
        .chart-container {
            position: relative;
            width: 100%;
            height: 300px;
            margin-top: 20px;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Center the content inside the chart container */
        }

        .highlighted-section {
            background-color: #f0f8ff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .chart-card {
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            overflow: hidden;
        }

        .content-wrapper {
            margin: 20px;
        }

        @media screen and (min-width: 768px) {
            .chart-container {
                height: 400px;
            }
        }

        .active {
    background-color: #1d4ed8; /* Blue background for active item */
    color: white; /* White text for active item */
}
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <x-app-layout>
        <div x-data="{ sidebarOpen: true, dropdownOpen: false }" class="flex h-screen">
            <!-- Sidebar -->
            <div :class="sidebarOpen ? 'w-64' : 'w-20'" class="flex flex-col h-full transition-all duration-300"
                style="background-color: #15151D; color: #ffffff;">
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
<div x-data="{ dropdownOpen: true }" class="relative"> <!-- Dropdown default open -->
    <a @click="dropdownOpen = !dropdownOpen" href="#" class="flex items-center py-3 px-4 rounded-md text-lg text-white hover:bg-blue-700 transition-all duration-200">
        <span class="material-icons mr-4 text-xl">assessment</span>
        <span x-show="sidebarOpen" class="flex-1 text-base">Report</span>
        <span class="material-icons ml-auto">arrow_drop_down</span>
    </a>
    <div x-show="dropdownOpen" x-transition @click.outside="dropdownOpen = false" class="pl-12 mt-2 space-y-2">
        <!-- Daily Sales -->
        <a href="/sales/daily" class="flex items-center py-2 px-4 text-sm rounded-md text-gray-200 hover:bg-blue-600 transition-all duration-200"
            :class="{'active': window.location.pathname == '/sales/daily'}"> <!-- Active state -->
            <span class="material-icons mr-2">event</span>
            Daily Sales
        </a>
        <!-- Weekly Sales -->
        <a href="/sales/weekly" class="flex items-center py-2 px-4 text-sm rounded-md text-gray-200 hover:bg-blue-600 transition-all duration-200"
            :class="{'active': window.location.pathname == '/sales/weekly'}">
            <span class="material-icons mr-2">calendar_view_week</span>
            Weekly Sales
        </a>
        <!-- Monthly Sales -->
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
                    </div>
                </header>

                <!-- Main Content -->
                <main class="flex-1 p-6 space-y-6 content-wrapper">
                    <h2 class="text-center text-xl font-semibold">Daily Sales Report</h2>

                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('sales.daily') }}" class="flex justify-center space-x-4 mt-4">
                        <label for="date" class="font-medium">Select Date:</label>
                        <input type="date" name="date" id="date" value="{{ $selectedDate }}" class="border rounded px-2 py-1">
                        <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Filter
                        </button>
                    </form>

                    <!-- Table structure -->
<table class="table table-bordered">
    <!-- Table Header -->
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Quantity Sold</th>
            <th>Total Price</th> <!-- Displaying total price -->
            <th>Payment Method</th>
            <th>Date of Sale</th>
        </tr>
    </thead>

    <!-- Table Body -->
    <tbody>
        @foreach($salesData as $sale)
        <tr>
            <td>{{ $sale['product']->name }}</td>  <!-- Accessing product name -->
            <td>{{ $sale['quantity'] }}</td>  <!-- Total quantity sold -->
            <td>₱{{ number_format($sale['total_price'], 2) }}</td>

            <td>{{ $sale['payment_method'] }}</td>  <!-- Payment method -->
            <td>{{ $sale['created_at']->format('Y-m-d') }}</td>  <!-- Date of sale -->
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Custom CSS for table design -->
<style>
    /* Enhancing table design */
    .table {
        width: 100%;
        margin: 20px 0;
        border-collapse: collapse;
        font-family: Arial, sans-serif; /* Font style */
    }

    .table th,
    .table td {
        padding: 12px 15px;
        text-align: left;
        border: 1px solid #dee2e6; /* Border around table cells */
    }

    .table th {
        background-color: #f8f9fa; /* Light gray background for headers */
        font-weight: bold;
        color: #333;
    }

    .table td {
        background-color: #fff; /* White background for data cells */
        color: #000000;
    }

    /* Hover effect for rows */
    .table tbody tr:hover {
        background-color: #f1f1f1; /* Light gray background when hovering over rows */
        cursor: pointer;
    }

    /* Styling for table borders */
    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #dee2e6;
    }

    /* Optional: add responsive behavior */
    @media (max-width: 768px) {
        .table th,
        .table td {
            padding: 8px;
        }
    }
</style>

                    <!-- Total Revenue -->
                    <div class="highlighted-section p-4 mb-6">
                        <h3 class="text-lg text-center font-semibold text-gray-800">Total Revenue: ₱{{ number_format($totalRevenue, 2) }}</h3>
                    </div>

                    <!-- Payment Methods Chart and Sales Chart -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Payment Methods Pie Chart -->
                        <div class="chart-card">
                            <h3 class="text-center text-lg font-semibold mb-4">Payment Methods</h3>
                            <div class="chart-container">
                                <canvas id="paymentChart"></canvas>
                            </div>
                        </div>

                        <!-- Sales Bar Chart -->
                        <div class="chart-card">
                            <h3 class="text-center text-lg font-semibold mb-4">Sales Performance</h3>
                            <div class="chart-container">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <script>
            // Payment Methods Pie Chart
            const paymentLabels = @json($paymentLabels);
            const paymentCounts = @json($paymentCounts);
            const paymentData = {
                labels: paymentLabels,
                datasets: [{
                    label: 'Payment Methods',
                    data: paymentCounts,
                    backgroundColor: ['rgba(255, 99, 132, 0.6)', 'rgba(54, 162, 235, 0.6)', 'rgba(255, 206, 86, 0.6)', 'rgba(75, 192, 192, 0.6)']
                }]
            };
            const paymentChartConfig = {
                type: 'pie',
                data: paymentData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom', // This will center the legend
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

            // Sales Bar Chart
            const salesNames = @json($productNames);
            const salesQuantities = @json($salesQuantities);
            const salesData = {
                labels: salesNames,
                datasets: [{
                    label: 'Quantity Sold',
                    data: salesQuantities,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            };
            const salesChartConfig = {
                type: 'bar',
                data: salesData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'start'
                        }
                    }
                }
            };
            const salesChart = new Chart(document.getElementById('salesChart'), salesChartConfig);
        </script>
    </x-app-layout>
</body>

</html>
