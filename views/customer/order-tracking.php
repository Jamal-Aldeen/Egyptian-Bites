<?php
session_start();
require_once __DIR__ . '/../../config/db.php';  
require_once __DIR__ . '/../../controllers/OrderController.php';

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    header("Location: /");
    exit();
}

$orderController = new OrderController($pdo);
$order = $orderController->getOrderDetails($order_id);

if (empty($order) || !isset($order['status'])) {
    echo "<div class='alert alert-danger text-center'>Order status not available.</div>";
    exit();
}

$statuses = ['Pending', 'Preparing', 'Ready', 'Delivered'];
$currentStage = array_search($order['status'], $statuses);

if ($currentStage === false) {
    echo "<div class='alert alert-danger text-center'>Invalid order status: " . htmlspecialchars($order['status']) . "</div>";
    exit();
}

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

<div class="container">
    <h2>Order Tracking</h2>
    <p>Order Number: #<?= htmlspecialchars($order['id']) ?></p>
    <p>Total Amount: $<?= number_format($order['total_price'], 2) ?></p>
    <p>Payment Status: <?= ucfirst(htmlspecialchars($order['payment_status'])) ?></p>

    <h4>Items in Your Order:</h4>
    <ul>
        <?php foreach ($order['items'] as $item): ?>
            <li><?= htmlspecialchars($item['name']) ?> (x<?= $item['quantity'] ?>)</li>
        <?php endforeach; ?>
    </ul>

    <h4>Order Progress</h4>
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

<?php include '../layouts/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
