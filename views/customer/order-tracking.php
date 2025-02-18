<?php
session_start();
require_once __DIR__ . '/../../controllers/OrderController.php'; 
require_once __DIR__ . '/../../controllers/PaymentController.php'; 

if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_id'])) {
    header("Location: /");
    exit();
}

$orderController = new OrderController();
$paymentController = new PaymentController();

$orderDetails = $orderController->getOrderHistory($_SESSION['user_id']);
$order = null;
foreach ($orderDetails as $o) {
    if ($o['id'] == $_SESSION['order_id']) {
        $order = $o;
        break;
    }
}

$paymentStatus = 'Pending'; 
if ($order) {
    $paymentStatus = $order['status']; 
}

$orderStages = [
    'pending' => ' pending',
    'completed' => ' Delivered '
];

$currentStage = $order ? $order['status'] : 'pending';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-btn {
            width: 100%;
            padding: 10px;
            margin: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .status-btn.active {
            background-color: #28a745;
            color: white;
        }
        .status-btn.inactive {
            background-color: #ccc;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Order Status</h2>
    
    <?php if ($order): ?>
        <div class="alert alert-success">
            <p><strong>Thank you for your order!</strong></p>
            <p><strong>Order Number:</strong> #<?= $order['id'] ?></p>
            <p><strong>Total Amount:</strong> $<?= number_format($order['total_price'], 2) ?></p>
            <p><strong>Payment Status:</strong> <?= $paymentStatus == 'Paid' ? 'Paid' : 'Pending' ?></p>
            <p><strong>Payment Method:</strong> <?= $paymentStatus == 'Paid' ? 'Credit/Debit Card' : 'Cash on Delivery' ?></p>
        </div>

        <div class="mt-4">
            <h4>Tracking Information</h4>
            <ul class="list-group">
                <?php foreach ($orderStages as $stageKey => $stageName): ?>
                    <li class="list-group-item <?= $stageKey == $currentStage ? 'active' : '' ?>">
                        <strong><?= $stageName ?></strong>
                        <?php if ($stageKey == 'Placed' && $currentStage == 'Placed'): ?>
                            <span class="badge bg-secondary float-end">In Progress</span>
                        <?php elseif ($stageKey == 'Shipped' && $currentStage == 'Shipped'): ?>
                            <span class="badge bg-info float-end">In Progress</span>
                        <?php elseif ($stageKey == 'Out for Delivery' && $currentStage == 'Out for Delivery'): ?>
                            <span class="badge bg-warning float-end">In Progress</span>
                        <?php elseif ($stageKey == 'Delivered' && $currentStage == 'Delivered'): ?>
                            <span class="badge bg-success float-end">Completed</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <a href="/views/customer/order-history.php" class="btn btn-primary mt-3">View Order History</a>
    <?php else: ?>
        <div class="alert alert-danger">
            <p>Sorry, we could not find the order details. Please try again later.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
