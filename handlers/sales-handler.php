<?php
session_start();
require_once __DIR__ . '/../controllers/SalesController.php';

// Authorization check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    header("Location: /views/shared/login.php");
    exit();
}

$salesController = new SalesController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $startDate = $_GET['start_date'] ?? date('Y-m-01');
    $endDate = $_GET['end_date'] ?? date('Y-m-d');
    $salesData = $salesController->getSalesReport($startDate, $endDate);
    $totalRevenue = $salesController->getTotalRevenue();

    // Pass data to the view
    $_SESSION['sales_data'] = $salesData;
    $_SESSION['total_revenue'] = $totalRevenue;
    $_SESSION['start_date'] = $startDate;
    $_SESSION['end_date'] = $endDate;
}

header("Location: /views/staff/reports.php");
exit();
?>