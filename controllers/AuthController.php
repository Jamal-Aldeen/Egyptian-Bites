<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Validation.php';
// require_once 'PHPMailer/PHPMailerAutoload.php';  // Correct the path as per your file structure
// require_once __DIR__ .'vendor/autoload.php';  // This includes PHPMailer's autoloader
// require("PHPMailer/PHPMailerAutoload.php");
// require 'vendor/autoload.php';

class AuthController
{
    private $userModel;
    private $validation;
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
        $this->userModel = new User();
        $this->validation = new Validation();
    }

    // User registration
    public function register($full_name, $email, $password, $role)
    {
        $this->validation->checkEmptyFields([
            "Full Name" => $full_name,
            "Email" => $email,
            "Password" => $password
        ]);
        $this->validation->validateEmail($email, $this->pdo);
        $this->validation->validatePassword($password, $password);

        if (!$this->validation->isValid()) {
            throw new Exception(implode(" ", $this->validation->getErrors()));
        }

        if ($this->userModel->findByEmail($email)) {
            throw new Exception("Email already registered.");
        }

        $verificationToken = bin2hex(random_bytes(32));
        $userId = $this->userModel->create($full_name, $email, $password, $role, $verificationToken);
        $this->sendVerificationEmail($email, $verificationToken);

        return $userId;
    }

    // User login
    public function login($email, $password)
    {
        // Sanitize the email input
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        // Fetch user by email
        $user = $this->userModel->findByEmail($email);
    
        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password.");
        }
    
        if (!$user['verified']) {
            throw new Exception("Please verify your email before logging in.");
        }
    
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
    
        // Store session data
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
    
        // Redirect based on user role
        if ($user['role'] === 'Staff') {
            header("Location: /views/staff/dashboard.php");
        } else {
            header("Location: /views/customer/profile.php");
        }
        exit();
    }
    

    // Password reset logic
    
    
    public function resetPassword($userId, $currentPassword, $newPassword, $confirmNewPassword)
{
    $user = $this->userModel->findById($userId);

    if (!$user) {
        throw new Exception("User not found.");
    }

    if (!password_verify($currentPassword, $user['password'])) {
        throw new Exception("Current password is incorrect.");
    }

    if ($newPassword !== $confirmNewPassword) {
        throw new Exception("New passwords do not match.");
    }

    // Validate the new password (ensure it meets your password policies)
    $this->validation->validateNewPassword($newPassword);

    if (!$this->validation->isValid()) {
        throw new Exception(implode(" ", $this->validation->getErrors()));
    }

    // Hash the new password
    $newPasswordHashed = password_hash($newPassword, PASSWORD_BCRYPT);

    // Now, ensure that the password is being updated correctly in the database
    $result = $this->userModel->changePassword($userId, $newPasswordHashed);

    // After updating the password, verify it
    if ($result) {
        $user = $this->userModel->findById($userId);
        
        // Verify the password in the database using password_verify()
        if (password_verify($newPassword, $user['password'])) {
            // If password hash matches, update the session and success message
            $_SESSION['success'] = "Your password has been successfully updated.";
        } else {
            // If password verification fails
            throw new Exception("There was an error updating your password.");
        }
    } else {
        throw new Exception("Failed to update password.");
    }

    // Update session data and redirect to the profile page
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    header("Location: /views/customer/profile.php");
    exit();
}

   
public function sendVerificationCode($email) {
    // Validate the email address
    try {
        // Ensure email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address");
        }
        echo 'Verification code sent to ' . $email . '<br>';
    } catch (Exception $e) {
        // Handle any errors
        echo 'Error sending verification code: ' . $e->getMessage();
    }
    // Generate the verification code (this could be a random number or a token)
    $verificationCode = rand(100000, 999999);  // 6-digit code example

    // Store this code temporarily in the session
    $_SESSION['verification_code'] = $verificationCode;

    // Send the code to the user's email
    $subject = "Password Reset Verification Code";
    $body = "Your verification code is: " . $verificationCode;

    // Use PHPMailer to send the email
    $this->sendEmail($email, $subject, $body);
}

// Method to verify the code entered by the user
public function verifyCode($code) {
    if ($code == $_SESSION['verification_code']) {
        // If the code matches, proceed to reset the password
        return true;
    } else {
        throw new Exception('Invalid verification code.');
    }
}

