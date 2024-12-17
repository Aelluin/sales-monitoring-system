<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script> <!-- Include Alpine.js -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> <!-- Include Tailwind CSS -->
    <title>Dashboard</title>
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

        /* Table Styling */
        table {
            width: 95%; /* Set table to a smaller width */
            margin: 0 auto;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 10px 15px; /* Reduced padding for a more compact look */
            text-align: center;
            border-bottom: 1px solid #ccc; /* Lighter border color */
        }

        table th {
            background-color: #f0f0f0;
            color: #333;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #e0e0e0;
        }

        /* Action Button Styling */
        .action-buttons {
            display: flex;
            justify-content: space-evenly;
        }

        .action-buttons a, .action-buttons button {
            text-decoration: none;
            padding: 6px 12px;
            margin: 2px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 0.9rem;
        }

        /* Button colors */
        .edit-button {
            background-color: #3498db; /* Blue */
        }

        .edit-button:hover {
            background-color: #2980b9; /* Darker Blue */
        }

        .delete-button {
            background-color: #e74c3c; /* Red */
        }

        .delete-button:hover {
            background-color: #c0392b; /* Darker Red */
        }

        .create-product {
            display: inline-block;
            padding: 8px 16px;
            background-color: #27ae60;
            color: white;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .create-product:hover {
            background-color: #219150;
        }

        /* General Styles */
        body {
            background-color: #ffffff;
            color: #333333;
        }

        header {
            background-color: #f8f8f8;
            color: #333333; /* Dark text color */
        }
    </style>
</head>
<body>
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
                <a href="/dashboard"
                class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200"
                :class="{'bg-blue-700 text-white': window.location.pathname == '/dashboard', 'active': window.location.pathname == '/dashboard'}">
                 <span class="material-icons mr-4 text-xl">dashboard</span>
                 <span x-show="sidebarOpen" class="flex-1 text-base">Dashboard</span>
             </a>
                <a href="/products" class="flex items-center py-3 px-4 rounded-md text-lg hover:bg-blue-700 hover:text-white transition-all duration-200"
                :class="{'bg-blue-700 text-white': window.location.pathname == '/products', 'active': window.location.pathname == '/products'}">
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

            <main class="flex-1 p-6 bg-white">
                <div class="flex-1 p-6 bg-white">
                    <div class="max-w-8xl mx-auto">
                        <div class="bg-white overflow-hidden shadow-md rounded-lg">
                            <div class="p-6 text-gray-900">

                                <h1 class="text-2xl font-bold mb-4">All Products</h1>

                                @if(session('success'))
                                    <div class="success">{{ session('success') }}</div>
                                @endif

                                @if(session('error'))
                                    <div class="error">{{ session('error') }}</div>
                                @endif

                                <!-- Aligning the Dropdown to the Left -->
                                <div class="mb-4 flex items-center space-x-4">
                                    <form method="GET" action="{{ route('products.index') }}">

                                        <!-- Stock Filter -->
                                        <select name="stock_status">
                                            <option value="all" {{ request('stock_status') === 'all' ? 'selected' : '' }}>All</option>
                                            <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                            <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                        </select>

                                        <!-- Submit Button -->
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded  hover:bg-blue-600 transition-colors duration-200">Filter</button>
                                    </form>

                                </div>
                                <div class="mb-4">
                                    <label for="search" class="block text-sm text-gray-700 mb-2">Search Products:</label>
                                    <div class="flex">
                                        <input
                                            type="text"
                                            id="search"
                                            placeholder="Enter product name..."
                                            class="px-4 border focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
                                        />
                                        <button
                                            class="ml-2 px-4 py-2 bg-blue-500 text-white rounded focus:outline-none hover:bg-blue-600 transition-colors duration-200"
                                            onclick="searchProducts()">
                                            Search
                                        </button>
                                    </div>
                                </div>


                              <!-- Table -->
<table>
    <thead>
        <tr>
            <th class="p-2 border-b">Name</th>
            <th class="p-2 border-b">Price</th>
            <th class="p-2 border-b">Quantity</th>
            <th class="p-2 border-b">Description</th>
            <th class="p-2 border-b">Stock Status</th>
            <th class="p-2 border-b">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td class="p-2 border-b">{{ $product->name }}</td>
                <td class="p-2 border-b">₱{{ number_format($product->price, 2) }}</td>
                <td class="p-2 border-b">{{ $product->quantity }}</td>
                <td class="p-2 border-b">{{ $product->description }}</td>
                <td class="p-2 border-b">
                    <span class="
                    {{ $product->quantity == 0 ? 'text-gray-500' : ($product->quantity <= $product->low_stock_threshold ? 'text-red-400' : 'text-green-400') }}">
                    {{ $product->quantity == 0 ? 'Out of Stock' : ($product->quantity <= $product->low_stock_threshold ? 'Low Stock' : 'In Stock') }}
                </span>
                </td>
                <td class="p-2 border-b action-buttons">
                    <a href="{{ route('products.edit', $product->id) }}" class="edit-button">Edit</a>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="delete-button">Delete</button>
                    </form>


                    <div x-data="{ openAddStockModal: false }">
                        <!-- Add Stock Button -->
                        <button @click="openAddStockModal = true" class="add-stock-button bg-green-500 hover:bg-green-400 text-white py-2 px-4 rounded">
                            Add Stock
                        </button>

                        <!-- Add Stock Modal -->
                        <div x-show="openAddStockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center" @click.away="openAddStockModal = false">
                            <div class="bg-white p-6 rounded-lg w-80">
                                <h3 class="text-xl mb-4">Add Stock to {{ $product->name }}</h3>
                                <form action="{{ route('products.addStock', $product->id) }}" method="POST">
                                    @csrf
                                    <label for="quantity" class="block mb-2">Enter Quantity to Add</label>
                                    <input type="number" id="quantity" name="quantity" class="border p-2 w-full mb-4" required />
                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded mr-2">Add</button>
                                        <button type="button" @click="openAddStockModal = false" class="bg-red-500 text-white py-2 px-4 rounded">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Pagination (Placed after the table) -->
<div class="mt-4">
    {{ $products->links() }}
</div>


                                <div class="mt-4">
                                    <a href="{{ route('products.create') }}" class="create-product">Add Product</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</x-app-layout>
<script>
   let allProducts = []; // Array to hold all products
let currentPage = 1;
const rowsPerPage = 10; // Number of rows per page

function fetchProducts() {
    const productsTable = document.querySelector('table tbody');
    const rows = Array.from(productsTable.rows);

    // Populate the allProducts array with all products data
    allProducts = rows.map(row => {
        const qty = parseInt(row.cells[2].textContent.trim()); // Assuming stock is in the 3rd column
        const productName = row.cells[0].textContent.trim(); // Assuming name is in the 1st column
        return { row, qty, productName };
    });

    // Sort and filter products initially
    sortProducts(); // Sort and display products on the first page
}

function sortProducts(event) {
    const sortOption = event ? event.target.value : 'in_stock_first'; // Default sort option

    // Sort all products based on stock status
    allProducts.sort((a, b) => {
        let stockStatusA = getStockStatus(a.qty);
        let stockStatusB = getStockStatus(b.qty);

        // Sort logic based on the selected option
        if (sortOption === 'in_stock_first') {
            return stockStatusB - stockStatusA; // In Stock First
        } else if (sortOption === 'low_stock_first') {
            return stockStatusA - stockStatusB; // Low Stock First
        } else if (sortOption === 'out_of_stock_first') {
            return stockStatusA - stockStatusB; // Out of Stock First (added option)
        }
        return 0;
    });

    displayPaginatedProducts(); // Display the products after sorting
}

// Helper function to determine stock status
function getStockStatus(quantity) {
    if (quantity === 0) {
        return 0; // Out of Stock
    } else if (quantity <= 10) { // Low stock threshold
        return 1; // Low Stock
    } else {
        return 2; // In Stock
    }
}

// Function to display paginated products
function displayPaginatedProducts() {
    const productsTable = document.querySelector('table tbody');

    // Clear current table rows
    productsTable.innerHTML = '';

    // Get the rows for the current page
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const pageProducts = allProducts.slice(startIndex, endIndex);

    // Append the rows for the current page
    pageProducts.forEach(product => {
        productsTable.appendChild(product.row);
    });

    updatePaginationControls(); // Update pagination controls
}

// Function to update pagination controls based on the current page
function updatePaginationControls() {
    const totalPages = Math.ceil(allProducts.length / rowsPerPage);
    const paginationControls = document.querySelector('.pagination');
    paginationControls.innerHTML = '';

    // Add previous button
    const prevButton = document.createElement('button');
    prevButton.textContent = 'Previous';
    prevButton.disabled = currentPage === 1;
    prevButton.addEventListener('click', () => changePage(currentPage - 1));
    paginationControls.appendChild(prevButton);

    // Add page number buttons
    for (let page = 1; page <= totalPages; page++) {
        const pageButton = document.createElement('button');
        pageButton.textContent = page;
        pageButton.disabled = page === currentPage;
        pageButton.addEventListener('click', () => changePage(page));
        paginationControls.appendChild(pageButton);
    }

    // Add next button
    const nextButton = document.createElement('button');
    nextButton.textContent = 'Next';
    nextButton.disabled = currentPage === totalPages;
    nextButton.addEventListener('click', () => changePage(currentPage + 1));
    paginationControls.appendChild(nextButton);
}

// Function to change the current page and display products for that page
function changePage(page) {
    currentPage = page;
    displayPaginatedProducts();
}

// Search function for product names
function searchProducts() {
    const searchInput = document.getElementById('search').value.toLowerCase();

    // Filter products based on search term
    const filteredProducts = allProducts.filter(product =>
        product.productName.toLowerCase().includes(searchInput)
    );

    // Update the table with filtered products
    displayProducts(filteredProducts);
}

// Display filtered products
function displayProducts(products) {
    const productsTable = document.querySelector('table tbody');
    productsTable.innerHTML = '';

    products.forEach(product => {
        productsTable.appendChild(product.row);
    });
}

// Function to filter products by stock status (In Stock, Low Stock, Out of Stock)
function filterByStock(status) {
    let filteredProducts;

    if (status === 'in_stock') {
        filteredProducts = allProducts.filter(product => getStockStatus(product.qty) === 2);
    } else if (status === 'low_stock') {
        filteredProducts = allProducts.filter(product => getStockStatus(product.qty) === 1);
    } else if (status === 'out_of_stock') {
        filteredProducts = allProducts.filter(product => getStockStatus(product.qty) === 0);
    }

    displayProducts(filteredProducts); // Display the filtered products
}

// Initialize when page loads
window.onload = function() {
    fetchProducts(); // Fetch all products and set up the table
};

</script>

</body>
</html>
