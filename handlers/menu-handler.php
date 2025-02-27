<?php
require_once __DIR__ . '/../controllers/MenuController.php';
$menuController = new MenuController();

header('Content-Type: application/json');

function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

try {
    // 📌 Add Category
    if (isset($_POST['add_category'])) {
        $categoryName = sanitize_input($_POST['category_name']);
        if (empty($categoryName)) throw new Exception("Category name cannot be empty.");
        
        $menuController->addCategory($categoryName);
        echo json_encode([
            'success' => true,
            'categoryId' => $menuController->getLastInsertId(),
            'categoryName' => $categoryName
        ]);
        exit();
    }

    // 📌 Delete Category
    elseif (isset($_POST['delete_category'])) {
        $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        if (!$categoryId) throw new Exception("Invalid category ID.");
        
        $menuController->deleteCategory($categoryId);
        echo json_encode(['success' => true]);
        exit();
    }

    // 📌 Add Menu Item
    elseif (isset($_POST['add_menu_item'])) {
        $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $menuName = sanitize_input($_POST['menu_name']);
        $description = sanitize_input($_POST['description']);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);

        if (!$categoryId || empty($menuName) || $price === false) {
            throw new Exception("Invalid menu item data.");
        }

        // Handle Image Upload
        $menuImageName = null;
        if (!empty($_FILES['menu_image']['name'])) {
            $targetDir = __DIR__ . "/../public/uploads/menu-image/";
            if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
                throw new Exception("Failed to create upload directory.");
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['menu_image']['tmp_name']);
            $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];

            if (!array_key_exists($mimeType, $allowedMimes)) {
                throw new Exception("Invalid file type. Allowed: JPG, PNG, GIF.");
            }

            if ($_FILES['menu_image']['size'] > 2 * 1024 * 1024) {
                throw new Exception("File size exceeds 2MB limit.");
            }

            $extension = $allowedMimes[$mimeType];
            $menuImageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

            if (!move_uploaded_file($_FILES['menu_image']['tmp_name'], $targetDir . $menuImageName)) {
                throw new Exception("Failed to save uploaded file.");
            }
        }

        // Add to Database
        $itemId = $menuController->addMenuItem($categoryId, $menuName, $description, $price, $menuImageName, 1);

        echo json_encode([
            'success' => true,
            'itemId' => $itemId,
            'itemName' => $menuName,
            'categoryId' => $categoryId
        ]);
        exit();
    }

    // 📌 Add Special Offer
    elseif (isset($_POST['add_special_offer'])) {
        $menuItemId = filter_input(INPUT_POST, 'menu_item_id', FILTER_VALIDATE_INT);
        $discountType = sanitize_input($_POST['discount_type']);
        $discountValue = filter_input(INPUT_POST, 'discount_value', FILTER_VALIDATE_FLOAT);
        $startDate = sanitize_input($_POST['start_date']);
        $endDate = sanitize_input($_POST['end_date']);

        if (!$menuItemId || !in_array($discountType, ['Percentage', 'Fixed']) || 
            $discountValue === false || empty($startDate) || empty($endDate)) {
            throw new Exception("Invalid special offer data.");
        }

        // Validate Date Range
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        if ($end < $start) throw new Exception("End date must be after start date.");

        // Prevent Duplicate Offers
        $existingOffers = $menuController->getSpecialOffers();
        foreach ($existingOffers as $offer) {
            if ($offer['menu_item'] == $menuItemId && $offer['end_date'] >= date('Y-m-d')) {
                throw new Exception("An active special offer already exists for this item.");
            }
        }

        // Add Offer to Database
        $response = $menuController->addSpecialOffer($menuItemId, $discountType, $discountValue, $startDate, $endDate);
        echo json_encode($response);
        exit();
    }

    // 📌 Get Special Offers
    elseif (isset($_GET['get_special_offers'])) {
        echo json_encode([
            'success' => true,
            'offers' => $menuController->getSpecialOffers()
        ]);
        exit();
    }

    // 📌 Delete Special Offer
    elseif (isset($_POST['delete_special_offer'])) {
        $offerId = filter_input(INPUT_POST, 'offer_id', FILTER_VALIDATE_INT);
        if (!$offerId) throw new Exception("Invalid offer ID.");

        if ($menuController->deleteSpecialOffer($offerId)) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Failed to delete the special offer.");
        }
        exit();
    }

    else {
        throw new Exception("Invalid action.");
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
exit();
?>
