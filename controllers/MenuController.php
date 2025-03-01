<?php
require_once __DIR__ . '/../config/db.php';

class MenuController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    // Categories Management
    public function addCategory($name)
    {
        $stmt = $this->pdo->prepare("INSERT INTO MenuCategories (name) VALUES (:name)");
        return $stmt->execute(['name' => $name]);
    }

    public function updateCategory($id, $name)
    {
        $stmt = $this->pdo->prepare("UPDATE MenuCategories SET name = :name WHERE id = :id");
        return $stmt->execute(['id' => $id, 'name' => $name]);
    }

    public function deleteCategory($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM MenuCategories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getCategories()
    {
        $stmt = $this->pdo->query("SELECT * FROM MenuCategories");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Menu Items Management
    public function addMenuItem($category_id, $name, $description, $price, $image, $availability)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO MenuItems (category_id, name, description, price, image, availability) 
            VALUES (:category_id, :name, :description, :price, :image, :availability)
        ");
        return $stmt->execute([
            'category_id' => $category_id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image' => $image,
            'availability' => $availability
        ]);
    }

    public function updateMenuItem($id, $category_id, $name, $description, $price, $image, $availability)
    {
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
    public function updateCategoryStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE MenuCategories SET status = :status WHERE id = :id");
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }

    public function deleteMenuItem($id)
    {
        global $pdo;
        $sql = "DELETE FROM MenuItems WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function getMenuItems()
    {
        $stmt = $this->pdo->query("SELECT * FROM MenuItems");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Special Offers Management
     // Add Special Offer
     public function addSpecialOffer($menu_item_id, $discount_type, $discount_value, $start_date, $end_date)
     {
         // Check if an active offer exists for this item
         $stmt = $this->pdo->prepare("SELECT id FROM SpecialOffers WHERE menu_item_id = :menu_item_id AND end_date >= CURDATE()");
         $stmt->execute(['menu_item_id' => $menu_item_id]);
         
         if ($stmt->fetch()) {
             return ['success' => false, 'error' => 'An active special offer already exists for this item.'];
         }
 
         // Insert new offer
         $stmt = $this->pdo->prepare("INSERT INTO SpecialOffers (menu_item_id, discount_type, discount_value, start_date, end_date) 
                                      VALUES (:menu_item_id, :discount_type, :discount_value, :start_date, :end_date)");
         $stmt->execute([
             'menu_item_id' => $menu_item_id,
             'discount_type' => $discount_type,
             'discount_value' => $discount_value,
             'start_date' => $start_date,
             'end_date' => $end_date
         ]);
 
         return ['success' => true, 'offer_id' => $this->pdo->lastInsertId()];
     }
 


     public function getSpecialOffers()
     {
         $stmt = $this->pdo->prepare("
             SELECT so.id, mi.name AS menu_item, so.discount_type, so.discount_value, so.start_date, so.end_date
             FROM SpecialOffers so
             JOIN MenuItems mi ON so.menu_item_id = mi.id
             WHERE so.end_date >= CURDATE()
         ");
         $stmt->execute();
         return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }


     public function deleteSpecialOffer($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM SpecialOffers WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    public function getExpiringOffers()
    {
        $stmt = $this->pdo->query("
            SELECT m.name AS menu_item, s.discount_value, s.end_date 
            FROM SpecialOffers s
            JOIN MenuItems m ON s.menu_item_id = m.id
            WHERE s.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
