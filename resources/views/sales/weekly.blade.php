<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Sales Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Weekly Sales Report</h1>

    <!-- Year and Week Selector Form -->
    <form method="GET" action="{{ route('sales.weekly') }}">
        <fieldset style="border: 2px solid #ddd; padding: 20px; margin-bottom: 20px;">
            <legend>Select Year and Week</legend>

            <!-- Year Selector -->
            <label for="year">Select a Year:</label>
            <select name="year" id="year" style="padding: 5px; margin-bottom: 15px; width: 200px;">
                <option value="">-- Choose a Year --</option>
                @foreach ($years as $yearOption)
                    <option value="{{ $yearOption }}" {{ $yearOption == $year ? 'selected' : '' }}>{{ $yearOption }}</option>
                @endforeach
            </select>

            <br>

            <!-- Week Selector -->
            <label for="week">Select a Week:</label>
            <select name="week" id="week" style="padding: 5px; margin-bottom: 15px; width: 200px;">
                <option value="">-- Choose a Week --</option>
                @foreach ($weeksInYear as $index => $week)
                    <option value="{{ $index + 1 }}" {{ $index + 1 == $week ? 'selected' : '' }}>
                        Week {{ $index + 1 }} ({{ \Carbon\Carbon::parse($week['start'])->format('M d') }} - {{ \Carbon\Carbon::parse($week['end'])->format('M d') }})
                    </option>
                @endforeach
            </select>

            <br>

            <button type="submit" style="padding: 8px 15px; background-color: #007bff; color: white; border: none; border-radius: 4px;">
                Get Report
            </button>
        </fieldset>
    </form>

    <!-- Sales Report Header -->
@php
// Ensure $week is an integer and validate the selection
$week = isset($week) ? (int) $week : null; // Convert the week value to an integer if set

// Determine header text based on week selection
if ($week && isset($weeksInYear[$week - 1])) {
    // Retrieve corresponding week start and end dates
    $selectedWeek = $weeksInYear[$week - 1];
    $startDate = \Carbon\Carbon::parse($selectedWeek['start'])->format('M d');
    $endDate = \Carbon\Carbon::parse($selectedWeek['end'])->format('M d');
    $headerText = "Sales Report for Week $week ($startDate - $endDate)";
} else {
    // Display a prompt message when no week is selected or invalid
    $headerText = "Please select a year and week to view the sales report.";
}
@endphp

<h2>{{ $headerText }}</h2>

@if(empty($week) || empty($weeklySales))
<p>No sales data available. Please select a year and week.</p>
@else
<!-- Sales Data Table -->
<table border="1" style="border-collapse: collapse; width: 100%; margin-top: 20px;">
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
        @foreach($weeklySales as $sale)
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

<!-- Sales Summary -->
<footer style="margin-top: 20px;">
    <p><strong>Total Sales:</strong> ${{ number_format($totalSales, 2) }}</p>
    <p><strong>Total Quantity Sold:</strong> {{ $totalQuantity }}</p>
</footer>
@endif

    <!-- Chart.js Bar Chart -->
    <div style="width: 80%; margin: 0 auto;">
        <canvas id="productSalesChart"></canvas>
    </div>

    <script>
        // Data for the chart (ensure JSON format is correct)
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
