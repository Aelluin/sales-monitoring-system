<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        /* General body and font styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1, h2, h3 {
            text-align: center;
            color: #333;
        }

        h1 {
            margin-bottom: 20px;
        }

        h2 {
            margin-top: 10px;
            margin-bottom: 15px;
        }

        h3 {
            margin-top: 30px;
            margin-bottom: 10px;
        }

        /* Highlight section for best-selling products */
        .highlight-section {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            background-color: #b7dfff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .highlight-section h3 {
            color: #2c3e50;
        }

        /* Card-style for each best-selling product */
        .product-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            margin: 10px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s ease;
        }

        .product-card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .product-card p {
            margin: 0;
            font-size: 16px;
            text-align: center;
        }

        /* Table styling */
        table {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            text-align: center;
            padding: 12px 15px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        th {
            background-color: #3498db;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Hover effect for table rows */
        tr:hover {
            background-color: #eaf6ff;
            transition: background-color 0.3s ease;
        }

        /* General layout for better spacing */
        h2, h3, ul, table {
            margin-left: auto;
            margin-right: auto;
        }

        /* Button styling */
        .button {
            display: inline-block;
            margin: 10px; /* Margin for spacing between buttons */
            padding: 12px 20px;
            color: white; /* White text color */
            text-decoration: none; /* No underline */
            border-radius: 4px; /* Rounded corners */
            font-weight: bold; /* Bold font */
            text-align: center; /* Center text */
            transition: background-color 0.3s; /* Smooth background color transition */
        }

        .button-blue {
            background-color: #007bff; /* Blue background */
        }

        .button-blue:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .button-green {
            background-color: #28a745; /* Green background */
        }

        .button-green:hover {
            background-color: #218838; /* Darker green on hover */
        }
    </style>
</head>
<body>

    <h1>Sales Report</h1>

    <h2>Total Revenue: ₱{{ number_format($totalRevenue, 2) }}</h2>

    <!-- Highlighted section for best-selling products -->
    <div class="highlight-section">
        <h3>Best-Selling Products</h3>
        @foreach($bestSellingProducts as $product)
            <div class="product-card">
                <p><strong>{{ $product->product->name }}</strong> - {{ $product->total_quantity }} sold</p>
            </div>
        @endforeach
    </div>

    <h3>All Sales</h3>
    <table>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Date</th>
        </tr>
        @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->product->name }}</td>
                <td>{{ $sale->quantity }}</td>
                <td>₱{{ number_format($sale->total_price, 2) }}</td>
                <td>{{ $sale->created_at->format('F j, Y, g:i A') }}</td> <!-- Date formatted -->
            </tr>
        @endforeach
    </table>

    <!-- Button section -->
    <div class="button-table" style="text-align: center;">
        <a class="button button-blue" href="{{ route('sales.index') }}">Back to Sales</a>
        <a class="button button-green" href="{{ route('sales.report') }}">View Sales Report</a> <!-- Change route to the actual report route -->
    </div>

</body>
</html>
