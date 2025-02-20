<?php
require_once __DIR__ . '/../controllers/MenuController.php';
$menuController = new MenuController();

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

try {
    if (isset($_POST['add_category'])) {
        $categoryName = sanitize_input($_POST['category_name']);
        if (!empty($categoryName)) {
            $menuController->addCategory($categoryName);
        } else {
            throw new Exception("Category name cannot be empty.");
        }
    } elseif (isset($_POST['delete_category'])) {
        $categoryId = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
        if ($categoryId) {
            $menuController->deleteCategory($categoryId);
        } else {
            throw new Exception("Invalid category ID.");
        }
    } elseif (isset($_POST['add_menu_item'])) {
        // Validate and sanitize inputs for menu item
        $categoryId  = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
        $menuName    = sanitize_input($_POST['menu_name']);
        $description = sanitize_input($_POST['description']);
        $price       = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);

        // Handle menu image upload from the 'menu_image' field
        $menuImageName = "";
        if (isset($_FILES['menu_image']) && !empty($_FILES['menu_image']['name'])) {
            $targetDir = __DIR__ . "/../public/uploads/menu-image/";
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $menuImage     = $_FILES['menu_image'];
            $imageFileType = strtolower(pathinfo($menuImage['name'], PATHINFO_EXTENSION));
            
            // Validate allowed file extensions
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedExtensions)) {
                throw new Exception("Invalid file type. Allowed types: " . implode(", ", $allowedExtensions));
            }
            
            // Generate a unique filename
            $menuImageName = time() . "_" . bin2hex(random_bytes(5)) . "." . $imageFileType;
            
            // Validate file size (limit: 2MB)
            if ($menuImage['size'] > 2 * 1024 * 1024) {
                throw new Exception("Menu image must be less than 2MB.");
            }
            
            // Attempt to move the uploaded file to the target directory
            if (!move_uploaded_file($menuImage['tmp_name'], $targetDir . $menuImageName)) {
                throw new Exception("Failed to upload menu image.");
            }
        }

        if ($categoryId && !empty($menuName) && $price !== false) {
            // '1' is assumed as the default availability flag
            $menuController->addMenuItem($categoryId, $menuName, $description, $price, $menuImageName, 1);
        } else {
            throw new Exception("Invalid data for menu item.");
        }
    } elseif (isset($_POST['add_special_offer'])) {
        $menuItemId    = filter_var($_POST['menu_item_id'], FILTER_VALIDATE_INT);
        $discountValue = filter_var($_POST['discount_value'], FILTER_VALIDATE_FLOAT);
        $startDate     = sanitize_input($_POST['start_date']);
        $endDate       = sanitize_input($_POST['end_date']);
        
        if ($menuItemId && $discountValue !== false && !empty($startDate) && !empty($endDate)) {
            $menuController->addSpecialOffer($menuItemId, 'Percentage', $discountValue, $startDate, $endDate);
        } else {
            throw new Exception("Invalid data for special offer.");
        }
    }

    // Redirect to the menu management page with a success flag
    header("Location: ../views/staff/menu-management.php?success=1");
} catch (Exception $e) {
    // Redirect with an error message
    header("Location: ../views/staff/menu-management.php?error=" . urlencode($e->getMessage()));
}

exit();
?>
