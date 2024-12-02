@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Weekly Sales Report</h1>
    <p><strong>Week:</strong> {{ $week }} | <strong>Month:</strong> {{ \Carbon\Carbon::createFromDate($year, $month)->format('F') }} | <strong>Year:</strong> {{ $year }}</p>

    <div class="card mb-4">
        <div class="card-body">
            <h3>Total Revenue for the Week: ${{ number_format($totalRevenue, 2) }}</h3>
        </div>
    </div>

    <!-- Chart for Daily Sales -->
    <div class="card mb-4">
        <div class="card-body">
            <h4>Sales by Day</h4>
            <canvas id="weeklySalesChart"></canvas>
        </div>
    </div>

    <!-- Weekly Product Sales Summary -->
    <div class="card">
        <div class="card-body">
            <h4>Product Sales Summary</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity Sold</th>
                        <th>Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weeklyProductSales as $productName => $data)
                        <tr>
                            <td>{{ $productName }}</td>
                            <td>{{ $data['quantity_sold'] }}</td>
                            <td>${{ number_format($data['total_revenue'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('weeklySalesChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($weeklyLabels),
                datasets: [{
                    label: 'Daily Sales ($)',
                    data: @json($weeklySales),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