// Method to send an email
private function sendEmail($email, $subject, $body) {
    require 'vendor/autoload.php';
$mail->SMTPDebug = 2;  // Show detailed debug information (2 = detailed)
$mail->Debugoutput = 'html';
    $mail = new \PHPMailer\PHPMailer\PHPMailer();

    // Set up SMTP settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com';  // Your email
    $mail->Password = 'your-email-password';  // Your email password or app password
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;  // SMTP port for TLS
    
    // Set up email content
    $mail->isHTML(true);  // Set email format to HTML
    $mail->From = 'your-email@gmail.com';  // Sender's email address
    $mail->FromName = 'Test Email';  // Sender's name
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email sent via PHPMailer.';
    $mail->addAddress('recipient@example.com');  // Recipient's email address
    
    // Send the email and check for errors
    if(!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;  // Error message
    } else {
        echo 'Email sent successfully!';  // Success message
    }
}

    
    // Logout method to ensure session is properly cleared if needed
    public function logout()
    {
        session_unset();
        session_destroy();
        header("Location: /login");
        exit();
    }

    // Add address method
    public function addAddress($userId, $addressData)
    {
        return $this->userModel->addAddress(
            $userId,
            $addressData['label'],
            $addressData['address_line1'],
            $addressData['address_line2'],
            $addressData['city']
        );
    }

    // Delete address method
    public function deleteAddress($addressId, $userId)
    {
        return $this->userModel->deleteAddress($addressId, $userId);
    }

    // Private method for handling profile picture upload
    private function handleProfilePicture($file, $userId)
    {
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . "/../public/uploads/";

            if (!is_dir($targetDir)) {
                if (!mkdir($targetDir, 0777, true)) {
                    error_log("Failed to create directory: $targetDir");
                    throw new Exception("Failed to create upload directory.");
                }
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = "user_{$userId}_" . time() . ".$extension";
            $targetFile = $targetDir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
                error_log("Upload failed. Check permissions for: $targetDir");
                throw new Exception("Failed to save profile picture.");
            }
            return $filename;
        } elseif ($file['error'] !== UPLOAD_ERR_NO_FILE) {
            error_log("Upload error code: " . $file['error']);
            throw new Exception("File upload error: " . $file['error']);
        }
        return null;
    }

    // Private method to send verification email
    private function sendVerificationEmail($email, $token)
    {
        $subject = "Verify Your Email";
        $message = "Click the link to verify your email: http://yourapp.com/verify-email?token=$token";
        mail($email, $subject, $message);
    }

    // Update user profile method
    public function updateProfile($userId, $fullName=null, $email=null
    
    , $profilePicture = null)
    {
        // error_log("Updating profile for user ID: $userId");
        // error_log("Full Name: $fullName, Email: $email");

        // Validate inputs
        // $this->validation->checkEmptyFields(["Full Name" => $fullName, "Email" => $email]);
        // $this->validation->validateEmail($email, $this->pdo, $userId);

        // if (!$this->validation->isValid()) {
        //     throw new Exception(implode(" ", $this->validation->getErrors()));
        // }

        // Handle profile picture upload
        $profilePictureName = $this->handleProfilePicture($profilePicture, $userId);
        error_log("Profile Picture Name: " . ($profilePictureName ?? "None"));

        // Update the profile
        $result = $this->userModel->updateProfile(
            $userId,
            $fullName,
            $email,
            $profilePictureName
        );

        if ($result) {
            error_log("Profile updated successfully.");
            // Refresh session data after successful update
            $user = $this->userModel->findById($userId);
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            if ($profilePictureName) {
                $_SESSION['profile_picture'] = $profilePictureName;
            }
        } else {
            error_log("Failed to update profile.");
        }

        return $result;
    }
}

// Handle profile actions
// Handle profile actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authController = new AuthController();
    $userId = $_SESSION['user_id'];

    try {
        // Perform the action based on the 'action' parameter
        switch ($_GET['action']) {
            case 'update_profile':
                $authController->updateProfile(
                    $userId,
                    $_POST['full_name'],
                    $_POST['email'],
                    $_FILES['profile_picture']
                );
                break;
                
            case 'reset_password':  // Add this case for resetting the password
                $authController->resetPassword(
                    $userId,
                    $_POST['current_password'],
                    $_POST['new_password'],
                    $_POST['confirm_new_password']
                );
                break;

            case 'add_address':
                $authController->addAddress($userId, $_POST);
                break;
                
            case 'delete_address':
                $authController->deleteAddress($_POST['address_id'], $userId);
                break;

            // Handle any unrecognized action
            default:
                throw new Exception("Unknown action: " . $_GET['action']);
        }

        // Redirect after successful execution of the action
        header("Location: /views/customer/profile.php");
        exit();
    } catch (Exception $e) {
        // If an exception occurs, store the error message in the session and redirect
        $_SESSION['error'] = $e->getMessage();
        header("Location: /views/customer/profile.php");
        exit();
    }
}
