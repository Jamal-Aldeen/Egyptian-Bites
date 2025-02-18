<?php
require_once __DIR__ . '/../models/Reservation.php';
require_once __DIR__ . '/../config/db.php';

// Check for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ReservationController();
    $controller->bookTable($_POST);
}


class ReservationController {
    private $reservationModel;

    public function __construct() {
        $this->reservationModel = new Reservation();
    }

    // Function to book a table
    public function bookTable($data) {
        session_start();
        $userId = $_SESSION['user_id'] ?? null;

        if (!$userId) {
            die("User not logged in");
        }

        $date = $data['date'];
        $time = $data['time'];
        $guests = $data['guests'];

        if ($this->reservationModel->createReservation($userId, $date, $time, $guests)) {
            echo "Reservation successful!";
            header("Location: /views/customer/confirmation.php");
            exit;
        } else {
            echo "Failed to reserve.";
        }
    }

    // Getter function to access reservations
    public function getReservations() {
        return $this->reservationModel->getReservations();
    }
}
?>
