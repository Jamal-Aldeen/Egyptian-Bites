<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

// Authorization check
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access']);
    exit();
}

$action = $_GET['action'] ?? '';
$reservationId = $_GET['id'] ?? null;

try {
    switch ($action) {
        case 'update':
            $date = $_POST['date'];
            $time = $_POST['time'];
            $guests = $_POST['guests'];
            updateReservation($reservationId, $date, $time, $guests);
            break;
            
        case 'cancel':
            updateReservationStatus($reservationId, 'Cancelled');
            break;
            
        case 'confirm':
            updateReservationStatus($reservationId, 'Confirmed');
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function updateReservationStatus($id, $status) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Reservations SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Reservation not found');
    }
}

function updateReservation($id, $date, $time, $guests) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE Reservations 
        SET date = ?, time = ?, number_of_guests = ?
        WHERE id = ?");
    $stmt->execute([$date, $time, $guests, $id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Reservation not found');
    }
}