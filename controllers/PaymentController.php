<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Order.php'; 
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php'; 
use setasign\Fpdi\Fpdi;

class PaymentController {
    private $paymentModel;
    private $orderModel;

    public function __construct() {
        global $pdo; 
        $this->paymentModel = new Payment($pdo);
        $this->orderModel = new Order($pdo); 
    }

    public function processPayment($order_id, $user_id, $payment_method, $amount) {
        $payment_status = '';
        $order_status = '';
        $transaction_id = null; 
        if ($payment_method == 'card') {
            if (!isset($_POST['stripeToken'])) {
                return ['status' => 'error', 'message' => 'Stripe token missing'];
            }

            \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
            try {
                $charge = \Stripe\Charge::create([
                    'amount' => $amount * 100, 
                    'currency' => 'usd',
                    'source' => $_POST['stripeToken'],
                    'description' => "Payment for Order #$order_id"
                ]);

                $transaction_id = $charge->id;

                if ($charge->status == 'succeeded') {
                    $payment_status = 'Completed'; 
                    $order_status = 'Preparing';
                    $this->sendNotification($user_id, "Payment completed for Order #$order_id");
                } else {
                    $payment_status = 'Failed'; 
                    $order_status = 'Pending'; 
                }

                if (strlen($transaction_id) > 255) {
                    throw new Exception("Transaction ID exceeds maximum length");
                }

            } catch (\Exception $e) {
                $this->paymentModel->savePayment(
                    $user_id, 
                    $order_id, 
                    'card', 
                    'Failed', 
                    null, 
                    $amount
                );
                return ['status' => 'error', 'message' => $e->getMessage()];
            }

        } else if ($payment_method == 'cash') {
            $payment_status = 'Pending'; 
            $order_status = 'Pending'; 
        } else {
            return ['status' => 'error', 'message' => 'Invalid payment method'];
        }

        if (!in_array($payment_status, ['Pending', 'Completed', 'Failed'])) {
            return ['status' => 'error', 'message' => "Invalid payment status: $payment_status"];
        }

        if (!in_array($order_status, ['Pending', 'Preparing', 'Ready', 'Delivered'])) {
            return ['status' => 'error', 'message' => "Invalid order status: $order_status"];
        }

        $this->paymentModel->savePayment(
            $user_id, 
            $order_id, 
            $payment_method, 
            $payment_status, 
            $transaction_id, 
            $amount
        );

        $this->orderModel->updateOrderStatusAndPaymentStatus(
            $order_id, 
            $order_status, 
            $payment_status
        );

        session_write_close();
        header("Location: /views/customer/order-tracking.php?order_id=$order_id");
        exit();
    }

    private function sendNotification($user_id, $message) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (:user_id, 'Payment Update', :message)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
    }
    
}

// function generateInvoice($order_id, $total_amount) {
//     // require('fpdf.php');
//     $pdf = new FPDF();
//     $pdf->AddPage();
//     $pdf->SetFont('Arial', 'B', 16);
//     $pdf->Cell(40, 10, 'Invoice for Order #' . $order_id);
//     $pdf->Ln();
//     $pdf->Cell(40, 10, 'Total Amount: $' . $total_amount);
//     $pdf->Output('F', 'invoices/invoice_' . $order_id . '.pdf');
// }

// if (!file_exists('invoices')) {
//     mkdir('invoices', 0777, true);
// }
?>
