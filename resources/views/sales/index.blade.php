<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Sales</title>
    <style>
        /* General body and font styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9; /* Light background for better contrast */
        }

        h1 {
            text-align: center; /* Center the title */
            color: #333; /* Darker color for the title */
            margin-bottom: 30px; /* Space below the title */
            font-size: 30px; /* Larger title font size */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff; /* White background for the table */
            border-radius: 8px; /* Rounded corners */
            overflow: hidden; /* Prevents border radius from being cut off */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }

        th, td {
            padding: 12px; /* Increased padding for better spacing */
            text-align: center; /* Centered content in table cells */
            border-bottom: 1px solid #e0e0e0; /* Lighter bottom border */
        }

        th {
            background-color: #dbdbdb; /* Light gray for header background */
            color: #333; /* Darker color for header text */
        }

        tr:hover {
            background-color: #f1f1f1; /* Light gray background on row hover */
        }

        .success {
            color: #28a745; /* Green color for success messages */
            margin-bottom: 15px;
            text-align: center; /* Center success message */
            font-size: 16px; /* Slightly larger font size */
        }

        a {
            text-decoration: none;
            color: white; /* White text color for button */
            padding: 12px 20px; /* Padding for button-like appearance */
            background-color: #007bff; /* Blue background for button */
            border-radius: 4px; /* Rounded corners */
            display: inline-block; /* Make it a block for spacing */
            transition: background-color 0.3s; /* Smooth background color transition */
            margin: 0 auto; /* Center the link */
            text-align: center; /* Center text in link */
            font-weight: bold; /* Bold font for the button */
        }

        a:hover {
            background-color: #0056b3; /* Darker on hover */
            text-decoration: none; /* No underline on hover */
        }

        .button-table {
            margin: 0 auto; /* Center the button table */
            text-align: center; /* Center align the buttons */
            margin-top: 20px; /* Space above button table */
        }
    </style>
</head>
<body>
    <h1>All Sales</h1>

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity Sold</th>
                <th>Total Price</th>
                <th>Sale Date</th> <!-- Added header for Sale Date -->
            </tr>
        </thead>
        <tbody>
            @if ($sales->isEmpty()) <!-- Check if there are no sales -->
                <tr>
                    <td colspan="4">No sales recorded.</td>
                </tr>
            @else
                @foreach ($sales as $sale)
                    <tr>
                        <td>{{ $sale->product->name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>&#8369;{{ number_format($sale->total_price, 2) }}</td>
                        <td>{{ $sale->created_at->format('F j, Y, g:i A') }}</td> <!-- Format the date -->
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="button-table">
        <a href="{{ route('sales.create') }}">Create New Sale</a>
    </div>
</body>
</html>
