<?php
session_start();
require_once __DIR__ . '/../../controllers/ReservationController.php';
$controller = new ReservationController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->bookTable($_POST);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Table Reservation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="text-center">Reserve a Table</h2>
    <form action="../staff/reservations.php" method="POST" class="card p-4 shadow">
        <div class="mb-3">
            <label for="date" class="form-label">Date:</label>
            <input type="date" name="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="time" class="form-label">Time:</label>
            <input type="time" name="time" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="guests" class="form-label">Number of Guests:</label>
            <input type="number" name="guests" class="form-control" min="1" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Book Now</button>
    </form>
</body>
</html>
