<?php
require_once __DIR__ . '/../../controllers/OrderController.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: /views/shared/login.php');
    exit();
}

$sortOrder = isset($_GET['sort']) && $_GET['sort'] === 'asc' ? 'asc' : 'desc'; 

$orderController = new OrderController($pdo);

$orders = $orderController->getOrderHistory($_SESSION['user_id'], $sortOrder);
?>


<?php include '../layouts/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="/public/css/order-payment.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Order History</h2>

    <div class="text-center mb-3">
        <a href="?sort=asc" class="btn btn-info">Sort: Oldest First</a>
        <a href="?sort=desc" class="btn btn-info">Sort: Newest First</a>
    </div>

    <?php if (empty($orders)) : ?>
        <div class="alert alert-warning">You have no past orders.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Order Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?= $order['id'] ?></td>
                        <td>$<?= number_format($order['total_price'], 2) ?></td>
                        <td><?= ucfirst($order['status']) ?></td>
                        <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                        <td>

                    <form method="POST" action="Reorder.php" style="display: inline-block; margin-right: 10px;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="btn btn-warning btn-sm">Reorder</button>
                    </form>
    
                    <form method="POST" action="order-tracking.php" style="display: inline-block; margin-right: 10px;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="btn btn-info btn-sm">Track Order</button>
                    </form>
                    </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../layouts/footer.php'; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".reorder-btn").forEach(button => {
        button.addEventListener("click", function (event) {
            if (!confirm("Are you sure you want to reorder this item?")) {
                event.preventDefault();
            }
        });
    });
});
</script>

</body>
</html>
