<?php
session_start();
require_once __DIR__ . '/../../config/db.php';  
require_once __DIR__ . '/../../controllers/OrderController.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_id'])) {
    header("Location: /");
    exit();
}

$orderController = new OrderController($pdo);
$order = $orderController->getOrderDetails($_SESSION['order_id']);

if (!$order) {
    echo "<div class='alert alert-danger text-center'>Order not found.</div>";
    exit();
}

$statuses = ['pending', 'processing', 'shipped', 'delivered'];
$currentStage = array_search($order['status'], $statuses);
?>

<?php include '../layouts/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/order-payment.css">
    <style>
        .progress {
            height: 30px;
            font-size: 16px;
            border-radius: 15px;
        }
        .progress-bar {
            transition: width 0.6s ease;
            font-weight: bold;
        }
        .status-label {
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Order Tracking</h2>

    <div class="alert alert-info text-center">
        <p><strong>Order Number:</strong> #<?= $order['id'] ?></p>
        <p><strong>Total Amount:</strong> $<?= number_format($order['total_price'], 2) ?></p>
        <p><strong>Payment Status:</strong> <?= ucfirst($order['status']) ?></p>
    </div>

    <div class="mt-4">
        <h4 class="text-center">Order Progress</h4>
        <div class="progress">
            <?php foreach ($statuses as $index => $status): ?>
                <div class="progress-bar <?= $index <= $currentStage ? 'bg-success' : 'bg-light text-dark' ?>" 
                     style="width: <?= 100 / count($statuses) ?>%;">
                    <?= ucfirst($status) ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="status-label">
            Current Status: <strong><?= ucfirst($statuses[$currentStage]) ?></strong>
        </div>
    </div>

    <a href="/views/customer/order-history.php" class="btn btn-primary mt-4">View Order History</a>
</div>

<?php include '../layouts/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
