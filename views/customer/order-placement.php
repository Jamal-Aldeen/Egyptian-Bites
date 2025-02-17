<?php
session_start();
require_once __DIR__ . '/../../controllers/OrderController.php';
require_once __DIR__ . '/../../config/db.php';

$orderController = new OrderController();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['place_order'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "You must be logged in to place an order!";
        header("Location: /views/customer/order-placement.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $item_id = $_POST['item_id'];
    $item_name = $_POST['item_name'];
    $item_price = $_POST['item_price'];
    $quantity = $_POST['quantity'];
    $customization = $_POST['customization'];

    $total_price = $item_price * $quantity;

    $items = [
        ['id' => $item_id, 'name' => $item_name, 'price' => $item_price, 'quantity' => $quantity, 'customization' => $customization]
    ];

    if ($orderController->placeOrder($user_id, $items, $total_price)) {
        $_SESSION['success'] = "Your order has been placed successfully!";
    } else {
        $_SESSION['error'] = "An error occurred while placing the order.";
    }

    header("Location: /views/customer/order-placement.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placement</title>
    <link rel="stylesheet" href="../../public/css/order-placement.css">
</head>
<body>

<?php include '../layouts/header.php'; ?>

<div class="container">
    <h2>Place an Order</h2>

    <?php
    if (isset($_SESSION['success'])) {
        echo "<p class='success'>" . $_SESSION['success'] . "</p>";
        unset($_SESSION['success']);
    }

    if (isset($_SESSION['error'])) {
        echo "<p class='error'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }

    // Fetch item details from URL
    if (isset($_GET['item_id']) && isset($_GET['name']) && isset($_GET['price'])) {
        $item_id = htmlspecialchars($_GET['item_id']);
        $item_name = htmlspecialchars($_GET['name']);
        $item_price = htmlspecialchars($_GET['price']);
    } else {
        echo "<p>No item selected.</p>";
        exit();
    }
    
    ?>

    <form method="POST" action="">
        <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
        <input type="hidden" name="item_name" value="<?php echo $item_name; ?>">
        <input type="hidden" name="item_price" value="<?php echo $item_price; ?>">

        <div class="menu-item">
            <h3><?php echo $item_name; ?> - $<?php echo $item_price; ?></h3>
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" min="1" value="1">
            
            <label for="customization">Customizations (optional):</label>
            <input type="text" name="customization" id="customization" placeholder="Extra cheese, no onions, etc.">

            <p><strong>Total Price: $<span id="total-price"><?php echo $item_price; ?></span></strong></p>

            <button type="submit" name="place_order" class="btn btn-success">Place Order</button>
        </div>
    </form>
</div>

<?php include '../layouts/footer.php'; ?>

<script>
document.getElementById("quantity").addEventListener("input", function() {
    let price = <?php echo $item_price; ?>;
    let quantity = this.value;
    document.getElementById("total-price").textContent = (price * quantity).toFixed(2);
});
</script>

</body>
</html>
