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

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main class="dashboard-main">
    <div class="dashboard-container">
        
        <!-- Header Section -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">User Dashboard</h1>
            <p class="dashboard-subtitle">Track your orders, spending habits, and account value in one place.</p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            
            <!-- Total Orders Card -->
            <div class="stat-card">
                <div class="icon-container icon-blue">
                    <i class="fas fa-shopping-bag stat-icon text-blue"></i>
                </div>
                <div>
                    <p class="stat-label">Total Orders</p>
                    <p class="stat-value"><?= number_format($totalOrders) ?></p>
                </div>
            </div>

            <!-- Total Spent Card -->
            <div class="stat-card">
                <div class="icon-container icon-green">
                    <i class="fas fa-wallet stat-icon text-green"></i>
                </div>
                <div>
                    <p class="stat-label">Total Spent</p>
                    <p class="stat-value">$<?= number_format($totalSpent, 2) ?></p>
                </div>
            </div>

            <!-- Average Order Value Card -->
            <div class="stat-card">
                <div class="icon-container icon-purple">
                    <i class="fas fa-chart-line stat-icon text-purple"></i>
                </div>
                <div>
                    <p class="stat-label">Avg. Order Value</p>
                    <p class="stat-value">$<?= number_format($avgOrderValue, 2) ?></p>
                </div>
            </div>

        </div>

        <!-- Chart Section -->
        <div class="chart-container">
            <div class="chart-header">
                <div>
                    <h2 class="chart-title">Spending Trends</h2>
                    <p class="chart-subtitle">Daily transaction volume for the last 30 days</p>
                </div>
                <!-- Mini Legend/Indicator -->
                <div class="chart-legend">
                    <span class="legend-dot"></span>
                    <span class="legend-text">Amount ($)</span>
                </div>
            </div>
            
            <div class="canvas-wrapper">
                <canvas id="spendingChart"></canvas>
            </div>
        </div>

    </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Pass PHP Data to JS via Global Variable -->
<script>
    window.dashboardData = {
        labels: <?= json_encode($chartLabels) ?>,
        values: <?= json_encode($chartValues) ?>
    };
</script>

<!-- Custom Dashboard JS -->
<script src="<?= BASE_URL ?>/assets/js/dashboard.js"></script>

<?php require '../resources/views/footer.php'; ?>
