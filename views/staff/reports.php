<?php
session_start();
require_once __DIR__ . '/../../models/Order.php';

// Authorization check
if (!isset($_SESSION['user_id'])) {
    header("Location: /views/shared/login.php"); // Not logged in → login page
    exit();
} elseif ($_SESSION['role'] !== 'Staff') {
    header("Location: /index.php"); // Logged in but not staff → index
    exit();
}

// Initialize variables
$salesData = [];
$totalRevenue = 0;
$startDate = date('Y-m-01');
$endDate = date('Y-m-d');
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

        <div class="row card shadow col-md-9 ms-sm-auto col-lg-10 px-4">
            <div class="card-header bg-dark text-white">
                <h3 class="mb-0">Sales Reports</h3>
            </div>
            <div class="card-body">
                <!-- Date Range Filter -->
                <form id="report-filter-form" class="mb-4">
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
                    <h5>Total Revenue: $<span id="total-revenue"><?= number_format($totalRevenue, 2) ?></span></h5>
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
                        <tbody id="sales-table-body">
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

                <!-- Export Buttons -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <button class="btn btn-success w-100 export-btn" data-export-type="csv">
                            <i class="fas fa-file-csv"></i> Export as CSV
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-danger w-100 export-btn" data-export-type="pdf">
                            <i class="fas fa-file-pdf"></i> Export as PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('report-filter-form');
        const salesTableBody = document.getElementById('sales-table-body');
        const totalRevenueElement = document.getElementById('total-revenue');

        // Handle filter form submission
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('../../handlers/sales-handler.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateSalesTable(data.salesData);
                    updateRevenue(data.totalRevenue);
                } else {
                    showError(data.error || 'Failed to fetch data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('An error occurred while fetching data');
            });
        });

        // Handle export buttons
        document.querySelectorAll('.export-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const type = this.dataset.exportType;
                const startDate = filterForm.querySelector('[name="start_date"]').value;
                const endDate = filterForm.querySelector('[name="end_date"]').value;

                fetch(`../../handlers/sales-handler.php?export=${type}&start_date=${startDate}&end_date=${endDate}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `sales_report_${new Date().toISOString()}.${type}`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.error('Export error:', error);
                    showError('Failed to generate export');
                });
            });
        });

        // Update sales table
        function updateSalesTable(data) {
            salesTableBody.innerHTML = '';
            data.forEach(row => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${row.date}</td>
                    <td>${row.total_orders}</td>
                    <td>$${parseFloat(row.revenue).toFixed(2)}</td>
                `;
                salesTableBody.appendChild(tr);
            });
        }

        // Update total revenue
        function updateRevenue(amount) {
            totalRevenueElement.textContent = parseFloat(amount).toFixed(2);
        }

        // Show error message
        function showError(message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger mt-3';
            alertDiv.textContent = message;
            document.querySelector('.card-body').prepend(alertDiv);
            setTimeout(() => alertDiv.remove(), 5000);
        }
    });
    </script>
</body>
</html>