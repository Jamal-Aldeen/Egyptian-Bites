<?php
require_once __DIR__ . '/../controllers/MenuController.php';
$menuController = new MenuController();

header('Content-Type: application/json');

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

try {
    if (isset($_POST['add_category'])) {
        $categoryName = sanitize_input($_POST['category_name']);
        if (empty($categoryName)) throw new Exception("Category name cannot be empty.");
        
        $menuController->addCategory($categoryName);
        echo json_encode([
            'success' => true,
            'categoryId' => $menuController->getLastInsertId(),
            'categoryName' => $categoryName
        ]);

    } elseif (isset($_POST['delete_category'])) {
        $categoryId = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
        if (!$categoryId) throw new Exception("Invalid category ID.");
        
        $menuController->deleteCategory($categoryId);
        echo json_encode(['success' => true]);

    } elseif (isset($_POST['add_menu_item'])) {
        // Validate inputs
        $categoryId = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
        $menuName = sanitize_input($_POST['menu_name']);
        $description = sanitize_input($_POST['description']);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        
        if (!$categoryId || empty($menuName) || $price === false) {
            throw new Exception("Invalid menu item data");
        }

        // Handle image upload
        $menuImageName = null;
        if (!empty($_FILES['menu_image']['name'])) {
            $targetDir = __DIR__ . "/../public/uploads/menu-image/";
            if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
                throw new Exception("Failed to create upload directory");
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['menu_image']['tmp_name']);
            $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];

            if (!array_key_exists($mimeType, $allowedMimes)) {
                throw new Exception("Invalid file type. Allowed: JPG, PNG, GIF");
            }

            if ($_FILES['menu_image']['size'] > 2 * 1024 * 1024) {
                throw new Exception("File size exceeds 2MB limit");
            }

            $extension = $allowedMimes[$mimeType];
            $menuImageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
            
            if (!move_uploaded_file($_FILES['menu_image']['tmp_name'], $targetDir . $menuImageName)) {
                throw new Exception("Failed to save uploaded file");
            }
        }

        // Add to database
        $itemId = $menuController->addMenuItem(
            $categoryId,
            $menuName,
            $description,
            $price,
            $menuImageName,
            1
        );

        echo json_encode([
            'success' => true,
            'itemId' => $itemId,
            'itemName' => $menuName,
            'categoryId' => $categoryId
        ]);

    } elseif (isset($_POST['add_special_offer'])) {
        // Validate inputs
        $menuItemId = filter_var($_POST['menu_item_id'], FILTER_VALIDATE_INT);
        $discountType = sanitize_input($_POST['discount_type']);
        $discountValue = filter_var($_POST['discount_value'], FILTER_VALIDATE_FLOAT);
        $startDate = sanitize_input($_POST['start_date']);
        $endDate = sanitize_input($_POST['end_date']);

        if (!$menuItemId || !in_array($discountType, ['Percentage', 'Fixed']) || 
            $discountValue === false || empty($startDate) || empty($endDate)) {
            throw new Exception("Invalid special offer data");
        }

        // Validate dates
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        if ($end < $start) throw new Exception("End date must be after start date");

        // Add to database
        $menuController->addSpecialOffer(
            $menuItemId,
            $discountType,
            $discountValue,
            $startDate,
            $endDate
        );

        echo json_encode(['success' => true]);

    } else {
        throw new Exception("Invalid action");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

exit();