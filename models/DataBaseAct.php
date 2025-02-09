<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/db.php';

class RestaurantDB {
    private $pdo=$GLOBALS['pdo'];
    
   
    
    public function addMenuItem($category_id, $name, $description, $price, $image = null, $available = 1) {
        $sql = "INSERT INTO menu_items (category_id, name, description, price, image, available) 
                VALUES (:category_id, :name, :description, :price, :image, :available)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':available', $available);
        
        return $stmt->execute();
    }

    public function updateMenuItem($id, $category_id, $name, $description, $price, $image = null, $available = 1) {
        $sql = "UPDATE menu_items 
                SET category_id = :category_id, name = :name, description = :description, 
                    price = :price, image = :image, available = :available 
                WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':available', $available);
        
        return $stmt->execute();
    }

    public function deleteMenuItem($id) {
        $sql = "DELETE FROM menu_items WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function addCategory($name) {
        $sql = "INSERT INTO categories (name) VALUES (:name)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        
        return $stmt->execute();
    }

    public function updateCategory($id, $name) {
        $sql = "UPDATE categories SET name = :name WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        $sql = "DELETE FROM categories WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function addOrder($user_id, $total, $payment_method, $status = 'pending') {
        $sql = "INSERT INTO orders (user_id, total, payment_method, status) 
                VALUES (:user_id, :total, :payment_method, :status)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }

    public function updateOrderStatus($id, $status) {
        $sql = "UPDATE orders SET status = :status WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $status);
        
        return $stmt->execute();
    }

    public function deleteOrder($id) {
        $sql = "DELETE FROM orders WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function getMenuItems() {
        $sql = "SELECT * FROM menu_items";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrders() {
        $sql = "SELECT * FROM orders";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReservations() {
        $sql = "SELECT * FROM reservations";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$db = new RestaurantDB('localhost', 'restaurant_db', 'root', '');

$db->addMenuItem(1, 'Pasta', 'Delicious pasta with tomato sauce', 9.99);

$db->updateMenuItem(1, 1, 'Spaghetti', 'Delicious spaghetti with tomato sauce', 10.99);

$db->deleteMenuItem(1);

$db->addOrder(1, 25.50, 'card');

$db->updateOrderStatus(1, 'preparing');

$db->deleteOrder(1);

?>

