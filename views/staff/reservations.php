<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

// Query to fetch all reservations with user names
$sql = "SELECT r.id, r.user_id, u.full_name, r.date, r.time, r.number_of_guests, r.status
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                                        <th>User Name</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Guests</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reservations as $reservation): ?>
                                        <tr id="reservation-<?= $reservation['id'] ?>">
                                            <td><?= htmlspecialchars($reservation['id']) ?></td>
                                            <td><?= htmlspecialchars($reservation['full_name']) ?></td>
                                            <td><?= htmlspecialchars($reservation['date']) ?></td>
                                            <td><?= htmlspecialchars($reservation['time']) ?></td>
                                            <td><?= htmlspecialchars($reservation['number_of_guests']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $reservation['status'] === 'Confirmed' ? 'success' : 'danger' ?>">
                                                    <?= htmlspecialchars($reservation['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary edit-btn"
                                                        data-id="<?= $reservation['id'] ?>"
                                                        data-date="<?= $reservation['date'] ?>"
                                                        data-time="<?= $reservation['time'] ?>"
                                                        data-guests="<?= $reservation['number_of_guests'] ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger cancel-btn"
                                                        data-id="<?= $reservation['id'] ?>">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success confirm-btn"
                                                        data-id="<?= $reservation['id'] ?>"
                                                        <?= $reservation['status'] === 'Confirmed' ? 'disabled' : '' ?>>
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Reservation Modal -->
    <div class="modal fade" id="editReservationModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Reservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editReservationForm">
                    <div class="modal-body">
                        <input type="hidden" name="reservation_id" id="editReservationId">
                        <div class="mb-3">
                            <label>Date</label>
                            <input type="date" name="date" id="editDate" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Time</label>
                            <input type="time" name="time" id="editTime" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Number of Guests</label>
                            <input type="number" name="guests" id="editGuests" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and FontAwesome -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Edit Reservation
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const modal = new bootstrap.Modal(document.getElementById('editReservationModal'));
                    document.getElementById('editReservationId').value = this.dataset.id;
                    document.getElementById('editDate').value = this.dataset.date;
                    document.getElementById('editTime').value = this.dataset.time;
                    document.getElementById('editGuests').value = this.dataset.guests;
                    modal.show();
                });
            });

            // Handle Edit Form Submission
            document.getElementById('editReservationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                
                fetch('../../handlers/reservation-handler.php?action=update', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Refresh to show changes
                    } else {
                        alert(data.error || 'Failed to update reservation');
                    }
                });
            });

            // Cancel Reservation
            document.querySelectorAll('.cancel-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to cancel this reservation?')) {
                        const reservationId = this.dataset.id;
                        
                        fetch(`../../handlers/reservation-handler.php?action=cancel&id=${reservationId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const row = document.getElementById(`reservation-${reservationId}`);
                                row.querySelector('.badge').className = 'badge bg-danger';
                                row.querySelector('.badge').textContent = 'Cancelled';
                                row.querySelector('.confirm-btn').disabled = false;
                            } else {
                                alert(data.error || 'Failed to cancel reservation');
                            }
                        });
                    }
                });
            });

            // Confirm Reservation
            document.querySelectorAll('.confirm-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const reservationId = this.dataset.id;
                    
                    fetch(`../../handlers/reservation-handler.php?action=confirm&id=${reservationId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.getElementById(`reservation-${reservationId}`);
                            row.querySelector('.badge').className = 'badge bg-success';
                            row.querySelector('.badge').textContent = 'Confirmed';
                            this.disabled = true;
                        } else {
                            alert(data.error || 'Failed to confirm reservation');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>