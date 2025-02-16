<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../vendor/autoload.php';

use TCPDF;

class SalesController {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order($GLOBALS['pdo']);
    }

    // Get sales report data
    public function getSalesReport($startDate, $endDate) {
        return $this->orderModel->getSalesReport($startDate, $endDate);
    }

    // Get total revenue
    public function getTotalRevenue() {
        return $this->orderModel->getTotalRevenue();
    }

    public function exportSalesReportToCSV($startDate, $endDate) {
        $salesData = $this->getSalesReport($startDate, $endDate);
    
        // Generate CSV file
        $filename = "sales_report_" . date('Y-m-d') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
    
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Total Orders', 'Revenue']); // Header row
    
        foreach ($salesData as $row) {
            fputcsv($output, $row);
        }
    
        fclose($output);
        exit();
    }
    
    public function exportSalesReportToPDF($startDate, $endDate) {
        $salesData = $this->getSalesReport($startDate, $endDate);

        $pdf = new TCPDF();
        $pdf->SetTitle('Sales Report');
        $pdf->AddPage();
        $pdf->SetFont('helvetica', '', 12);

        // Add report title
        $pdf->Cell(0, 10, 'Sales Report (' . $startDate . ' to ' . $endDate . ')', 0, 1, 'C');

        // Add table headers
        $pdf->Cell(60, 10, 'Date', 1);
        $pdf->Cell(60, 10, 'Total Orders', 1);
        $pdf->Cell(60, 10, 'Revenue', 1);
        $pdf->Ln();

        // Add table rows
        foreach ($salesData as $row) {
            $pdf->Cell(60, 10, $row['date'], 1);
            $pdf->Cell(60, 10, $row['total_orders'], 1);
            $pdf->Cell(60, 10, '$' . number_format($row['revenue'], 2), 1);
            $pdf->Ln();
        }

        // Output PDF
        $filename = "sales_report_" . date('Y-m-d') . ".pdf";
        $pdf->Output($filename, 'D'); // Force download
        exit();
    }
}

?>
