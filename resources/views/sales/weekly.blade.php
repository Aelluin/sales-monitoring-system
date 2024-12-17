<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Weekly Sales Report</title>
    <style>
        /* Sidebar Styling */
        .active {
            background-color: #1d4ed8;
            color: white;
        }

        /* Adjust sidebar to always span the full height */
        .sidebar {
            height: 200vh !important;
            overflow-y: auto !important;
            background-color: #15151D;
            color: #ffffff;
        }

        table {
    width: 100%;
    margin-top: 20px;
    border-collapse: collapse;
    font-size: 14px; /* Smaller font size */
}

table th,
table td {
    padding: 8px 10px; /* Smaller padding */
    text-align: center;
    border: 1px solid #ddd;
}

 table th {
        background-color: #f4f4f4;
        font-weight: bold;
        padding-left: 15px;  /* Adjust left padding for better alignment */
        text-align: center;  /* Ensure header text is centered */
    }


        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
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
        #pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
}

.pagination-btn {
    background-color: #313141;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.pagination-btn:hover {
    background-color: #0056b3;
}

.pagination-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
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
                    <div class="text-lg font-bold text-gray-800">Weekly Sales Report</div>
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

               <!-- Year and Week Selector Form -->
<div class="flex items-center ">
    <form method="GET" action="{{ route('sales.weekly') }}" class=" rounded-lg p-6 w-full max-w-4xl">
        <div class="p-6">
            <div class="flex items-end space-x-6">
                <!-- Year Selector -->
                <div class="flex flex-col">
                    <label for="year" class="mb-1 font-medium">Select a Year:</label>
                    <select name="year" id="year" class="border border-gray-300 rounded-md p-2 w-48">
                        <option value="">-- Choose a Year --</option>
                        @foreach ($years as $yearOption)
                            <option value="{{ $yearOption }}" {{ old('year', $year) == $yearOption ? 'selected' : '' }}>
                                {{ $yearOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Week Selector -->
                <div class="flex flex-col">
                    <label for="week" class="mb-1 font-medium">Select a Week:</label>
                    <select name="week" id="week" class="border border-gray-300 rounded-md p-2 w-48">
                        <option value="">-- Choose a Week --</option>
                        @foreach ($weeksInYear as $index => $week)
                            <option value="{{ $index + 1 }}" {{ old('week', $week) == $index + 1 ? 'selected' : '' }}>
                                Week {{ $index + 1 }}
                                ({{ \Carbon\Carbon::parse($week['start'])->format('M d') }} -
                                 {{ \Carbon\Carbon::parse($week['end'])->format('M d') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md">
                        Get Report
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

                @php
                    // Handle dynamic header text based on the week selection
                    if ($week && $week >= 1 && $week <= count($weeksInYear)) {
                        $selectedWeek = $weeksInYear[$week - 1];
                        $startDate = \Carbon\Carbon::parse($selectedWeek['start'])->format('M d');
                        $endDate = \Carbon\Carbon::parse($selectedWeek['end'])->format('M d');
                        $headerText = "Sales Report for Week $week ($startDate - $endDate)";
                    } else {
                        $headerText = "";
                    }
                @endphp

                <h2>{{ $headerText }}</h2>

                @if(empty($week) || empty($weeklySales))
                    <p>No sales data available. Please select a year and week.</p>
                @else
                    <!-- Sales Data Table -->
                    <table>
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity Sold</th>
                                <th>Total Price</th>
                                <th>Payment Method</th>
                                <th>Sale Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weeklySales as $sale)
                                <tr>
                                    <td>{{ $sale->product->name }}</td>
                                    <td>{{ $sale->quantity }}</td>
                                    <td>₱{{ number_format($sale->total_price, 2) }}</td>
                                    <td>{{ $sale->payment_method }}</td>
                                    <td>{{ $sale->created_at->format('F j, Y g:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div id="pagination" class="text-center mt-4">
                        <button onclick="changePage(1)" class="pagination-btn">1</button>
                        <button onclick="changePage(2)" class="pagination-btn">2</button>
                        <button onclick="changePage(3)" class="pagination-btn">3</button>
                        <!-- Add more pages as needed -->
                    </div>

<!-- Main Content Container -->
<div class="flex flex-col items-center space-y-8 mt-8">

    <!-- Sales Summary Card -->
    <div class="w-full bg-white p-6 rounded-lg shadow-md m-3">
        <h3 class="text-center font-bold mb-4">Sales Summary</h3>
        <footer>
            <p class="text-center"><strong>Total Sales:</strong> ₱{{ number_format($totalSales, 2) }}</p>
            <p class="text-center"><strong>Total Quantity Sold:</strong> {{ $totalQuantity }}</p>
        </footer>
    </div>
    @endif

   <!-- Main Chart Container -->
<div class="flex space-x-4 justify-center w-full">

    <!-- Card for Pie Chart (Payment Methods) -->
    <div class="w-1/3 bg-white p-4 rounded-lg shadow-md m-3 flex flex-col items-center justify-center">
        <h3 class="text-center font-bold mb-4">Payment Methods</h3>
        <div class="chart-container w-full" style="height: 400px;">
            <canvas id="paymentChart"></canvas>
        </div>
    </div>

    <!-- Card for Bar Chart (Product Sales) -->
    <div class="w-2/3 bg-white p-4 rounded-lg shadow-md m-3 flex flex-col items-center justify-center">
        <h3 class="text-center font-bold mb-4">Product Sales</h3>
        <div class="chart-container w-full" style="height: 400px;">
            <canvas id="productSalesChart"></canvas>
        </div>
    </div>

</div>


                <script>
                    // Data for the chart (ensure JSON format is correct)
                    var productNames = @json($productNames);
                    var quantities = @json($quantities);

                    // Create the chart
                    var ctx = document.getElementById('productSalesChart').getContext('2d');
                    var productSalesChart = new Chart(ctx, {
                        type: 'bar', // Bar chart
                        data: {
                            labels: productNames, // Product names as labels
                            datasets: [{
                                label: 'Products Sold',
                                data: quantities, // Quantity sold for each product
                                backgroundColor: 'rgba(54, 162, 235, 0.2)', // Bar color
                                borderColor: 'rgba(54, 162, 235, 1)', // Bar border color
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
                    let currentPage = 1;
    const rowsPerPage = 10;

    // Function to show the table based on the current page
    function displayTable() {
        const rows = document.querySelectorAll("table tbody tr");
        const totalRows = rows.length;
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        // Hide all rows
        rows.forEach(row => row.style.display = "none");

        // Show only the rows for the current page
        for (let i = start; i < end && i < totalRows; i++) {
            rows[i].style.display = "";
        }

        updatePagination(totalRows);
    }

    // Update the pagination buttons
    function updatePagination(totalRows) {
        const totalPages = Math.ceil(totalRows / rowsPerPage);
        const pagination = document.getElementById("pagination");

        // Remove existing buttons
        pagination.innerHTML = "";

        // Add new buttons
        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement("button");
            button.textContent = i;
            button.classList.add("pagination-btn");
            button.addEventListener("click", () => {
                currentPage = i;
                displayTable();
            });
            pagination.appendChild(button);
        }
    }

    // Change the page when the button is clicked
    function changePage(page) {
        currentPage = page;
        displayTable();
    }

    // Initialize the table on page load
    window.onload = displayTable;

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

                </script>
            </div>
        </div>
    </x-app-layout>
</body>
</html>
