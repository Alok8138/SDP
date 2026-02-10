<?php
require_once '../app/config/database.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/models/Product.php';

$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

$orderController = new OrderController();
$productController = new ProductController();

$importSummary = null;
if ($isAdmin) {
    // Admin Stats
    $adminStats = Order::getAdminDashboardStats();
    $totalOrders = $adminStats['total_orders'];
    $totalRevenue = $adminStats['total_spent'];
    $totalProducts = Product::getCount();

    // Handle Actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'export') {
            $productController->exportCSV();
        } elseif (isset($_FILES['product_csv'])) {
            $importSummary = $productController->importCSV();
        }
    }
    
    $totalSpent = $totalRevenue;
    $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
    
    $chartLabels = [];
    $chartValues = [];
} else {
    $data = $orderController->dashboard();
    $totalOrders = $data['total_orders'];
    $totalSpent = $data['total_spent'];
    $avgOrderValue = $data['avg_order_value'];
    $chartLabels = $data['chart_labels'];
    $chartValues = $data['chart_values'];
}

require_once '../app/helpers/functions.php';
require_once '../resources/views/header.php';
?>

<!-- Font Awesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main class="dashboard-main">
    <div class="dashboard-container">
        
        <!-- Header Section -->
        <div class="dashboard-header">
            <h1 class="dashboard-title"><?= $isAdmin ? 'Admin Dashboard' : 'User Dashboard' ?></h1>
            <p class="dashboard-subtitle"><?= $isAdmin ? 'Manage your products, view site-wide sales and performance.' : 'Track your orders, spending habits, and account value in one place.' ?></p>
        </div>

        <?php if ($importSummary): ?>
        <div class="import-summary" style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h4 style="color: #166534; margin-top: 0;">Import Summary:</h4>
            <p style="margin: 5px 0;">Total Rows: <?= $importSummary['total'] ?> | Inserted: <?= $importSummary['inserted'] ?> | Skipped: <?= $importSummary['skipped'] ?></p>
        </div>
        <?php endif; ?>

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

            <!-- Total Spent / Revenue Card -->
            <div class="stat-card">
                <div class="icon-container icon-green">
                    <i class="fas fa-wallet stat-icon text-green"></i>
                </div>
                <div>
                    <p class="stat-label"><?= $isAdmin ? 'Total Revenue' : 'Total Spent' ?></p>
                    <p class="stat-value">$<?= number_format($totalSpent, 2) ?></p>
                </div>
            </div>

            <!-- Average Order Value / Total Products Card -->
            <div class="stat-card">
                <div class="icon-container icon-purple">
                    <i class="fas <?= $isAdmin ? 'fa-box' : 'fa-chart-line' ?> stat-icon text-purple"></i>
                </div>
                <div>
                    <p class="stat-label"><?= $isAdmin ? 'Total Products' : 'Avg. Order Value' ?></p>
                    <p class="stat-value"><?= $isAdmin ? number_format($totalProducts) : '$' . number_format($avgOrderValue, 2) ?></p>
                </div>
            </div>

        </div>

        <?php if ($isAdmin): ?>
        <!-- Admin Actions Section -->
        <div class="chart-container" style="margin-top: 30px;">
            <div class="chart-header">
                <h2 class="chart-title">Product Management</h2>
                <p class="chart-subtitle">Bulk import or export your product catalog via CSV</p>
            </div>
            
            <div style="display: flex; gap: 20px; padding: 20px;">
                <!-- Import Form -->
                <div style="flex: 1; border: 1px dashed #ddd; padding: 20px; border-radius: 8px;">
                    <h4>Import Products</h4>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="file" name="product_csv" accept=".csv" required style="margin-bottom: 15px;">
                        <button type="submit" class="btn-import" style="display: block; width: 100%; padding: 10px; background: #333; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
                            <i class="fas fa-file-import" style="margin-right: 8px;"></i> Import Products (CSV)
                        </button>
                    </form>
                </div>

                <!-- Export Form -->
                <div style="flex: 1; border: 1px dashed #ddd; padding: 20px; border-radius: 8px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h4>Export Products</h4>
                        <p style="font-size: 0.9em; color: #666;">Download your entire product catalog as a CSV file compatible with the import format.</p>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="action" value="export">
                        <button type="submit" class="btn-export" style="display: block; width: 100%; padding: 10px; background: #059669; color: #fff; border: none; border-radius: 5px; cursor: pointer;">
                            <i class="fas fa-file-export" style="margin-right: 8px;"></i> Export Products (CSV)
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!$isAdmin): ?>
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
        <?php endif; ?>

    </div>
</main>

<?php if (!$isAdmin): ?>
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
<?php endif; ?>

<?php require '../resources/views/footer.php'; ?>
