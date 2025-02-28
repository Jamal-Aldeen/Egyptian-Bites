<?php
require_once __DIR__ . '/../config/db.php'; 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Payment {
    
    private $pdo; 

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    

    public function savePayment($user_id, $order_id, $payment_method, $status, $transaction_id, $amount) {
        $validStatuses = ['pending', 'completed', 'failed'];
        $status = trim($status); 
        error_log("Attempting to insert payment with status: " . $status);
        error_log("Payment method: " . $payment_method);
        error_log("Transaction ID: " . $transaction_id);
        error_log("Amount: " . $amount);
        error_log("Preparing to insert into Payments table: user_id = $user_id, order_id = $order_id, payment_method = $payment_method, amount = $amount, status = $status, transaction_id = $transaction_id");

        if ($payment_method == 'cash') {
            $transaction_id = null;
        }
        error_log("Attempting to insert payment with status: " . $status);

        if (!in_array($status, $validStatuses)) {
        error_log("Invalid status value received: " . $status);
        throw new Exception("Invalid status value: " . $status);
    }
    try {
        error_log("Attempting to insert payment with status: " . $status);

        $stmt = $this->pdo->prepare("
            INSERT INTO Payments (user_id, order_id, payment_method, amount, status, transaction_id) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $user_id, 
            $order_id, 
            $payment_method, 
            $amount, 
            $status, 
            $transaction_id ?? null
        ]);

        return ['status' => 'success'];
    } catch (PDOException $e) {
        error_log("SQL Error: " . $e->getMessage());
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}



//     public function savePayment($user_id, $order_id, $payment_method, $status, $transaction_id, $amount) {
//         if ($payment_method == 'cash') {
//             $transaction_id = null;
//         }
//         if (!in_array($status, ['pending', 'completed', 'failed'])) {
//             throw new Exception("Invalid status value: " . $status);
//         }
//         $stmt = $this->pdo->prepare(
//             "INSERT INTO Payments 
//             (user_id,order_id, payment_method, amount, status, transaction_id) 
//             VALUES (?,?, ?, ?, ?, ?)");
//         $stmt->execute([$user_id,$order_id, $payment_method, $amount, $status, $transaction_id ?? null]);
//         return $stmt->rowCount(); 
//         // $stmt = $this->pdo->prepare("INSERT INTO Payments (user_id, order_id, payment_method, status, transaction_id, amount) VALUES (?, ?, ?, ?, ?, ?)");
//         // $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
//         // $stmt->bindParam(2, $order_id, PDO::PARAM_INT);
//         // $stmt->bindParam(3, $payment_method, PDO::PARAM_STR);
//         // $stmt->bindParam(4, $status, PDO::PARAM_STR);
//         // $stmt->bindParam(5, $transaction_id, PDO::PARAM_STR);
//         // $stmt->bindParam(6, $amount, PDO::PARAM_STR);
        
//         return $stmt->execute(); 
//     }
// }
}
?>
