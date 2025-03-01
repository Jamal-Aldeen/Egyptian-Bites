<?php
require_once __DIR__ . '/../config/db.php';

class Order {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function createOrder($user_id, $total_price, $items) {
        try {

            $this->pdo->beginTransaction();

            $query = "INSERT INTO Orders (user_id, total_price, status, payment_status, created_at)
                      VALUES (:user_id, :total_price, 'Pending', 'Pending', NOW())";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':total_price', $total_price);
            $stmt->execute();
            $order_id = $this->pdo->lastInsertId(); 

            foreach ($items as $item) {
                $query = "INSERT INTO OrderItems (order_id, menu_item_id, quantity)
                          VALUES (:order_id, :menu_item_id, :quantity)";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':order_id', $order_id);
                $stmt->bindParam(':menu_item_id', $item['id']);
                $stmt->bindParam(':quantity', $item['quantity']);
                $stmt->execute();
            }

            $this->pdo->commit();
            return $order_id;

        } catch (Exception $e) {

            $this->pdo->rollBack();
            error_log("Order creation failed: " . $e->getMessage());
            throw new Exception("Order creation failed: " . $e->getMessage());
        }
    }
    



    public function updateOrderStatusAndPaymentStatus($order_id, $status, $payment_status) {
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
    
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function addOrderItems($order_id, $items) {
        $stmt = $this->pdo->prepare("INSERT INTO OrderItems (order_id, menu_item_id, quantity) VALUES (?, ?, ?)");
        
        foreach ($items as $item) {
            if (isset($item['id'], $item['quantity'])) {
                $stmt->execute([$order_id, $item['id'], $item['quantity']]);
            }
        }
    }


    public function getOrderHistory($user_id, $sortOrder = 'DESC') {
        $sortOrder = ($sortOrder == 'ASC' || $sortOrder == 'DESC') ? $sortOrder : 'DESC'; 

        $stmt = $this->pdo->prepare("SELECT * FROM Orders WHERE user_id = ? ORDER BY created_at $sortOrder");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public function updateOrderStatus($order_id, $status, $payment_status) {
        $query = "UPDATE Orders SET status = :status, payment_status = :payment_status WHERE id = :order_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':payment_status', $payment_status);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
    }
    
    
    


    public function getAllOrders($limit, $offset) {
        $query = "SELECT o.*, u.name as customer_name 
                  FROM Orders o
                  LEFT JOIN users u ON o.user_id = u.id
                  ORDER BY o.created_at DESC
                  LIMIT :limit OFFSET :offset";
    
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt;
    }


    public function getTotalSales() {
        $stmt = $this->pdo->prepare("SELECT SUM(total_price) as total_sales FROM Orders");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_sales'] ? $result['total_sales'] : 0;
    }


    public function getActiveOrderCount() {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as active_orders FROM Orders WHERE status = 'Pending'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['active_orders'] ? $result['active_orders'] : 0;
    }


    public function getSalesReport($startDate, $endDate) {
        $stmt = $this->pdo->prepare("
            SELECT DATE(created_at) AS date, COUNT(*) AS total_orders, SUM(total_price) AS revenue 
            FROM Orders 
            WHERE created_at BETWEEN ? AND ?
            GROUP BY DATE(created_at)
        ");
        $stmt->execute([$startDate, $endDate]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getTotalRevenue() {
        $stmt = $this->pdo->query("SELECT SUM(total_price) AS total FROM Orders WHERE status = 'Delivered'");
        return $stmt->fetchColumn() ?? 0;
    }

    public function addMenuItem($category_id, $name, $description, $price, $image, $availability) {
        $stmt = $this->pdo->prepare("INSERT INTO MenuItems (category_id, name, description, price, image, availability) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$category_id, $name, $description, $price, $image, $availability]);
    }


       public function getByCustomer($user_id) {  
        $query = "SELECT * FROM Orders WHERE user_id = :user_id";  
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);  
        $stmt->execute();

        return $stmt;  
    }

    public function create() {
        $query = "INSERT INTO Orders (user_id, total_price, status, created_at) 
                  VALUES (:user_id, :total_price, 'Pending', NOW())";
    
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $this->customer_id, PDO::PARAM_INT);
        $stmt->bindParam(':total_price', $this->total, PDO::PARAM_STR);
    
        if ($stmt->execute()) {

            $order_id = $this->pdo->lastInsertId();
    

            foreach ($this->items as $item) {
                $itemQuery = "INSERT INTO OrderItems (order_id, menu_item_id, quantity, customizations) 
                              VALUES (:order_id, :menu_item_id, :quantity, :customizations)";
                $itemStmt = $this->pdo->prepare($itemQuery);
                $itemStmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                $itemStmt->bindParam(':menu_item_id', $item['id'], PDO::PARAM_INT);
                $itemStmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                $itemStmt->bindParam(':customizations', $item['customizations'], PDO::PARAM_STR);
                $itemStmt->execute();
            }
    
            return true;
        }
    
        return false;
    }

    public function getByOrderId($order_id) {
    $query = "SELECT o.id, o.total_price, o.status, o.created_at, oi.menu_item_id, oi.quantity, oi.customizations,
                     m.name AS product_name, m.price, m.category_id
              FROM Orders o
              JOIN OrderItems oi ON o.id = oi.order_id
              JOIN MenuItems m ON oi.menu_item_id = m.id
              WHERE o.id = :order_id";

    $stmt = $this->pdo->prepare($query);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt;  
}

}
?>
