<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Sale</title>
    <style>
        /* General body and font styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9; /* Light background for better contrast */
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center; /* Center the title */
            color: #333; /* Darker color for the title */
            margin-bottom: 15px; /* Space below the title */
        }

        /* Error message styling */
        .error {
            text-align: center; /* Center error message */
            color: red; /* Error color */
            margin-bottom: 15px; /* Space below error message */
            font-size: 16px; /* Font size for error message */
        }

        /* Form styling */
        form {
            max-width: 500px; /* Set a max width for the form */
            margin: 0 auto; /* Center the form on the page */
            background-color: #fff; /* White background for the form */
            padding: 25px; /* Padding inside the form */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }

        label {
            display: block; /* Block display for labels */
            margin-bottom: 5px; /* Space below labels */
            font-weight: bold; /* Bold labels */
            font-size: 14px; /* Smaller font size */
        }

        select, input[type="number"] {
            width: 100%; /* Full width for inputs */
            max-width: 100%; /* Prevent exceeding the form width */
            padding: 8px; /* Consistent padding */
            margin-bottom: 15px; /* Space below inputs */
            border: 1px solid #ccc; /* Border for inputs */
            border-radius: 4px; /* Rounded corners */
            font-size: 14px; /* Font size for inputs */
            box-sizing: border-box; /* Include padding and border in the element's total width */
        }

        button {
            width: 100%;
            padding: 8px; /* Reduced padding */
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 14px; /* Smaller font size */
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9; /* Darker blue on hover */
        }

        /* Link to go back to products */
        .back-link {
            display: block;
            text-align: center;
            text-decoration: none;
            padding: 8px; /* Reduced padding */
            background-color: #27ae60;
            color: white;
            border-radius: 4px;
            font-size: 14px; /* Smaller font size */
        }

        .back-link:hover {
            background-color: #219150; /* Darker green on hover */
        }

        /* Table for buttons */
        .button-table {
            margin: 10px auto; /* Center the table */
            width: 500px; /* Fixed width for consistency */
            border-collapse: collapse; /* Remove spaces between cells */
        }

        .button-table td {
            padding: 10px; /* Space between buttons */
        }
    </style>
</head>
<body>
    <h1>Create New Sale</h1>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form id="sales-form" action="{{ route('sales.store') }}" method="POST">
        @csrf

        <label for="product">Product:</label>
        <select name="product_id" id="product" required>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" data-stock="{{ $product->quantity }}">
                    {{ $product->name }} - ₱{{ number_format($product->price, 2) }} (Stock: {{ $product->quantity }})
                </option>
            @endforeach
        </select>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" min="1" required placeholder="Enter quantity sold">
    </form>

    <!-- Button table for organizing buttons -->
    <table class="button-table">
        <tr>
            <td>
                <button type="submit" form="sales-form">Process Sale</button>
            </td>
        </tr>
        <tr>
            <td>
                <a href="{{ route('products.index') }}" class="back-link">Back to Products</a>
            </td>
        </tr>
    </table>

    <script>
        // Update quantity input's max attribute based on selected product's stock
        document.getElementById('product').addEventListener('change', function() {
            var selectedProduct = this.options[this.selectedIndex];
            var availableStock = parseInt(selectedProduct.getAttribute('data-stock')); // Get stock from data attribute
            document.getElementById('quantity').setAttribute('max', availableStock); // Set the max value for quantity input
        });

        // Trigger the change event on page load to set the initial max value
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('product').dispatchEvent(new Event('change'));
        });
    </script>
</body>
</html>
