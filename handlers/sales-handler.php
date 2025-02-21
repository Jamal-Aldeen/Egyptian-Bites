<?php
session_start();
require_once __DIR__ . '/../controllers/SalesController.php';
require_once __DIR__ . '/../lib/vendor/autoload.php';

// Set JSON header for AJAX responses
header('Content-Type: application/json');

// Authorization check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Check if it's an AJAX request
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

try {
    $salesController = new SalesController();

    // Handle AJAX requests
    if ($isAjax) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle filter form submission
            $startDate = $_POST['start_date'] ?? date('Y-m-01');
            $endDate = $_POST['end_date'] ?? date('Y-m-d');

            // Validate dates
            if (strtotime($startDate) > strtotime($endDate)) {
                throw new Exception('End date must be after start date');
            }

            // Fetch sales data
            $salesData = $salesController->getSalesReport($startDate, $endDate);
            $totalRevenue = $salesController->getTotalRevenue();

            // Return JSON response
            echo json_encode([
                'success' => true,
                'salesData' => $salesData,
                'totalRevenue' => $totalRevenue
            ]);
            exit();
        }

        if (isset($_GET['export'])) {
            // Handle export requests
            $startDate = $_GET['start_date'] ?? date('Y-m-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-d');
            $type = $_GET['export'];

            // Validate export type
            if (!in_array($type, ['csv', 'pdf'])) {
                throw new Exception('Invalid export type');
            }

            // Generate export file
            if ($type === 'csv') {
                $salesController->exportSalesReportToCSV($startDate, $endDate);
            } elseif ($type === 'pdf') {
                $salesController->exportSalesReportToPDF($startDate, $endDate);
            }

            // No need to return anything for file downloads
            exit();
        }
    }

    // Handle non-AJAX requests (fallback)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        // Fetch sales data
        $salesData = $salesController->getSalesReport($startDate, $endDate);
        $totalRevenue = $salesController->getTotalRevenue();

        // Store data in session for non-AJAX requests
        $_SESSION['sales_data'] = $salesData;
        $_SESSION['total_revenue'] = $totalRevenue;
        $_SESSION['start_date'] = $startDate;
        $_SESSION['end_date'] = $endDate;

        // Redirect to reports page
        header("Location: /views/staff/reports.php");
        exit();
    }

    // If no valid action is detected
    throw new Exception('Invalid request');

} catch (Exception $e) {
    // Handle errors
    if ($isAjax) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    } else {
        // For non-AJAX requests, redirect with error message
        $_SESSION['error'] = $e->getMessage();
        header("Location: /views/staff/reports.php");
    }
    exit();
}