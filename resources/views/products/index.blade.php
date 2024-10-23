<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
    <style>
        /* General styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        /* Success and error messages */
        div {
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 4px;
        }

        div.success {
            background-color: #2ecc71;
            color: white;
        }

        div.error {
            background-color: #e74c3c;
            color: white;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #3498db;
            color: white;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Action button styling */
        .action-buttons a, .action-buttons button {
            text-decoration: none;
            padding: 8px 12px;
            margin: 5px;
            color: white;
            background-color: #3498db;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .action-buttons a:hover, .action-buttons button:hover {
            background-color: #2980b9;
        }

        /* Delete button */
        .action-buttons button {
            background-color: #e74c3c;
        }

        .action-buttons button:hover {
            background-color: #c0392b;
        }

        /* Low stock styling */
        .low-stock {
            color: red;
            font-weight: bold;
        }

        /* Link for creating new product */
        .create-product {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #27ae60;
            color: white;
            text-align: center;
            border-radius: 4px;
            text-decoration: none;
        }

        .create-product:hover {
            background-color: #219150;
        }
    </style>
</head>
<body>
    <h1>All Products</h1>

    <!-- Success and Error Messages -->
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <!-- Products Table -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Description</th>
                <th>Stock Status</th> <!-- Added column for stock status -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>₱{{ number_format($product->price, 2) }}</td> <!-- Added Peso Sign and formatted price -->
                    <td>{{ $product->quantity }}</td>
                    <td>{{ $product->description }}</td>
                    <td>
                        <!-- Check for stock threshold -->
                        @if($product->quantity <= $product->low_stock_threshold)
                            <span class="low-stock">Low Stock</span>
                        @else
                            In Stock
                        @endif
                    </td>
                    <td class="action-buttons">
                        <!-- Edit link -->
                        <a href="{{ route('products.edit', $product->id) }}">Edit</a>

                        <!-- Delete form -->
                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('products.create') }}" class="create-product">Create New Product</a>
</body>
</html>
