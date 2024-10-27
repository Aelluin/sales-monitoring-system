<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script> <!-- Include Alpine.js -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> <!-- Include Tailwind CSS -->
    <title>Dashboard</title>
</head>
<body>
<x-app-layout>
    <!-- Full-Page Container with Sidebar and Content -->
    <div class="flex h-screen font-sans bg-gray-100">

        <!-- Collapsible Sidebar -->
        <div x-data="{ open: true }" :class="open ? 'w-64' : 'w-20'" class="flex flex-col" style="background-color: #15151D; color: #ffffff;">
            <!-- Sidebar Header with Collapse Button -->
            <div class="flex items-center justify-between p-4 border-b border-blue-700">
                <div class="flex justify-center w-full"> <!-- Center the content here -->
                    <img
                        x-show="open"
                        src="{{ asset('img/gg.png') }}"
                        alt="My Dashboard"
                        class="h-14 w-50 object-contain mx-auto" /> <!-- Add mx-auto for centering -->
                </div>
                <button @click="open = !open" class="text-gray-400 hover:text-white focus:outline-none">
                    <span class="material-icons text-2xl">menu</span> <!-- Increased icon size -->
                </button>
            </div>

            <!-- Sidebar Navigation Links -->
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
                <a href="/report" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200">
                    <span class="material-icons mr-4 text-xl">assessment</span>
                    <span x-show="open" class="flex-1 text-base">Report</span>
                </a>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- User Info Header Only -->
            <header class="bg-white shadow px-6 py-2 border-b border-gray-200 flex justify-end items-center h-16">
                <!-- User Info Section - Keeps the Laravel Breeze functionality -->
                <div class="flex items-center space-x-4">
                    <div x-data="{ open: false }" class="relative">
                        <!-- User Info Trigger -->
                        <div @click="open = !open" class="flex items-center cursor-pointer text-gray-800">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-transparent">
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    {{ __('Profile') }}
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ __('Log Out') }}
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6 bg-gray-100">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6 text-gray-900">
                            <!-- Main content goes here -->
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
</body>
</html>
