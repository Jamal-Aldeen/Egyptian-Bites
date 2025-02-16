<?php
session_start();
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Reservation.php';
require_once __DIR__ . '/../../models/User.php';

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
    </style>
</head>
<body class="bg-light">
    <?php include '../layouts/header.php'; ?>

    <div class="container mt-4">
        <h1 class="text-center mb-4">Staff Dashboard</h1>

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

        <!-- Admin Tools Navigation -->
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Admin Tools</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="user-management.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                    <a href="menu-management.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-utensils me-2"></i>Manage Menu
                    </a>
                    <a href="inventory-management.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-boxes me-2"></i>Manage Inventory
                    </a>
                    <a href="reports.php" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-bar me-2"></i>Sales Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>