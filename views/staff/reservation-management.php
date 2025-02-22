<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

require_once __DIR__ . '/../../controllers/ReservationController.php';

$controller = new ReservationController();
$reservations = $controller->getReservations(); // Call the getter function
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Reservations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <div class="row">
    <?php
         require_once "../layouts/sidebar.php";
         ?>
    <div class=" col-md-9 ms-sm-auto col-lg-10 px-4">
    <h2 class="text-center">Manage Reservations</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Customer ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>Guests</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation) { ?>
            <tr>
                <td><?= $reservation['user_id']; ?></td>
                <td><?= $reservation['date']; ?></td>
                <td><?= $reservation['time']; ?></td>
                <td><?= $reservation['number_of_guests']; ?></td>
                <td><span class="badge bg-success"><?= $reservation['status']; ?></span></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
   
    </div>
       
</body>
</html>
