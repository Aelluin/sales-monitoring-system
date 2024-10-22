<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Sales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f2f2f2;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
        a {
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
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
                    <td colspan="4" style="text-align: center;">No sales recorded.</td>
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

    <a href="{{ route('sales.create') }}">Create New Sale</a>
</body>
</html>
