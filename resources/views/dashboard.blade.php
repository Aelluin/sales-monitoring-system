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
            max-width: 600px;
            max-height: 400px;
        }
    </style>
</head>

<body>
    <x-app-layout>
        <div class="flex h-screen font-sans bg-gray-100">
            <!-- Sidebar -->
            <div x-data="{ open: true }" :class="open ? 'w-64' : 'w-20'" class="flex flex-col" style="background-color: #15151D; color: white;">
                <div class="flex items-center justify-between p-4 border-b border-blue-700">
                    <div class="flex justify-center w-full">
                        <img x-show="open" src="{{ asset('img/gg.png') }}" alt="My Dashboard" class="h-14 w-auto object-contain mx-auto" />
                    </div>
                    <button @click="open = !open" class="text-gray-400 hover:text-white focus:outline-none">
                        <span class="material-icons text-2xl">menu</span>
                    </button>
                </div>

                <nav class="flex-1 mt-4 space-y-2 px-2">
                    <a href="/dashboard" class="flex items-center py-3 px-4 rounded-md text-lg bg-blue-700 text-white transition-all duration-200">
                        <span class="material-icons mr-4 text-xl">dashboard</span>
                        <span x-show="open" class="flex-1 text-base">Dashboard</span>
                    </a>
                    <a href="/products" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
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

            <!-- Main content area -->
            <div class="flex-1 p-6 overflow-y-auto">
                <h1 class="text-3xl font-semibold mb-6">Dashboard</h1>

                <!-- Year Selector -->
                <div class="mb-6">
                    <label for="yearSelector" class="font-semibold text-xl">Select Year</label>
                    <select id="yearSelector" class="p-2 border border-gray-300 rounded-md mt-2">
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                    </select>
                </div>

                <!-- Seasonal Sales Trends Section -->
                <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
                    <h2 class="text-2xl font-semibold mb-4">Seasonal Sales Trends</h2>
                    <div class="flex justify-center items-center">
                        <canvas id="seasonalTrendChart" style="width: 100%; height: auto;"></canvas>
                    </div>
                </div>

                <!-- Other Dashboard Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-xl font-semibold">Total Sales</h3>
                        <p id="totalSales" class="text-gray-500">Loading...</p>
                    </div>
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-xl font-semibold">Top Products</h3>
                        <ul id="topProducts" class="text-gray-500">Loading top products...</ul>
                    </div>
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h3 class="text-xl font-semibold">Recent Orders</h3>
                        <ul id="recentOrders" class="text-gray-500">Loading recent orders...</ul>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>

    <script>
        // Function to fetch all required data
        document.addEventListener('DOMContentLoaded', () => {
            const yearSelector = document.getElementById('yearSelector');
            fetchSeasonalData(yearSelector.value);
            fetchTotalSales();
            fetchTopProducts();
            fetchRecentOrders();

            yearSelector.addEventListener('change', (e) => {
                fetchSeasonalData(e.target.value);
            });
        });

        // Fetching Seasonal Sales Trends Data
        function fetchSeasonalData(year) {
            console.log("Fetching data for year:", year);

            fetch(`/sales/seasonal-trends/${year}`)
                .then(response => response.json())
                .then(data => {
                    console.log("Fetched Data:", data);

                    const seasons = ["Winter", "Spring", "Summer", "Fall"];
                    const itemsSold = [0, 0, 0, 0];

                    data.forEach(item => {
                        const seasonIndex = seasons.indexOf(item.season);
                        if (seasonIndex !== -1) {
                            itemsSold[seasonIndex] = item.total_items_sold || 0;
                        }
                    });

                    if (window.seasonalChart) {
                        window.seasonalChart.data.datasets[0].data = itemsSold;
                        window.seasonalChart.update();
                    } else {
                        const ctx = document.getElementById('seasonalTrendChart').getContext('2d');
                        window.seasonalChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: seasons,
                                datasets: [{
                                    label: 'Total Items Sold per Season',
                                    data: itemsSold,
                                    borderColor: '#3498DB',  // Color of the line
                                    backgroundColor: 'rgba(52, 152, 219, 0.2)',  // Light background for the area under the line
                                    borderWidth: 3,  // Thicker line
                                    tension: 0.4,  // Smoother curve
                                    pointRadius: 5,  // Bigger data points
                                    pointBackgroundColor: '#2980B9'  // Point color
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            font: { size: 14 },
                                            boxWidth: 20,
                                            padding: 10,
                                        }
                                    },
                                    title: {
                                        display: true,
                                        text: 'Seasonal Sales Trends (Items Sold)',
                                        font: {
                                            size: 16,
                                            weight: 'bold'
                                        },
                                        color: '#333',
                                        padding: { top: 10, bottom: 20 }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 10,
                                        },
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.1)'  // Lighter grid lines
                                        }
                                    },
                                    x: {
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.1)'  // Lighter grid lines
                                        }
                                    }
                                }
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    alert('Error fetching seasonal data');
                });
        }

        // Fetching Total Sales Data
        function fetchTotalSales() {
            fetch('/sales/total')
                .then(response => response.json())
                .then(data => {
                    document.querySelector('#totalSales').innerText = data.total
                        ? `$${data.total.toLocaleString()}`
                        : 'No data available';
                })
                .catch(err => {
                    console.error('Error fetching total sales:', err);
                    document.querySelector('#totalSales').innerText = `Error: ${err.message}`;
                });
        }

        // Fetching Top Products Data
        function fetchTopProducts() {
            fetch('/products/top')
                .then(response => response.json())
                .then(data => {
                    const topProductsContainer = document.querySelector('#topProducts');
                    if (data && data.length) {
                        topProductsContainer.innerHTML = data.map(
                            product => `<li>${product.name}: ${product.sales} units</li>`
                        ).join('');
                    } else {
                        topProductsContainer.innerHTML = '<p>No top products available</p>';
                    }
                })
                .catch(err => {
                    console.error('Error fetching top products:', err);
                    document.querySelector('#topProducts').innerHTML = `Error: ${err.message}`;
                });
        }

        // Fetching Recent Orders Data
       public function recentOrders()
{
    $recentOrders = Sale::with('product')->orderBy('created_at', 'desc')->take(5)->get();

    // Check if orders are found
    if ($recentOrders->isEmpty()) {
        return response()->json(['message' => 'No recent orders found.'], 404);
    }

    return response()->json($recentOrders);
}

    </script>
</body>

</html>
