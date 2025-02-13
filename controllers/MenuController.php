<?php
require_once __DIR__ . '/../config/db.php';

class MenuController {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Categories Management
    public function addCategory($name) {
        $stmt = $this->pdo->prepare("INSERT INTO MenuCategories (name) VALUES (:name)");
        return $stmt->execute(['name' => $name]);
    }

    public function updateCategory($id, $name) {
        $stmt = $this->pdo->prepare("UPDATE MenuCategories SET name = :name WHERE id = :id");
        return $stmt->execute(['id' => $id, 'name' => $name]);
    }

    public function deleteCategory($id) {
        $stmt = $this->pdo->prepare("DELETE FROM MenuCategories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getCategories() {
        $stmt = $this->pdo->query("SELECT * FROM MenuCategories");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menu Items Management
    public function addMenuItem($category_id, $name, $description, $price, $image, $availability) {
        $stmt = $this->pdo->prepare("INSERT INTO MenuItems (category_id, name, description, price, image, availability) 
                                     VALUES (:category_id, :name, :description, :price, :image, :availability)");
        return $stmt->execute([
            'category_id' => $category_id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image' => $image,
            'availability' => $availability
        ]);
    }

    public function updateMenuItem($id, $category_id, $name, $description, $price, $image, $availability) {
        $stmt = $this->pdo->prepare("UPDATE MenuItems SET category_id = :category_id, name = :name, description = :description, 
                                    price = :price, image = :image, availability = :availability WHERE id = :id");
        return $stmt->execute([
            'id' => $id,
            'category_id' => $category_id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image' => $image,
            'availability' => $availability
        ]);
    }

    public function deleteMenuItem($id) {
        $stmt = $this->pdo->prepare("DELETE FROM MenuItems WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getMenuItems() {
        $stmt = $this->pdo->query("SELECT * FROM MenuItems");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Special Offers Management
    public function addSpecialOffer($menu_item_id, $discount_type, $discount_value, $start_date, $end_date) {
        $stmt = $this->pdo->prepare("INSERT INTO SpecialOffers (menu_item_id, discount_type, discount_value, start_date, end_date) 
                                     VALUES (:menu_item_id, :discount_type, :discount_value, :start_date, :end_date)");
        return $stmt->execute([
            'menu_item_id' => $menu_item_id,
            'discount_type' => $discount_type,
            'discount_value' => $discount_value,
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }

    public function getSpecialOffers() {
        $stmt = $this->pdo->query("SELECT * FROM SpecialOffers");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteSpecialOffer($id) {
        $stmt = $this->pdo->prepare("DELETE FROM SpecialOffers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
?>
