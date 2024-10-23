<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management</title> <!-- Updated title -->
    <style>
        /* General body and font styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1, h2 {
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

        /* Table styling */
        table {
            width: 100%;
            max-width: 800px; /* Limit the width for better readability */
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
            background-color: #3498db; /* Blue header */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9; /* Alternate row colors */
        }

        /* Hover effect for table rows */
        tr:hover {
            background-color: #eaf6ff; /* Light blue on hover */
            transition: background-color 0.3s ease;
        }

        .success {
            color: #28a745; /* Green for success messages */
            margin-bottom: 15px;
            text-align: center; /* Center success message */
            font-size: 16px; /* Slightly larger font size */
        }

        /* Button table styling */
        .button-table {
            margin: 20px auto; /* Center the button table */
            text-align: center; /* Center align the buttons */
        }

        .button {
            text-decoration: none;
            color: white; /* White text color for button */
            padding: 12px 20px; /* Padding for button-like appearance */
            border-radius: 4px; /* Rounded corners */
            display: inline-block; /* Make it a block for spacing */
            transition: background-color 0.3s; /* Smooth background color transition */
            font-weight: bold; /* Bold font for the button */
            margin: 10px; /* Margin for spacing between buttons */
        }

        .button-blue {
            background-color: #007bff; /* Blue background for the report button */
        }

        .button-blue:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .button-green {
            background-color: #28a745; /* Green background for the create new sale button */
        }

        .button-green:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .button-gray {
            background-color: #6c757d; /* Gray background for the back button */
        }

        .button-gray:hover {
            background-color: #5a6268; /* Darker gray on hover */
        }
    </style>
</head>
<body>
    <h1>Sales Management</h1> <!-- Updated title -->

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity Sold</th>
                <th>Total Price</th>
                <th>Date of Sale</th>
            </tr>
        </thead>
        <tbody>
            @if ($sales->isEmpty())
                <tr>
                    <td colspan="4">No sales recorded.</td> <!-- Updated message -->
                </tr>
            @else
                @foreach ($sales as $sale)
                    <tr>
                        <td>{{ $sale->product->name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>₱{{ number_format($sale->total_price, 2) }}</td>
                        <td>{{ $sale->created_at->format('F j, Y, g:i A') }}</td> <!-- Formatted date -->
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="button-table">
        <a class="button button-green" href="{{ route('sales.create') }}">Add New Sale</a> <!-- Updated button text -->
        <a class="button button-blue" href="{{ route('sales.report') }}">View Sales Report</a> <!-- Updated button text -->
        <a class="button button-gray" href="{{ route('products.index') }}">Back to Products</a> <!-- New Back button -->
    </div>
</body>
</html>
