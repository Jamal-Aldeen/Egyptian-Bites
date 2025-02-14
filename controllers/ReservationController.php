<?php
require_once __DIR__ . '/../models/Reservation.php';

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
        } else {
            echo "Failed to reserve.";
        }
    }

    // ✅ Fix: Getter function to access reservations
    public function getReservations() {
        return $this->reservationModel->getReservations();
    }
}
?>
