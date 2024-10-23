<h1>Sales Report</h1>

<h2>Total Revenue: ₱{{ number_format($totalRevenue, 2) }}</h2>

<h3>Best-Selling Products</h3>
<ul>
    @foreach($bestSellingProducts as $product)
        <li>{{ $product->product->name }} - {{ $product->total_quantity }} sold</li>
    @endforeach
</ul>

<h3>All Sales</h3>
<table border="1">
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
            <td>{{ $sale->created_at }}</td>
        </tr>
    @endforeach
</table>
