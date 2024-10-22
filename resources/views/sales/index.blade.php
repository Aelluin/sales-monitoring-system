<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Sales</title>
</head>
<body>
    <h1>All Sales</h1>

    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Sale Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sales as $sale)
                <tr>
                    <td>{{ $sale->product->name }}</td>
                    <td>{{ $sale->quantity }}</td>
                    <td>₱{{ number_format($sale->total_price, 2) }}</td>
                    <td>{{ $sale->sale_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
