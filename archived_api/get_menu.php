<?php
require_once __DIR__ . '/../../config/db.php';  

header('Content-Type: application/json'); 

if (!isset($conn) || $conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$query = "SELECT id, name, description, price, image FROM MenuItems WHERE availability = 1";
$result = $conn->query($query);

if (!$result) {
    echo json_encode(["status" => "error", "message" => "Query execution failed"]);
    exit;
}

$menuItems = [];
while ($row = $result->fetch_assoc()) {
    $menuItems[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "description" => $row["description"],
        "price" => (float) $row["price"],
        "image" => "/public/images/" . $row["image"]  
    ];
}

echo json_encode(["status" => "success", "data" => $menuItems]);
?>
