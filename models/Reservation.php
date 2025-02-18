<?php
require_once __DIR__ . '/../config/db.php';

class Reservation {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Function to create a reservation
    public function createReservation($userId, $date, $time, $guests) {
        $stmt = $this->pdo->prepare("INSERT INTO Reservations (user_id, date, time, number_of_guests, status) VALUES (?, ?, ?, ?, 'Confirmed')");
        return $stmt->execute([$userId, $date, $time, $guests]);
    }

    // Function to retrieve all reservations
    public function getReservations() {
        $stmt = $this->pdo->query("SELECT * FROM Reservations ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Function to get upcoming reservations count
    public function getUpcomingReservationCount() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM Reservations WHERE date >= CURDATE()");
        return $stmt->fetchColumn();
    }
}
?>
