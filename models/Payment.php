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
        $validStatuses = ['Pending', 'Completed', 'Failed'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid status value: " . $status);
        }
    
        try {
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
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
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

?>
