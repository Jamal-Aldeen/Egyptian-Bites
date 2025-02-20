<?php
require_once '../../config/db.php';

// Ensure an ID is provided
if (!isset($_GET['id'])) {
    http_response_code(400);
    exit('Menu item id not provided.');
}

$menu_item_id = intval($_GET['id']);

// Query the database to get the image filename for this menu item
$sql = "SELECT image FROM MenuItems WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $menu_item_id]);
$menuItem = $stmt->fetch(PDO::FETCH_ASSOC);

// Use a default image if none is set
$imageFile = 'default-menu.jpg';
if ($menuItem && !empty($menuItem['image'])) {
    $imageFile = $menuItem['image'];
}

// Define the correct folder path for menu images
$path = __DIR__ . '/../../public/uploads/menu-image/' . $imageFile;

// If the image file does not exist, return a 404 error
if (!file_exists($path)) {
    http_response_code(404);
    exit('Image not found.');
}

// Get the MIME type and output the image content
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $path);
finfo_close($finfo);

header("Content-Type: $mimeType");
header("Content-Length: " . filesize($path));
readfile($path);
?>
