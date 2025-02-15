<?php
define('DB_HOST', 'localhost:3306');
define('DB_NAME', 'restaurant_db');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
    $GLOBALS['pdo'] = new PDO($dsn, DB_USER, DB_PASSWORD);
    $GLOBALS['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Query to fetch all reservations
$sql = "SELECT r.id, u.id AS user_id,  r.date, r.time, r.number_of_guests 
        FROM Reservations r 
        JOIN Users u ON r.user_id = u.id";
$stmt = $GLOBALS['pdo']->prepare($sql);
$stmt->execute();
$reservations = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Reservations List</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Reservation ID</th>
                    <th>User ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Number of Guests</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reservation['id']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['date']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['time']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['number_of_guests']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>