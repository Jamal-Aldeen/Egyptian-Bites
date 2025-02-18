<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Order.php'; 
require_once __DIR__ . '/../vendor/autoload.php';

class PaymentController {
    private $paymentModel;
    private $orderModel;

    public function __construct() {
        global $pdo; 
        $this->paymentModel = new Payment($pdo);
        $this->orderModel = new Order($pdo); 
    }
    public function processPayment($order_id, $user_id, $payment_method, $amount) {
        if ($payment_method == 'card') {
            if (isset($_POST['stripeToken'])) {
                error_log("Stripe Token: " . $_POST['stripeToken']);
                \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
                try {
                    $charge = \Stripe\Charge::create([
                        'amount' => $amount * 100, 
                        'currency' => 'usd',
                        'source' => $_POST['stripeToken'], 
                        'description' => "Payment for Order #$order_id"
                    ]);
                    $transaction_id = $charge->id;
                    if (strlen($transaction_id) > 255) {
                        throw new Exception("Transaction ID exceeds the maximum length of 255 characters.");
                    }
                    if ($charge->status == 'succeeded') {
                        $this->paymentModel->savePayment($user_id, $order_id, 'card', 'completed', $transaction_id, $amount);
                        $this->orderModel->updateOrderStatus($order_id, 'completed');
                        return ['status' => 'success'];
                    } else {

                        $this->paymentModel->savePayment($user_id, $order_id, 'card', 'failed', $transaction_id, $amount);
                        $this->orderModel->updateOrderStatus($order_id, 'failed');
                        return ['status' => 'error', 'message' => 'Payment failed'];
                    }
                } catch (\Exception $e) {
                    return ['status' => 'error', 'message' => $e->getMessage()];
                }
            } else {
                return ['status' => 'error', 'message' => 'Stripe token missing'];
            }
        } else if ($payment_method == 'cash') {
            $this->paymentModel->savePayment($user_id, $order_id, 'cash', 'pending', null, $amount);
            $this->orderModel->updateOrderStatus($order_id, 'pending');
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Invalid payment method'];
        }
    }
    // public function processPayment($order_id, $user_id, $payment_method, $amount) {

    //     if ($payment_method == 'card') {
    //         if (isset($_POST['stripeToken'])) {
    //             \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
    //             try {

    //                 $charge = \Stripe\Charge::create([
    //                     'amount' => $amount * 100, 
    //                     'currency' => 'usd',
    //                     'source' => $_POST['stripeToken'], 
    //                     'description' => "Payment for Order #$order_id"
    //                 ]);
    //                 $transaction_id = $charge->id;
    //                 if (strlen($transaction_id) > 255) {
    //                     throw new Exception("Transaction ID exceeds the maximum length of 255 characters.");
    //                 }
                    
    //                 if ($charge->status == 'succeeded') {
    //                     $this->paymentModel->savePayment($user_id, $order_id, 'card', 'completed', $transaction_id, $amount);
    //                     $this->orderModel->updateOrderStatus($order_id, 'completed'); 
    //                     return ['status' => 'success'];


    //                 } else {
    //                     $this->paymentModel->savePayment($user_id, $order_id, 'card', 'failed', $transaction_id, $amount);
    //                     $this->orderModel->updateOrderStatus($order_id, 'pending'); 
    //                     return ['status' => 'error', 'message' => 'Payment failed'];
    //                 }
    //             } catch (\Exception $e) {
    //                 return ['status' => 'error', 'message' => $e->getMessage()];
    //             }
    //         } else {
    //             return ['status' => 'error', 'message' => 'Stripe token missing'];
    //         }
    //     } else if ($payment_method == 'cash') {

    //         $this->paymentModel->savePayment($user_id, $order_id, 'cash', 'pending', null, $amount);
    //         $this->orderModel->updateOrderStatus($order_id, 'pending');                   
    //         return ['status' => 'success'];
    //     } else {
    //         return ['status' => 'error', 'message' => 'Invalid payment method'];
    //     }
    // }
    
    // public function processPayment($order_id, $user_id, $payment_method, $amount) {
    //     if ($payment_method == 'card') {
    //         if (isset($_POST['stripeToken'])) {
    //             \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
    //             try {
    //                 // Stripe Payment Process
    //                 $charge = \Stripe\Charge::create([
    //                     'amount' => $amount * 100, 
    //                     'currency' => 'usd',
    //                     'source' => $_POST['stripeToken'], 
    //                     'description' => "Payment for Order #$order_id"
    //                 ]);
    //                 $transaction_id = $charge->id;
    //                 if (strlen($transaction_id) > 255) {
    //                     throw new Exception("Transaction ID exceeds the maximum length of 255 characters.");
    //                 }
    //                 if ($charge->status == 'succeeded') {
    //                     $this->orderModel->updateOrderStatus($order_id, 'completed'); 
    //                     return ['status' => 'success'];
    //                 }else {
    //                     $this->orderModel->updateOrderStatus($order_id, 'pending'); 
    //                     return ['status' => 'success'];
    //                 }
    //                 $this->paymentModel->savePayment($user_id, $order_id, 'card', 'completed', $transaction_id, $amount);
    //                 return ['status' => 'success'];
    //             } catch (\Exception $e) {
    //                 return ['status' => 'error', 'message' => $e->getMessage()];
    //             }
    //         } else {
    //             return ['status' => 'error', 'message' => 'Stripe token missing'];
    //         }
    //     } else {
    //         // Cash on Delivery Payment - Save and Update Order status to 'pending'
    //         $this->paymentModel->savePayment($user_id, $order_id, 'cash', 'pending', null, $amount);
    //         $this->orderModel->updateOrderStatus($order_id, 'pending');                   
    //         return ['status' => 'success'];
    //     }
    // }

}

?>
