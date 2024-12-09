<script>
    // Pass the monthly data to JavaScript from the PHP variable
const monthlyData = @json($monthlyData);

// Function to update the chart based on the selected year
function updateChart(selectedYear) {
const salesData = monthlyData[selectedYear] || {};
const chartData = [
salesData[1] || 0,
salesData[2] || 0,
salesData[3] || 0,
salesData[4] || 0,
salesData[5] || 0,
salesData[6] || 0,
salesData[7] || 0,
salesData[8] || 0,
salesData[9] || 0,
salesData[10] || 0,
salesData[11] || 0,
salesData[12] || 0
];

monthlySalesChart.data.datasets[0].data = chartData;
monthlySalesChart.update();
}

const ctx = document.getElementById('monthlySalesChart').getContext('2d');
const monthlySalesChart = new Chart(ctx, {
type: 'line',
data: {
labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
datasets: [{
    label: '',
    data: [
        monthlyData[2024][1] || 0,
        monthlyData[2024][2] || 0,
        monthlyData[2024][3] || 0,
        monthlyData[2024][4] || 0,
        monthlyData[2024][5] || 0,
        monthlyData[2024][6] || 0,
        monthlyData[2024][7] || 0,
        monthlyData[2024][8] || 0,
        monthlyData[2024][9] || 0,
        monthlyData[2024][10] || 0,
        monthlyData[2024][11] || 0,
        monthlyData[2024][12] || 0
    ],
    borderColor: 'rgba(75, 192, 192, 1)',
    backgroundColor: 'rgba(75, 192, 192, 0.2)',
    borderWidth: 2,
    fill: true,
    pointRadius: 5,
    pointHoverRadius: 7,
}]
},
options: {
responsive: true,
plugins: {
    legend: {
        display: false
    },
    tooltip: {
        callbacks: {
            label: function(tooltipItem) {
                return '₱' + tooltipItem.raw.toLocaleString();
            }
        }
    }
},
scales: {
    y: {
        beginAtZero: true,
        title: {
            display: true,
            text: 'Sales Amount'
        },
        ticks: {
            callback: function(value) {

                return '₱' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); // commas
            }
        }
    },
    x: {
        title: {
            display: true,
            text: 'Months'
        }
    }
}
}
});

// Year selector
document.getElementById('yearSelector').addEventListener('change', function() {
updateChart(this.value);
});


</script>
