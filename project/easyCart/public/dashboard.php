<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';

$controller = new OrderController();
$data = $controller->dashboard();

$totalOrders = $data['total_orders'];
$totalSpent = $data['total_spent'];
$avgOrderValue = $data['avg_order_value'];
$chartLabels = $data['chart_labels'];
$chartValues = $data['chart_values'];

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';
?>

<!-- Tailwind CSS Support -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">User Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">Track your orders, spending habits, and account value in one place.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            
            <!-- Total Orders Card -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 flex items-center">
                <div class="bg-blue-100 p-4 rounded-lg mr-5">
                    <i class="fas fa-shopping-bag text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Orders</p>
                    <p class="text-3xl font-bold text-gray-900"><?= number_format($totalOrders) ?></p>
                </div>
            </div>

            <!-- Total Spent Card -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 flex items-center">
                <div class="bg-green-100 p-4 rounded-lg mr-5">
                    <i class="fas fa-wallet text-green-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Spent</p>
                    <p class="text-3xl font-bold text-gray-900">$<?= number_format($totalSpent, 2) ?></p>
                </div>
            </div>

            <!-- Average Order Value Card -->
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 flex items-center">
                <div class="bg-purple-100 p-4 rounded-lg mr-5">
                    <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Avg. Order Value</p>
                    <p class="text-3xl font-bold text-gray-900">$<?= number_format($avgOrderValue, 2) ?></p>
                </div>
            </div>

        </div>

        <!-- Chart Section -->
        <div class="bg-white rounded-xl shadow-md p-8 border border-gray-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Spending Trends</h2>
                    <p class="text-sm text-gray-500">Daily transaction volume for the last 30 days</p>
                </div>
                <!-- Mini Legend/Indicator -->
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-xs font-medium text-gray-600 uppercase">Amount ($)</span>
                </div>
            </div>
            
            <div class="relative h-96">
                <canvas id="spendingChart"></canvas>
            </div>
        </div>

    </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('spendingChart').getContext('2d');
    
    // Create Gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)'); // blue-500
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

    const labels = <?= json_encode($chartLabels) ?>;
    const values = <?= json_encode($chartValues) ?>;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Order Total',
                data: values,
                borderColor: '#3b82f6', // blue-500
                borderWidth: 3,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4, // Smooth curves
                pointBackgroundColor: '#fff',
                pointBorderColor: '#3b82f6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#3b82f6',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // We use our custom header legend
                },
                tooltip: {
                    backgroundColor: '#1f2937', // gray-800
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 10,
                        color: '#9ca3af' // gray-400
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6', // gray-100
                        drawBorder: false
                    },
                    ticks: {
                        color: '#9ca3af', // gray-400
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php require '../resources/views/footer.php'; ?>
