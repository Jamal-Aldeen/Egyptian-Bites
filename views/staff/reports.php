<?php
session_start();
require_once __DIR__ . '/../../models/Order.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /views/shared/login.php"); // Not logged in → login page
    exit();
} elseif ($_SESSION['role'] !== 'Staff') {
    header("Location: /index.php"); // Logged in but not staff → index
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
        .card {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .list-group-item {
            transition: background-color 0.2s;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
        }
        .alert {
    margin-bottom: 1rem;
    padding: 1rem;
    border-radius: 0.5rem;
}

.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}

.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeeba;
    color: #856404;
}

.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}
.sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px 15px;
            display: block;
            color: white;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body class="bg-light">

    <div class="container mt-4">
        <?php require_once "../layouts/sidebar.php"; ?>

        <div class=" row card shadow col-md-9 ms-sm-auto col-lg-10 px-4">

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
            <div class="row g-3 mb-4">
    <div class="col-md-3">
        <a href="../../handlers/sales-handler.php?export=csv&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>"
           class="btn btn-success w-100">
            <i class="fas fa-file-csv"></i> Export as CSV
        </a>
    </div>
    <div class="col-md-3">
        <a href="../../handlers/sales-handler.php?export=pdf&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>"
           class="btn btn-danger w-100">
            <i class="fas fa-file-pdf"></i> Export as PDF
        </a>
    </div>
        </div>
    </div>
    <!-- Export Buttons -->
   
</div>
    <!-- Bootstrap 5 JS and FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
