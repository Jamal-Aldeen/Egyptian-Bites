<?php
// Include the header
include __DIR__ . '/../layouts/header.php';
?>

<!-- Booking Section -->
<div id="booking" class="container my-5">
    <h2 class="booking-heading">Book Your Table with Us</h2>
    <div class="row align-items-center">
        <!-- Left Side Image -->
        <div class="col-md-6 booking-image">
            <img src="../../public/assets/images/image.png" alt="Elegant Egyptian Dining">
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
