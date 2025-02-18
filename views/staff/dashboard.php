<?php
session_start();
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Reservation.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../controllers/NotificationController.php';

// Authorization check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

// Fetch metrics
$orderModel = new Order($GLOBALS['pdo']);
$reservationModel = new Reservation();
$userModel = new User();

$totalSales = $orderModel->getTotalSales();
$activeOrders = $orderModel->getActiveOrderCount();
$upcomingReservations = $reservationModel->getUpcomingReservationCount();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Dashboard</title>
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

    <div class="container-fluid mt-4">
        <div class="row">
         <?php
         require_once "../layouts/sidebar.php";
         ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-4">
                <h1 class="text-center mb-4 text-dark">Staff Dashboard</h1>
                <!-- Metrics Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Sales</h5>
                                <p class="card-text display-6">$<?= number_format($totalSales, 2) ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Active Orders</h5>
                                <p class="card-text display-6"><?= $activeOrders ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Upcoming Reservations</h5>
                                <p class="card-text display-6"><?= $upcomingReservations ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card shadow mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-bell"></i> Notifications</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $notificationController = new NotificationController();
                        $lowStockItems = $notificationController->getLowStockNotifications();
                        $expiringOffers = $notificationController->getExpiringOfferNotifications();
                        ?>

                        <!-- Low Stock Notifications -->
                        <?php if (!empty($lowStockItems)): ?>
                            <div class="alert alert-danger">
                                <h6>Low Stock Items</h6>
                                <ul>
                                    <?php foreach ($lowStockItems as $item): ?>
                                        <li>
                                            <?= htmlspecialchars($item['item_name']) ?> 
                                            (Stock: <?= $item['quantity'] ?>, Reorder Threshold: <?= $item['reorder_threshold'] ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Expiring Special Offers -->
                        <?php if (!empty($expiringOffers)): ?>
                            <div class="alert alert-warning">
                                <h6>Expiring Special Offers</h6>
                                <ul>
                                    <?php foreach ($expiringOffers as $offer): ?>
                                        <li>
                                            <?= htmlspecialchars($offer['menu_item']) ?> 
                                            (Discount: <?= $offer['discount_value'] ?>, Ends: <?= $offer['end_date'] ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- No Notifications -->
                        <?php if (empty($lowStockItems) && empty($expiringOffers)): ?>
                            <div class="alert alert-success">
                                <h6>No new notifications.</h6>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS and FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>