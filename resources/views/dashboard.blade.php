<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Dashboard</title>
    <style>
      #seasonalTrendChart {
        width: 95% !important;   /* Force the canvas to take 100% of its container width */
        height: 50vh !important;  /* Increase the height of the chart */
}


        .card {
            background: linear-gradient(145deg, #ffffff, #f7f7f7);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
        }

        .card-content {
            color: #4a5568;
            font-size: 1rem;
        }

        .chart-container {
             background: #ffffff;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    height: 60vh;   /* Make sure the container height is large enough to allow the chart to expand */
    width: 100%;
        }

        .select-container select {
            border-radius: 8px;
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            transition: border 0.3s ease;
        }

        .select-container select:hover {
            border-color: #3490dc;
        }
         /* Season Description */

    </style>
</head>

<body class="bg-gray-50">
    <x-app-layout>
        <div x-data="{ sidebarOpen: true, dropdownOpen: false }" class="flex h-screen">
            <!-- Sidebar -->
            <div :class="sidebarOpen ? 'w-64' : 'w-30'" class="flex flex-col h-full transition-all duration-300"
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
                    <a href="/dashboard"
                    class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200"
                    :class="{'bg-blue-700 text-white': window.location.pathname == '/dashboard', 'active': window.location.pathname == '/dashboard'}">
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
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <a @click="dropdownOpen = !dropdownOpen" href="#" class="flex items-center py-3 px-4 rounded-md text-lg text-white hover:bg-blue-700 transition-all duration-200">
                            <span class="material-icons mr-4 text-xl">assessment</span>
                            <span x-show="sidebarOpen" class="flex-1 text-base">Report</span>
                            <span class="material-icons ml-auto">arrow_drop_down</span>
                        </a>
                        <div x-show="dropdownOpen" x-transition @click.outside="dropdownOpen = false" class="pl-12 mt-2 space-y-2">
                            <!-- Daily Sales -->
                            <a href="/sales/daily" class="flex items-center py-2 px-4 text-sm rounded-md text-gray-200 hover:bg-blue-600 transition-all duration-200"
                                :class="{'active': window.location.pathname == '/sales/daily'}">
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

            <!-- Main Content Area -->
            <div class="flex-1 p-6 overflow-y-auto">
                <h1 class="text-4xl font-semibold text-gray-800 mb-6">Dashboard</h1>

                <!-- Year Selector -->
<div class="select-container mb-6">
    <label for="yearSelector" class="text-lg font-semibold">Select Year</label>
    <select id="yearSelector" class="p-2 border border-gray-300 rounded-md mt-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-32">
        <option value="2023">2023</option>
        <option value="2024">2024</option>
    </select>
</div>


                <!-- Seasonal Sales Trends Section -->
                <div class="chart-container mb-8">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Seasonal Sales Trends</h2>
                    <div class="flex justify-center items-center">
                        <canvas id="seasonalTrendChart" style="width: 100%; height: auto;"></canvas>
                    </div>
                </div>


                <!-- Other Dashboard Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="card p-6">
                        <h3 class="card-title">Total Sales</h3>
                        <p id="totalSales" class="card-content">Loading...</p>
                    </div>
                    <div class="card p-6">
                        <h3 class="card-title">Top Products</h3>
                        <ul id="topProducts" class="card-content">Loading top products...</ul>
                    </div>
                    <div class="card p-6">
                        <h3 class="card-title">Recent Orders</h3>
                        <ul id="recentOrders" class="card-content">Loading recent orders...</ul>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const yearSelector = document.getElementById('yearSelector');
            const year = yearSelector.value;
            fetchSeasonalData(year);
            fetchTotalSales(year);
            fetchTopProducts(year);
            fetchRecentOrders(year);

            yearSelector.addEventListener('change', (e) => {
                const selectedYear = e.target.value;
                fetchSeasonalData(selectedYear);
                fetchTotalSales(selectedYear);
                fetchTopProducts(selectedYear);
                fetchRecentOrders(selectedYear);
            });
        });

        // Function to format price as Philippine Peso (₱) with commas
        function formatPrice(amount) {
            return '₱' + amount.toLocaleString('en-PH');
        }

        // Fetching Seasonal Sales Data for the selected year
        function fetchSeasonalData(year) {
            fetch(`/dashboard/seasonal-data/${year}`)
                .then(response => response.json())
                .then(data => {
                    const seasons = ["Winter", "Spring", "Summer", "Fall"];
                    const salesData = [0, 0, 0, 0];

                    if (data && Array.isArray(data)) {
                        data.forEach(item => {
                            const seasonIndex = seasons.indexOf(item.season);
                            if (seasonIndex !== -1) {
                                salesData[seasonIndex] = item.total_sales || 0;
                            }
                        });
                    }

                    if (window.seasonalChart) {
                        window.seasonalChart.data.datasets[0].data = salesData;
                        window.seasonalChart.update();
                    } else {
                        const ctx = document.getElementById('seasonalTrendChart').getContext('2d');
                        window.seasonalChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: seasons,
                                datasets: [{
                                    label: 'Total Sales per Season',
                                    data: salesData,
                                    borderColor: '#3498DB',
                                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    pointRadius: 6,
                                    pointBackgroundColor: '#2980B9'
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    },
                                    title: {
                                        display: true,
                                        text: 'Seasonal Sales Trends (Total Sales)',
                                        font: { size: 16, weight: 'bold' },
                                        color: '#333'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: { stepSize: 1000 }
                                    }
                                }
                            }
                        });
                    }
                })
                .catch(err => {
                    console.error('Error fetching seasonal data:', err);
                    alert('Error fetching seasonal sales data');
                });
        }

        // Fetching Total Sales Data
        function fetchTotalSales(year) {
            fetch(`/dashboard/total-sales/${year}`)
                .then(response => response.json())
                .then(data => {
                    const totalSalesElement = document.querySelector('#totalSales');
                    totalSalesElement.innerText = data.total_sales ? formatPrice(data.total_sales) : 'No data available';
                })
                .catch(err => {
                    console.error('Error fetching total sales:', err);
                    document.querySelector('#totalSales').innerText = 'Error: Unable to fetch total sales';
                });
        }

        // Fetching Top Products Data
        function fetchTopProducts(year) {
            fetch(`/dashboard/top-products/${year}`)
                .then(response => response.json())
                .then(data => {
                    const topProductsContainer = document.querySelector('#topProducts');
                    if (data && data.length) {
                        topProductsContainer.innerHTML = data.map(product => {
                            return `<li>${product.name}: ${product.sales_count} units</li>`;
                        }).join('');
                    } else {
                        topProductsContainer.innerHTML = '<p>No top products available</p>';
                    }
                })
                .catch(err => {
                    console.error('Error fetching top products:', err);
                    document.querySelector('#topProducts').innerHTML = 'Error: Unable to fetch top products';
                });
        }

        // Fetching Recent Orders Data
        function fetchRecentOrders(year) {
            fetch(`/dashboard/recent-orders/${year}`)
                .then(response => response.json())
                .then(data => {
                    const recentOrdersContainer = document.querySelector('#recentOrders');
                    if (data && data.length) {
                        recentOrdersContainer.innerHTML = data.map(order => {
                            const formattedDate = new Date(order.created_at).toLocaleString('en-US', {
                                weekday: 'short',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric',
                                hour: 'numeric',
                                minute: 'numeric',
                                hour12: true
                            });
                            return `<li>Order #${order.id}: ${order.product.name} - ${formattedDate}</li>`;
                        }).join('');
                    } else {
                        recentOrdersContainer.innerHTML = '<p>No recent orders available</p>';
                    }
                })
                .catch(err => {
                    console.error('Error fetching recent orders:', err);
                    document.querySelector('#recentOrders').innerHTML = 'Error: Unable to fetch recent orders';
                });
        }
    </script>
</body>

</html>
