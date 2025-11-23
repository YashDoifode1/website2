<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

$error = '';
$success = '';

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = trim($_POST['email'] ?? '');
    
    if (!$email) {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            $pdo = getDbConnection();

            // Check if email exists
            $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if (!$admin) {
                $error = "No account found with this email.";
            } else {
                // Generate secure token
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour

                // Save token in database
                $update = $pdo->prepare("UPDATE admin_users SET reset_token = ?, reset_expires = ? WHERE id = ?");
                $update->execute([$token, $expires, $admin['id']]);

                // Create reset link
                $reset_link = SITE_URL . "/admin/reset_password.php?token=" . urlencode($token);

                // Send email
                $mail = new PHPMailer(true);

                try {
                    // SMTP Settings (configure yours)
                    $mail->isSMTP();
                    $mail->Host       = SMTP_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = SMTP_USER;
                    $mail->Password   = SMTP_PASS;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = SMTP_PORT;

                    $mail->setFrom(ADMIN_EMAIL, "Admin Support");
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = "Password Reset Request";
                    $mail->Body = "
                        <p>Hello,</p>
                        <p>You requested a password reset. Click below:</p>
                        <p><a href='$reset_link' style='padding:10px 15px; background:#333; color:#fff; text-decoration:none;'>Reset Password</a></p>
                        <p>This link is valid for 1 hour.</p>
                    ";

                    $mail->send();
                    $success = "A password reset link has been sent to your email.";

                } catch (Exception $e) {
                    $error = "Mail Error: " . $mail->ErrorInfo;
                }
            }

        } catch (Exception $e) {
            $error = "System error. Try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        body { font-family: Arial; background:#f3f3f3; padding:40px; }
        .box { max-width:400px; margin:auto; background:#fff; padding:25px; border-radius:8px; }
        input { width:100%; padding:10px; margin-bottom:15px; }
        button { width:100%; padding:10px; background:#333; color:#fff; border:none; cursor:pointer; }
        .msg { padding:10px; border-radius:4px; margin-bottom:15px; }
        .error { background:#ffb3b3; }
        .success { background:#b0ffb3; }
    </style>
</head>
<body>

<div class="box">
    <h2>Forgot Password</h2>

    <?php if ($error): ?>
        <div class="msg error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="msg success"><?= htmlspecialchars($success) ?></div>
        <a href="login.php"><button>Back to Login</button></a>
    <?php else: ?>

    <form method="POST">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="Enter your registered email" required>

        <button type="submit">Send Reset Link</button>
    </form>

    <?php endif; ?>

    <br>
    <a href="login.php">← Back to Login</a>
</div>

</body>
</html>
