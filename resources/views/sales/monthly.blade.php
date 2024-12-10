<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Sales Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Monthly Sales Report</h1>

    <!-- Simple Month and Year Selector Form -->
    <form method="GET" action="{{ route('sales.monthly') }}">
        <label for="year">Select Year:</label>
        <select name="year" id="year">
            @foreach ($years as $yearOption)
                <option value="{{ $yearOption }}" {{ $yearOption == $year ? 'selected' : '' }}>{{ $yearOption }}</option>
            @endforeach
        </select>

        <label for="month">Select Month:</label>
        <select name="month" id="month">
            <option value="01" {{ $month == '01' ? 'selected' : '' }}>January</option>
            <option value="02" {{ $month == '02' ? 'selected' : '' }}>February</option>
            <option value="03" {{ $month == '03' ? 'selected' : '' }}>March</option>
            <option value="04" {{ $month == '04' ? 'selected' : '' }}>April</option>
            <option value="05" {{ $month == '05' ? 'selected' : '' }}>May</option>
            <option value="06" {{ $month == '06' ? 'selected' : '' }}>June</option>
            <option value="07" {{ $month == '07' ? 'selected' : '' }}>July</option>
            <option value="08" {{ $month == '08' ? 'selected' : '' }}>August</option>
            <option value="09" {{ $month == '09' ? 'selected' : '' }}>September</option>
            <option value="10" {{ $month == '10' ? 'selected' : '' }}>October</option>
            <option value="11" {{ $month == '11' ? 'selected' : '' }}>November</option>
            <option value="12" {{ $month == '12' ? 'selected' : '' }}>December</option>
        </select>

        <button type="submit">Get Report</button>
    </form>

    <h2>Sales Report for: {{ $monthName }}</h2>

    @if($monthlySales->isEmpty())
        <p>No sales for the selected month.</p>
    @else
        <table border="1">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sale ID</th>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Total Price</th>
                    <th>Payment Method</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlySales as $sale)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->product->name }}</td>
                        <td>{{ $sale->quantity }}</td>
                        <td>${{ number_format($sale->total_price, 2) }}</td>
                        <td>{{ $sale->payment_method }}</td>
                        <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary of sales -->
        <footer>
            <p><strong>Total Sales:</strong> ${{ number_format($totalSales, 2) }}</p>
            <p><strong>Total Quantity Sold:</strong> {{ $totalQuantity }}</p>
        </footer>
    @endif

    <!-- Chart.js Bar Chart -->
    <div style="width: 80%; margin: 0 auto;">
        <canvas id="productSalesChart"></canvas>
    </div>

    <script>
        // Data for the chart
        var productNames = @json($productNames);
        var quantities = @json($quantities);

        // Create the chart
        var ctx = document.getElementById('productSalesChart').getContext('2d');
        var productSalesChart = new Chart(ctx, {
            type: 'bar', // Bar chart
            data: {
                labels: productNames, // Product names as labels
                datasets: [{
                    label: 'Products Sold',
                    data: quantities, // Quantity sold for each product
                    backgroundColor: 'rgba(54, 162, 235, 0.2)', // Bar color
                    borderColor: 'rgba(54, 162, 235, 1)', // Bar border color
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
