<?php
session_start(); 
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../config/db.php';

class OrderController {
    private $pdo;
    private $orderModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->orderModel = new Order($pdo);
    }

    public function getOrderHistory($user_id, $sortOrder = 'desc') {
        global $pdo;
        $validSortOrder = ($sortOrder === 'asc') ? 'ASC' : 'DESC';
    
        $query = "SELECT id, total_price, status, created_at 
                  FROM Orders 
                  WHERE user_id = :user_id 
                  ORDER BY created_at $validSortOrder";
    
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    public function placeOrder($user_id, $items, $total_price) {
        try {

            foreach ($items as $item) {
                $checkStmt = $this->pdo->prepare("SELECT id FROM MenuItems WHERE id = ?");
                $checkStmt->execute([$item['id']]);
                if (!$checkStmt->fetch()) {
                    throw new Exception("Item not exist: #" . $item['id']);
                }
            }

            $order_id = $this->orderModel->createOrder($user_id, $total_price, $items);

            $_SESSION['order_id'] = $order_id;
            $_SESSION['amount'] = $total_price;

            header("Location: /views/customer/payment.php");
            exit();
        } catch (Exception $e) {

            error_log("Order Error: " . $e->getMessage()); 
            $_SESSION['error'] = "Failed to place order: " . $e->getMessage();
            header("Location: /views/customer/cart.php");
            exit();
        }
    }

    public function updateOrderStatus($order_id, $status, $payment_status) {
        try {
            $validStatuses = ['Pending', 'Preparing', 'Ready', 'Delivered'];
            $validPaymentStatuses = ['Pending', 'Completed', 'Failed'];

            if (!in_array($status, $validStatuses)) {
                throw new Exception("Invalid order status: " . $status);
            }

            if (!in_array($payment_status, $validPaymentStatuses)) {
                throw new Exception("Invalid payment status: " . $payment_status);
            }

            $query = "UPDATE Orders SET status = :status, payment_status = :payment_status WHERE id = :order_id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':payment_status', $payment_status);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();

        } catch (Exception $e) {

            error_log("Update Order Status Error: " . $e->getMessage()); 
            return ['status' => 'error', 'message' => 'Failed to update order status: ' . $e->getMessage()];
        }
    }

    public function getOrderDetails($order_id) {
        try {
            $query = "SELECT 
                        o.id, 
                        o.user_id, 
                        o.total_price, 
                        o.status, 
                        o.payment_status, 
                        o.created_at, 
                        o.updated_at,
                        JSON_ARRAYAGG(
                            JSON_OBJECT(
                                'menu_item_id', oi.menu_item_id,
                                'quantity', oi.quantity,
                                'name', m.name
                            )
                        ) AS items
                      FROM Orders o
                      JOIN OrderItems oi ON o.id = oi.order_id 
                      JOIN MenuItems m ON oi.menu_item_id = m.id 
                      WHERE o.id = ?
                      GROUP BY o.id";
                      
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$order_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$result) {
                error_log("No order details found for order ID: $order_id");
                return [];
            }
    
            $result['items'] = json_decode($result['items'], true);
    
            return $result;
    
        } catch (Exception $e) {
            error_log("Get Order Details Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to retrieve order details'];
        }
    }
    
    

    public function reorder($order_id) {
        try {

            $orderDetails = $this->getOrderDetails($order_id);

            if (empty($orderDetails)) {
                echo "Invalid order items.";
                exit();
            }

            $_SESSION['cart'] = [];
            foreach ($orderDetails as $item) {
                $_SESSION['cart'][] = [
                    'id' => $item['menu_item_id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity']
                ];
            }
        } catch (Exception $e) {
            error_log("Reorder Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to reorder: ' . $e->getMessage()];
        }
    }
}