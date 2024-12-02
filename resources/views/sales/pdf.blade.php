<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        /* Page Size and Margins */
        @page {
            size: A4;
            margin: 15mm; /* Smaller margins for more content */
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            font-size: 10px; /* Smaller font size for compactness */
        }

        .container {
            padding: 30px;
            max-width: 1000px;
            margin: 40px auto;
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            text-align: center;
        }

        h1 {
            font-size: 16pt; /* Smaller header */
            margin-bottom: 10px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            font-size: 9pt; /* Reduced font size */
        }

        th, td {
            padding: 6px 8px; /* Reduced padding */
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #2c3e50;
            color: #ffffff;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #ececec;
        }

        .footer {
            margin-top: 20px;
            font-size: 8pt; /* Smaller footer text */
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sales Report</h1>

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Total Price (₱)</th> <!-- Using peso symbol -->
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales->sortByDesc('created_at') as $sale)
                    <tr>
                        <td>{{ $sale->product->name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>₱{{ number_format($sale->total_price, 2) }}</td>
                        <td>{{ $sale->created_at->format('F j, Y, g:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Generated on {{ now()->format('F j, Y, g:i A') }}</p>
        </div>
    </div>
</body>
</html>
