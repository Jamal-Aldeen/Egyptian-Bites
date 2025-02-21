<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Query to fetch all reservations
$sql = "SELECT r.id, r.user_id, u.full_name AS full_name, 
               r.date, r.time, r.number_of_guests
        FROM Reservations r
        JOIN Users u ON r.user_id = u.id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reservations List</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <!-- FontAwesome (optional) -->
  <link rel="stylesheet" 
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container-fluid mt-4">
  <div class="row">
    <?php require_once "../layouts/sidebar.php"; ?>
    
    <div class="col-md-9 ms-sm-auto col-lg-10 px-4 mt-4">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h2 class="mb-0">Reservations List</h2>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
              <thead class="table-dark">
                <tr>
                  <th>Reservation ID</th>
                  <th>User ID</th>
                  <th>User Name</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Number of Guests</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reservations as $reservation): ?>
                  <tr>
                    <td><?= htmlspecialchars($reservation['id']) ?></td>
                    <td><?= htmlspecialchars($reservation['user_id']) ?></td>
                    <td><?= htmlspecialchars($reservation['full_name']) ?></td>
                    <td><?= htmlspecialchars($reservation['date']) ?></td>
                    <td><?= htmlspecialchars($reservation['time']) ?></td>
                    <td><?= htmlspecialchars($reservation['number_of_guests']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div> <!-- table-responsive -->
        </div> <!-- card-body -->
      </div> <!-- card shadow -->
    </div> <!-- col-md-9 -->
  </div> <!-- row -->
</div> <!-- container-fluid -->

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>