@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Form for selecting year, month, and week -->
    <form action="{{ route('sales.weeklyRedirect') }}" method="POST">
        @csrf
        <label for="year">Year:</label>
        <select name="year" id="year">
            @foreach($years as $yearOption)
                <option value="{{ $yearOption }}" {{ $yearOption == $selectedYear ? 'selected' : '' }}>{{ $yearOption }}</option>
            @endforeach
        </select>

        <label for="month">Month:</label>
        <select name="month" id="month">
            @foreach($months as $monthOption)
                <option value="{{ $monthOption }}" {{ $monthOption == $selectedMonth ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($monthOption)->format('F') }}
                </option>
            @endforeach
        </select>

        <label for="week">Week:</label>
        <select name="week" id="week">
            @foreach(range(1, 52) as $weekOption)
                <option value="{{ $weekOption }}" {{ $weekOption == $selectedWeek ? 'selected' : '' }}>
                    Week {{ $weekOption }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary mt-3">Go to Weekly Sales</button>
    </form>

    <h1 class="mt-4">Weekly Sales Report</h1>
    <p>
        <strong>Week:</strong> {{ $selectedWeek }} |
        <strong>Month:</strong> {{ \Carbon\Carbon::create()->month($selectedMonth)->format('F') }} |
        <strong>Year:</strong> {{ $selectedYear }}
    </p>

    <!-- Total Revenue -->
    <div class="card mb-4">
        <div class="card-body">
            <h3>Total Revenue for the Week: ${{ number_format($totalRevenue, 2) }}</h3>
        </div>
    </div>

    <!-- Chart for Daily Sales -->
    @if(!empty($dailySales) && count($dailySales) > 0)
    <div class="card mb-4">
        <div class="card-body">
            <h4>Sales by Day</h4>
            <canvas id="weeklySalesChart"></canvas>
        </div>
    </div>
    @else
    <div class="alert alert-warning">No sales data available for the selected week.</div>
    @endif

    <!-- Weekly Product Sales Summary -->
    @if(!empty($weeklyProductSales) && count($weeklyProductSales) > 0)
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
                    @foreach($weeklyProductSales as $data)
                        <tr>
                            <td>{{ $data['product_name'] }}</td>
                            <td>{{ $data['quantity_sold'] }}</td>
                            <td>${{ number_format($data['total_revenue'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="alert alert-warning">No product sales data available for the selected week.</div>
    @endif
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('weeklySalesChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($weeklyLabels),  // Days of the week (e.g., Sunday, Monday, etc.)
                datasets: [{
                    label: 'Daily Sales ($)',
                    data: @json($dailySales),  // Sales data for each day of the week
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toFixed(2);  // Format Y-axis with $ symbol
                            }
                        }
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return '$' + tooltipItem.raw.toFixed(2);  // Format tooltip with $ symbol
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
