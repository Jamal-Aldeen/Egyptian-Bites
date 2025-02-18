<?php
// Include the header
include __DIR__ . '/../layouts/header.php';
?>

<<<<<<< HEAD
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
=======
<!-- Booking Section -->
<div id="booking" class="container my-5">
    <h2 class="booking-heading">Book Your Table with Us</h2>
    <div class="row align-items-center">
        <!-- Left Side Image -->
        <div class="col-md-6 booking-image">
            <img src="../../public/assets/images/image.png" alt="Elegant Egyptian Dining">
>>>>>>> bf82ade92a81d8e40a59d06056c27e13bd4964b7
        </div>

        <!-- Right Side Form -->
        <div class="col-md-6">
            <div class="booking-section">
                <form action="/controllers/ReservationController.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="col-md-4">
                            <input type="email" class="form-control" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="col-md-4">
                            <input type="tel" class="form-control" name="phone" placeholder="Your Phone" required>
                        </div>

                        <div class="col-md-4">
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="col-md-4">
                            <input type="time" class="form-control" name="time" required>
                        </div>
                        <div class="col-md-4">
                            <input type="number" class="form-control" name="guests" placeholder="# of People" min="1" required>
                        </div>

                        <div class="col-12">
                            <textarea class="form-control" name="requests" rows="4" placeholder="Special Requests"></textarea>
                        </div>

                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-book w-100">Book a Table</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include the footer
include __DIR__ . '/../layouts/footer.php';
?>
