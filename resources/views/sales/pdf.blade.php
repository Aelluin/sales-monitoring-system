<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <style>
        /* Page Size and Margins */
        @page {
            size: A4 landscape; /* Set the page to landscape orientation */
            margin: 15mm 20mm; /* Added margin for the sides and top/bottom */
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
            width: 100%;
            max-width: 850px; /* Maximum container width */
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            text-align: center;
            margin: 0 auto; /* Ensure container is centered */
        }

        h1 {
            font-size: 16pt; /* Smaller header */
            margin-bottom: 10px;
            color: #2c3e50;
        }

        /* Footer in Header */
        .footer {
            margin-top: 10px;
            font-size: 8pt; /* Smaller footer text */
            color: #777;
        }

        /* Table Styling */
        table {
            width: 100%; /* Table width set to 96% of the container width */
            margin: 20px auto; /* Center table horizontally */
            border-collapse: collapse;
            font-size: 9pt; /* Reduced font size for table */
            table-layout: fixed; /* Ensures that columns are evenly distributed */
        }

        th, td {
            padding: 6px 10px; /* Adjusted padding for better fit */
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

        /* Column specific styles for email and address fields */
        td:nth-child(6), td:nth-child(9) {
            word-wrap: break-word; /* Allow the content to break and fit in the box */
            word-break: break-all;  /* Ensure that long words will break if needed */
            white-space: normal;    /* Allow wrapping for long content */
        }

        /* Success and Error Messages */
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Sales Report</h1>

        <!-- Footer in the header section -->
        <div class="footer">
            <p>Generated on {{ now()->format('F j, Y, g:i A') }}</p>
        </div>

        <!-- Success/Error Message Section -->
        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Total Price (₱)</th>
                    <th>Payment Method</th>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Customer Address</th>
                    <th>Added By</th>
                    <th>User Email</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales->sortByDesc('created_at') as $sale)
                    <tr>
                        <td>{{ $sale->product->name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>₱{{ number_format($sale->total_price, 2) }}</td>
                        <td>{{ $sale->payment_method }}</td>
                        <td>{{ $sale->customer_name }}</td>
                        <td>{{ $sale->customer_email }}</td>
                        <td>{{ $sale->customer_address }}</td>
                        <td>{{ optional($sale->user)->name ?? 'No user' }}</td>
                        <td>{{ optional($sale->user)->email ?? 'No email' }}</td>
                        <td>{{ $sale->created_at->format('F j, Y, g:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</body>
</html>
