<?php
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

$query = "SELECT id, name, description, price, image FROM MenuItems WHERE availability = 1";
$result = $conn->query($query);

$menuItems = [];
while ($row = $result->fetch_assoc()) {
    $menuItems[] = $row;
}

echo json_encode($menuItems);
?>
