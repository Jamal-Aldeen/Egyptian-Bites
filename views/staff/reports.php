<?php
session_start();
require_once __DIR__ . '/../../models/Order.php';

// Authorization check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

$salesData = $_SESSION['sales_data'] ?? [];
$totalRevenue = $_SESSION['total_revenue'] ?? 0;
$startDate = $_SESSION['start_date'] ?? date('Y-m-01');
$endDate = $_SESSION['end_date'] ?? date('Y-m-d');

// Clear session data after use
unset($_SESSION['sales_data']);
unset($_SESSION['total_revenue']);
unset($_SESSION['start_date']);
unset($_SESSION['end_date']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sales Reports</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-light">
    <?php include '../layouts/header.php'; ?>

    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h3 class="mb-0">Sales Reports</h3>
            </div>
            <div class="card-body">
                <!-- Date Range Filter -->
                <form method="GET" action="../../handlers/sales-handler.php" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
                        </div>
                        <div class="col-md-4">
                            <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Total Revenue -->
                <div class="alert alert-success">
                    <h5>Total Revenue: $<?= number_format($totalRevenue, 2) ?></h5>
                </div>

                <!-- Sales Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salesData as $data): ?>
                            <tr>
                                <td><?= $data['date'] ?></td>
                                <td><?= $data['total_orders'] ?></td>
                                <td>$<?= number_format($data['revenue'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
