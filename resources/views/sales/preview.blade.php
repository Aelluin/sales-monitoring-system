<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Sales Report Preview</title>
    <style>
        body {
            background-color: #f5f6fa;
            color: #333333;
        }
        .iframe-container {
            width: 85%;
            height: 70vh;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .download-button {
            margin: 20px 0;
            padding: 12px 28px;
            background-color: #1d4ed8;
            color: white;
            border-radius: 25px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .download-button:hover {
            background-color: #1e40af;
            transform: scale(1.05);
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
                    <!-- Logo Section -->
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
