<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/profiledata.php';
require_once __DIR__ . '/../src/PHPMailer.php';
require_once __DIR__ . '/../src/Exception.php';
require_once __DIR__ . '/../src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

class ProfileHandler {
    private $model;
    private $currentUser;
    
    public function __construct() {
        global $conn;
        $this->model = new ProfileData($conn);
        
        if (isset($_SESSION['email'])) {
            $this->currentUser = $this->model->loadUserByEmail($_SESSION['email']);
        }
    }
    
    public function handleRequest() {
        if (!isset($_SESSION['email'])) {
            header('Location: index.php');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_profile'])) {
                $this->handleProfileUpdate($_POST);
            } elseif (isset($_POST['send_password_code'])) {
                $this->handleSendPasswordCode($_POST);
            } elseif (isset($_POST['change_password_verify'])) {
                $this->handlePasswordChangeVerify($_POST);
            } elseif (isset($_POST['cancel_password_code'])) {
                $this->cancelPasswordCode();
            } elseif (isset($_POST['change_password'])) {
                $this->handlePasswordChange($_POST);
            } elseif (isset($_POST['change_picture']) && isset($_FILES['profile_pic'])) {
                $this->handlePictureUpload($_FILES['profile_pic']);
            }
            
            header('Location: profile.php');
            exit();
        }
    }
    
    private function handleProfileUpdate($post_data) {
        $newName = trim($post_data['name'] ?? '');
        $newEmail = trim($post_data['email'] ?? '');
        
        if (empty($newName) || empty($newEmail)) {
            $_SESSION['profile_error'] = "Both fields are required!";
            return;
        }
        
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['profile_error'] = "Invalid email format!";
            return;
        }
        
        if ($newEmail !== $this->currentUser['email']) {
            $emailDomain = strtolower(substr(strrchr($newEmail, '@'), 1));
            $allowedDomains = ['gmail.com', 'freemail.com'];
            if (!in_array($emailDomain, $allowedDomains, true)) {
                $_SESSION['profile_error'] = "When changing email, only gmail.com or freemail.com addresses are allowed.";
                return;
            }
        }
        
        if (strlen($newName) < 3) {
            $_SESSION['profile_error'] = "Username must be at least 3 characters!";
            return;
        }
        
        if ($newEmail !== $this->currentUser['email'] && 
            $this->model->checkEmailExists($newEmail, $this->currentUser['id'])) {
            $_SESSION['profile_error'] = "Email already exists!";
            return;
        }
        
        if ($this->model->updateProfile($this->currentUser['id'], $newName, $newEmail)) {
            $_SESSION['profile_success'] = "Profile updated successfully!";
            $_SESSION['email'] = $newEmail;
            $_SESSION['name'] = $newName;
            $this->currentUser['name'] = $newName;
            $this->currentUser['email'] = $newEmail;
        } else {
            $_SESSION['profile_error'] = "Update failed! Please try again.";
        }
    }
    
    private function handlePasswordChange($post_data) {
        $currentPassword = $post_data['current_password'] ?? '';
        $newPassword = $post_data['new_password'] ?? '';
        $confirmPassword = $post_data['confirm_password'] ?? '';
        
        $hashedPassword = $this->model->getCurrentPassword($this->currentUser['id']);
        
        if (!password_verify($currentPassword, $hashedPassword)) {
            $_SESSION['password_error'] = "The current password is incorrect!";
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['password_error'] = "The new passwords do not match!";
            return;
        }
        
        if (strlen($newPassword) < 6) {
            $_SESSION['password_error'] = "Password must be at least 6 characters!";
            return;
        }
        
        if ($this->model->changePassword($this->currentUser['id'], $newPassword)) {
            $mailSent = $this->sendPasswordChangedEmail($this->currentUser['email']);
            $_SESSION['password_success'] = $mailSent
                ? "Password successfully changed. Confirmation email sent!"
                : "Password successfully changed, but confirmation email could not be sent.";
        } else {
            $_SESSION['password_error'] = "Password change failed!";
        }
    }

    private function handleSendPasswordCode($post_data) {
        $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
        
        if ($email === '') {
            $_SESSION['password_error'] = 'Email is required.';
            return;
        }

        try {
            $verificationCode = $this->generateVerificationCode();
            $expiresAt = time() + 120;

            if (!$this->sendResetCodeEmail($email, $verificationCode)) {
                throw new Exception('Failed to send verification code email. Please try again.');
            }

            $_SESSION['password_code_hash'] = hash('sha256', $verificationCode);
            $_SESSION['password_code_email'] = $email;
            $_SESSION['password_code_expires_at'] = $expiresAt;
            $_SESSION['password_code_verified'] = true;

            $_SESSION['password_code_success'] = 'Verification code sent. Enter it within 2 minutes.';
        } catch (Exception $e) {
            $_SESSION['password_error'] = $e->getMessage();
        }
    }

    private function handlePasswordChangeVerify($post_data) {
        $email = $post_data['password_email_hidden'] ?? '';
        $newPassword = $post_data['new_password'] ?? '';
        $confirmPassword = $post_data['confirm_password'] ?? '';
        $verificationCode = trim($post_data['password_verification_code'] ?? '');
        
        $validationErrors = [];
        
        if ($newPassword === '') {
            $validationErrors[] = 'New password is required.';
        }
        
        if ($confirmPassword === '') {
            $validationErrors[] = 'Password confirmation is required.';
        }

        if ($verificationCode === '') {
            $validationErrors[] = 'Verification code is required.';
        }
        
        if (!empty($validationErrors)) {
            $_SESSION['password_error'] = implode(' ', $validationErrors);
            return;
        }
        
        if ($newPassword !== $confirmPassword) {
            $_SESSION['password_error'] = 'Passwords do not match.';
            return;
        }

        $storedCodeHash = $_SESSION['password_code_hash'] ?? '';
        $storedCodeEmail = $_SESSION['password_code_email'] ?? '';
        $codeExpiresAt = (int)($_SESSION['password_code_expires_at'] ?? 0);

        if ($storedCodeHash === '' || $storedCodeEmail === '' || $storedCodeEmail !== $email) {
            $_SESSION['password_error'] = 'Please request a verification code first.';
            return;
        }

        if (time() > $codeExpiresAt) {
            $this->clearPasswordCodeSession();
            $_SESSION['password_error'] = 'Verification code expired. Please request a new code.';
            return;
        }

        if (!hash_equals($storedCodeHash, hash('sha256', $verificationCode))) {
            $_SESSION['password_error'] = 'Invalid verification code.';
            return;
        }
        
        if (strlen($newPassword) < 6) {
            $_SESSION['password_error'] = 'Password must be at least 6 characters!';
            return;
        }
        
        if ($this->model->changePassword($this->currentUser['id'], $newPassword)) {
            $this->clearPasswordCodeSession();
            $mailSent = $this->sendPasswordChangedEmail($this->currentUser['email']);
            $_SESSION['password_code_success'] = $mailSent
                ? "Password successfully changed. Confirmation email sent!"
                : "Password successfully changed, but confirmation email could not be sent.";
        } else {
            $_SESSION['password_error'] = "Password change failed!";
        }
    }

    private function cancelPasswordCode() {
        $this->clearPasswordCodeSession();
    }

    private function generateVerificationCode(): string {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function clearPasswordCodeSession() {
        unset($_SESSION['password_code_hash']);
        unset($_SESSION['password_code_email']);
        unset($_SESSION['password_code_expires_at']);
        unset($_SESSION['password_code_verified']);
    }

    private function sendResetCodeEmail(string $email, string $code): bool {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            $mail->Body =
                '<!doctype html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,sans-serif;">' .
                '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f6fb;padding:24px 0;">' .
                '<tr><td align="center">' .
                '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:94%;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">' .
                '<tr><td style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);padding:28px;color:#ffffff;">' .
                '<h1 style="margin:0;font-size:24px;line-height:1.3;">Aqua Mini Shop</h1>' .
                '<p style="margin:8px 0 0 0;font-size:14px;opacity:0.92;">Password Reset</p>' .
                '</td></tr>' .
                '<tr><td style="padding:32px 28px;">' .
                '<h2 style="margin:0 0 16px 0;font-size:22px;color:#0f172a;">Your verification code</h2>' .
                '<p style="margin:0 0 24px 0;color:#334155;font-size:15px;line-height:1.6;">Use this code to reset your password. It will expire in 2 minutes.</p>' .
                '<div style="margin:28px 0;padding:20px;background:#f0f9ff;border:2px dashed #3b82f6;border-radius:12px;text-align:center;">' .
                '<p style="margin:0;font-size:12px;color:#0c4a6e;font-weight:bold;letter-spacing:2px;font-family:\'Courier New\',monospace;">' . htmlspecialchars($code) . '</p>' .
                '</div>' .
                '<p style="margin:0;color:#64748b;font-size:13px;line-height:1.5;">Do not share this code with anyone. Aqua Mini Shop staff will never ask for it.</p>' .
                '</td></tr>' .
                '<tr><td style="padding:16px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:12px;">This is an automated security email from Aqua Mini Shop.</td></tr>' .
                '</table>' .
                '</td></tr></table></body></html>';
            $mail->AltBody = 'Your password reset code is: ' . $code . '. Do not share this code with anyone.';

            return $mail->send();
        } catch (MailException $e) {
            return false;
        }
    }

    private function sendPasswordChangedEmail(string $email): bool {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your password was changed';
            $mail->Body =
                '<!doctype html><html><head><meta charset="UTF-8"></head><body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,sans-serif;">' .
                '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3f6fb;padding:24px 0;">' .
                '<tr><td align="center">' .
                '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:94%;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e7eb;">' .
                '<tr><td style="background:linear-gradient(135deg,#22c55e,#16a34a);padding:24px 28px;color:#ffffff;">' .
                '<h1 style="margin:0;font-size:24px;line-height:1.3;">Aqua Mini Shop</h1>' .
                '<p style="margin:6px 0 0 0;font-size:14px;opacity:0.92;">Security notification</p>' .
                '</td></tr>' .
                '<tr><td style="padding:28px;">' .
                '<h2 style="margin:0 0 12px 0;font-size:22px;color:#0f172a;">Your password was changed</h2>' .
                '<p style="margin:0 0 12px 0;color:#334155;font-size:15px;line-height:1.6;">This is a confirmation that your Aqua Mini Shop account password has been updated successfully.</p>' .
                '<div style="margin:18px 0;padding:14px 16px;border:1px solid #bbf7d0;border-radius:12px;background:#f0fdf4;color:#166534;font-size:14px;line-height:1.5;">If you made this change, no further action is needed.</div>' .
                '<div style="margin:0;padding:14px 16px;border:1px solid #fecaca;border-radius:12px;background:#fef2f2;color:#991b1b;font-size:14px;line-height:1.5;">If this was not you, contact support immediately and secure your account.</div>' .
                '</td></tr>' .
                '<tr><td style="padding:16px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;color:#64748b;font-size:12px;">This is an automated security email from Aqua Mini Shop.</td></tr>' .
                '</table>' .
                '</td></tr></table></body></html>';
            $mail->AltBody = 'Your Aqua Mini Shop password was changed successfully. If this was not you, contact support immediately.';

            return $mail->send();
        } catch (MailException $e) {
            return false;
        }
    }
    
    private function handlePictureUpload($file_data) {
        $targetDir = "uploads/";
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $fileName = basename($file_data["name"]);
        $targetFile = $targetDir . uniqid() . "_" . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $maxFileSize = 5 * 1024 * 1024;
        
        if (!in_array($imageFileType, $allowedTypes)) {
            $_SESSION['picture_error'] = "Only JPG, JPEG, PNG and GIF files are allowed!";
            return;
        }
        
        if ($file_data["size"] > $maxFileSize) {
            $_SESSION['picture_error'] = "File is too large. Maximum size is 5MB!";
            return;
        }
        
        if (move_uploaded_file($file_data["tmp_name"], $targetFile)) {
            if ($this->model->updateProfilePicture($this->currentUser['id'], $targetFile)) {
                $this->model->deleteOldProfilePicture($this->currentUser['profile_pic']);
                $_SESSION['picture_success'] = "Profile picture uploaded successfully!";
                $this->currentUser['profile_pic'] = $targetFile;
            } else {
                $_SESSION['picture_error'] = "Failed to update profile picture in database!";
                unlink($targetFile); 
            }
        } else {
            $_SESSION['picture_error'] = "There was an error uploading your file!";
        }
    }
    
    public function getCurrentUser() {
        return $this->currentUser;
    }
    
    public function getUserFavorites() {
        return $this->model->getUserFavorites($this->currentUser['id']);
    }
}

$handler = new ProfileHandler();
$handler->handleRequest();
?>
