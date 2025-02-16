<?php
require_once __DIR__ . '/../config/db.php';

class Payment {
    public function savePayment($user_id, $order_id, $payment_method, $status, $transaction_id, $amount) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO Payments (user_id, order_id, payment_method, status, transaction_id, amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssd", $user_id, $order_id, $payment_method, $status, $transaction_id, $amount);
        return $stmt->execute();
    }
}
?>
