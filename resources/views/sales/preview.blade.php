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
            max-width: 1000px;
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

        /* Active link styling */
        .active {
            background-color: #1e40af;
            color: white;
        }

        .active .material-icons {
            color: white;
        }

        /* Adjusted the position of Logs button when dropdown is open */
        .logs-button {
            transition: margin-top 0.3s;
        }

        .logs-button-open {
            margin-top: 10rem;
        }

        /* PDF iframe styling */
        .iframe-container {
            width: 100%;
            height: 500px;
            margin-bottom: 1rem;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .download-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #1e40af;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .download-button:hover {
            background-color: #2563eb;
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

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">
                <main class="flex-1 p-10 bg-gray-100 flex flex-col items-center justify-center">
                    <div class="text-center mb-6">
                        <h1 class="text-3xl font-bold text-gray-700 mb-4">Sales Report Preview</h1>
                        <p class="text-gray-600">Here is the latest sales report preview. You can download the PDF by clicking the button below.</p>
                    </div>
                    <div class="iframe-container">
                        <!-- Embedded PDF Preview -->
                        <iframe src="data:application/pdf;base64,{{ $pdfBase64 }}"></iframe>
                    </div>
                    <a href="{{ route('sales.pdf.download') }}" class="download-button">Download PDF</a>
                </main>
            </div>
        </div>
    </x-app-layout>

</body>

</html>
