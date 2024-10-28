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
                    <a href="/dashboard" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
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

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <header class="bg-white shadow px-6 py-2 border-b border-gray-200 flex justify-end items-center h-16">
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
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Profile') }}</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); this.closest('form').submit();"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ __('Log Out') }}</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Main Content Area -->
                <main class="flex-1 p-6 bg-gray-100">
                    <div class="max-w-7xl mx-auto">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6 text-gray-900">
                                <h2 class="text-xl font-bold mb-4 text-center">Monthly Sales Overview</h2>
                                <!-- Year Selector -->
                                <div class="mb-4">
                                    <label for="yearSelector" class="block text-gray-700">Select Year:</label>
                                    <select id="yearSelector" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        @foreach($years as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <canvas id="monthlySalesChart"></canvas>
                            </div>
                            <div class="bg-white overflow-hidden shadow-md rounded-lg p-6 text-gray-900">
                                <h2 class="text-xl font-bold mb-4 text-center">Key Metrics</h2>
                                <div class="flex flex-col space-y-4">
                                    <div class="flex justify-between">
                                        <span>Total Sales:</span>
                                        <span class="font-bold">₱{{ number_format($totalSales, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Average Order Value:</span>
                                        <span class="font-bold">₱{{ number_format($averageOrderValue, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <script>
            // Pass the monthly data to JavaScript from the PHP variable
            const monthlyData = @json($monthlyData); // Ensure this variable is passed correctly from your route

            // Function to update the chart based on the selected year
            function updateChart(selectedYear) {
                const salesData = monthlyData[selectedYear] || {};
                const chartData = [
                    salesData[1] || 0,
                    salesData[2] || 0,
                    salesData[3] || 0,
                    salesData[4] || 0,
                    salesData[5] || 0,
                    salesData[6] || 0,
                    salesData[7] || 0,
                    salesData[8] || 0,
                    salesData[9] || 0,
                    salesData[10] || 0,
                    salesData[11] || 0,
                    salesData[12] || 0
                ];

                monthlySalesChart.data.datasets[0].data = chartData;
                monthlySalesChart.update();
            }

            const ctx = document.getElementById('monthlySalesChart').getContext('2d');
            const monthlySalesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    datasets: [{
                        label: 'Sales per Month',
                        data: [
                            monthlyData[2024][1] || 0,
                            monthlyData[2024][2] || 0,
                            monthlyData[2024][3] || 0,
                            monthlyData[2024][4] || 0,
                            monthlyData[2024][5] || 0,
                            monthlyData[2024][6] || 0,
                            monthlyData[2024][7] || 0,
                            monthlyData[2024][8] || 0,
                            monthlyData[2024][9] || 0,
                            monthlyData[2024][10] || 0,
                            monthlyData[2024][11] || 0,
                            monthlyData[2024][12] || 0
                        ],
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        fill: true,
                        pointRadius: 5, // Increased size of point markers
                        pointHoverRadius: 7, // Hover effect for point markers
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return '₱' + tooltipItem.raw.toLocaleString(); // Format tooltip with peso sign
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Sales Amount'
                            },
                            ticks: {
                                callback: function(value) {
                                    // Add commas to the y-axis labels
                                    return '₱' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); // Format value with commas
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Months'
                            }
                        }
                    }
                }
            });

            // Example: Call updateChart when a year is selected
            document.getElementById('yearSelector').addEventListener('change', function() {
                updateChart(this.value); // Call the update function with the selected year
            });
        </script>

    </x-app-layout>
</body>

</html>
