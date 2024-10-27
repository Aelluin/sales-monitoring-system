<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Create Product</title>
    <style>
        /* General Styling for Success and Error Messages */
        .success, .error {
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 4px;
        }

        .success {
            background-color: #2ecc71;
            color: white;
        }

        .error {
            background-color: #e74c3c;
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
            background-color: #27ae60; /* Green */
            color: white;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .create-button:hover {
            background-color: #219150; /* Darker Green */
        }

        .back-link {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            padding: 10px 20px;
            background-color: #3498db; /* Blue */
            color: white;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .back-link:hover {
            background-color: #2980b9; /* Darker Blue */
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
                <a href="/products" class="flex items-center py-3 px-4 rounded-md text-lg bg-blue-700 text-white transition-all duration-200">
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

            <main class="flex-1 p-6 bg-white">
                <div class="max-w-8xl mx-auto">
                    <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                        <h1 class="text-2xl font-bold mb-4">Create New Product</h1>

                        <!-- Success and Error Messages -->
                        @if(session('success'))
                            <div class="success">{{ session('success') }}</div>
                        @endif

                        @if(session('error'))
                            <div class="error">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('products.store') }}" method="POST">
                            @csrf

                            <label for="name">Name:</label>
                            <input type="text" name="name" id="name" required>

                            <label for="price">Price:</label>
                            <input type="text" name="price" id="price" required>

                            <label for="quantity">Quantity:</label>
                            <input type="text" name="quantity" id="quantity" required>

                            <label for="description">Description:</label>
                            <textarea name="description" id="description" rows="4"></textarea>

                            <div class="mt-4">
                                <button type="submit" class="create-button">Create Product</button>
                                <a href="{{ route('products.index') }}" class="back-link">Back to Products</a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</x-app-layout>
</body>
</html>
