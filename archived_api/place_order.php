session_start();
require_once __DIR__ . '/../../controllers/OrderController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $items = json_decode($_POST['cart'], true);
    $total_price = array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $items));

    $orderController = new OrderController();
    $response = $orderController->placeOrder($user_id, $items, $total_price);

    echo json_encode($response);
}
