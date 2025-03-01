<?php
require_once __DIR__ . '/../vendor/autoload.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender {
    private $mail;
    private $pdo;

    public function __construct($pdo) { 
        $this->pdo = $pdo;
        $this->mail = new PHPMailer(true);
        try {
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'your-email@gmail.com'; // استبدل بالبريد الإلكتروني الخاص بك
            $this->mail->Password = 'your-app-password'; // استخدم كلمة مرور التطبيق من Google
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = 587;
            $this->mail->setFrom('no-reply@egyptianbites.com', 'Egyptian Bites');
        } catch (Exception $e) {
            error_log("Error initializing PHPMailer: " . $this->mail->ErrorInfo);
        }
    }

    public function sendInvoiceToUser($user_id, $invoicePath) {
        try {
            // استرجاع البريد الإلكتروني للمستخدم بناءً على `user_id`
            $stmt = $this->pdo->prepare("SELECT email FROM Users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || empty($user['email'])) {
                throw new Exception("User email not found.");
            }

            $user_email = $user['email'];
            error_log("Preparing to send invoice to: $user_email");

            // إعداد البريد الإلكتروني
            $this->mail->addAddress($user_email);
            $this->mail->Subject = "Your Invoice from Egyptian Bites";
            $this->mail->Body = "Dear Customer,\n\nPlease find attached your invoice.\n\nBest regards,\nEgyptian Bites Team";
            $this->mail->addAttachment($invoicePath); // إرفاق الفاتورة

            // إرسال البريد
            $this->mail->send();
            error_log("Invoice successfully sent to: $user_email");
        } catch (Exception $e) {
            error_log("Failed to send invoice: " . $this->mail->ErrorInfo);
        }
    }
}
?>
