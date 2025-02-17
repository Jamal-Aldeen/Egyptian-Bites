<?php
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../../vendor/autoload.php';

class PaymentController {
    private $paymentModel;

    public function __construct() {
        $this->paymentModel = new Payment();
    }

    public function processPayment($order_id, $user_id, $payment_method, $amount) {
        if ($payment_method == 'card') {
            \Stripe\Stripe::setApiKey(STRIPE_API_KEY);
            try {
                $charge = \Stripe\Charge::create([
                    'amount' => $amount * 100, 
                    'currency' => 'usd',
                    'source' => $_POST['stripeToken'], 
                    'description' => "Payment for Order #$order_id"
                ]);

                if ($charge->status == 'succeeded') {
                    return $this->paymentModel->savePayment($user_id, $order_id, 'card', 'completed', $charge->id, $amount);
                }
            } catch (\Exception $e) {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
        } else {
            return $this->paymentModel->savePayment($user_id, $order_id, 'cash', 'pending', null, $amount);
        }
    }
}
?>
<?php
require_once __DIR__ . '/../config/stripe.php';

echo "Stripe Secret Key: " . STRIPE_API_KEY;
echo "<br>";
echo "Stripe Publishable Key: " . STRIPE_PUBLISHABLE_KEY;
?>
