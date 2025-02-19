<?php
require_once __DIR__ . '/../controllers/MenuController.php';
$menuController = new MenuController();

// Sanitize input data to prevent XSS or other injection attacks
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

try {
    if (isset($_POST['add_category'])) {
        $category_name = sanitize_input($_POST['category_name']);
        if (!empty($category_name)) {
            $menuController->addCategory($category_name);
        } else {
            throw new Exception("Category name cannot be empty.");
        }
    } elseif (isset($_POST['delete_category'])) {
        $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
        if ($category_id) {
            $menuController->deleteCategory($category_id);
        } else {
            throw new Exception("Invalid category ID.");
        }
    } elseif (isset($_POST['add_menu_item'])) {
        // Validate and sanitize the input for menu item
        $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
        $menu_name = sanitize_input($_POST['menu_name']);
        $description = sanitize_input($_POST['description']);
        $profile_pic = $_FILES['profile_pic'];
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        
        if ($category_id && $menu_name && !empty($menu_name) && $price) {
            $stmt = $this->pdo->prepare("INSERT INTO menuitems (category_id, name, description, price, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$category_id, $menu_name, $description, $price, $profile_pic['name']]);
        } else {
            throw new Exception("Invalid data for menu item.");
        }
    } elseif (isset($_POST['add_special_offer'])) {
        $menu_item_id = filter_var($_POST['menu_item_id'], FILTER_VALIDATE_INT);
        $discount_value = filter_var($_POST['discount_value'], FILTER_VALIDATE_FLOAT);
        $start_date = sanitize_input($_POST['start_date']);
        $end_date = sanitize_input($_POST['end_date']);
        
        if ($menu_item_id && $discount_value && $start_date && $end_date) {
            $menuController->addSpecialOffer($menu_item_id, 'Percentage', $discount_value, $start_date, $end_date);
        } else {
            throw new Exception("Invalid data for special offer.");
        }
    }

    // Redirect to the menu management page
    header("Location: ../views/staff/menu-management.php?success=1");
} catch (Exception $e) {
    // Redirect with error message
    header("Location: ../views/staff/menu-management.php?error=" . urlencode($e->getMessage()));
}

exit();
?>
