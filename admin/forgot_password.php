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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            $pdo = getDbConnection();

            $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if (!$admin) {
                // Security: Don't reveal if email exists
                $success = "If your email is registered, a password reset link has been sent.";
            } else {
                $token = bin2hex(random_bytes(32));
                $expires = date("Y-m-d H:i:s", time() + 3600);

                $update = $pdo->prepare("UPDATE admin_users SET reset_token = ?, reset_expires = ? WHERE id = ?");
                $update->execute([$token, $expires, $admin['id']]);

                $reset_link = SITE_URL . "/admin/reset_password.php?token=" . urlencode($token);

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = SMTP_HOST;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = SMTP_USER;
                    $mail->Password   = SMTP_PASS;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = SMTP_PORT;

                    $mail->setFrom(ADMIN_EMAIL, "SecureAdmin Support");
                    $mail->addAddress($email);
                    $mail->isHTML(true);
                    $mail->Subject = "Password Reset Request - SecureAdmin";

                    $mail->Body = "
                    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px;'>
                        <h2 style='color: #4361ee;'>Password Reset Request</h2>
                        <p>Hello,</p>
                        <p>You requested a password reset for your <strong>SecureAdmin</strong> account.</p>
                        <p>Click the button below to set a new password:</p>
                        <p style='text-align: center; margin: 30px 0;'>
                            <a href='$reset_link' style='background:#4361ee; color:white; padding:14px 28px; text-decoration:none; border-radius:10px; font-weight:bold; display:inline-block;'>
                                Reset Password
                            </a>
                        </p>
                        <p><small>This link will expire in <strong>1 hour</strong> for security.</small></p>
                        <p style='color: #666; font-size: 14px;'>
                            If you didn't request this, please ignore this email. Your password will remain unchanged.
                        </p>
                        <hr style='margin: 30px 0; border: 1px solid #eee;'>
                        <p style='color: #999; font-size: 12px;'>
                            © " . date('Y') . " SecureAdmin – Protected Administration Panel<br>
                            This is an automated message, please do not reply.
                        </p>
                    </div>";

                    $mail->send();
                    $success = "If your email is registered, a password reset link has been sent.";
                } catch (Exception $e) {
                    error_log("Mail error: " . $mail->ErrorInfo);
                    $success = "If your email is registered, a password reset link has been sent.";
                }
            }
        } catch (Exception $e) {
            error_log("Forgot password error: " . $e->getMessage());
            $success = "If your email is registered, a password reset link has been sent.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | SecureAdmin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --secondary-color: #7209b7;
            --text-color: #333;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --error-color: #e63946;
            --success-color: #2a9d8f;
            --box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
        }

        .reset-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            min-height: 600px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .reset-left {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .reset-left::before, .reset-left::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        .reset-left::before { width: 200px; height: 200px; top: -50px; left: -50px; }
        .reset-left::after { width: 150px; height: 150px; bottom: -50px; right: -50px; }

        .reset-right {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo { display: flex; align-items: center; margin-bottom: 30px; }
        .logo-icon { font-size: 28px; margin-right: 10px; }
        .logo-text { font-size: 24px; font-weight: 700; }

        .welcome-text h1 { font-size: 32px; margin-bottom: 10px; }
        .welcome-text p { opacity: 0.9; font-size: 16px; }

        .features { margin-top: 30px; }
        .feature { display: flex; align-items: center; margin-bottom: 15px; }
        .feature i { margin-right: 12px; font-size: 18px; }

        .form-header { margin-bottom: 30px; text-align: center; }
        .form-header h2 { font-size: 28px; color: var(--text-color); margin-bottom: 10px; }
        .form-header p { color: var(--dark-gray); }

        .form-group { margin-bottom: 20px; position: relative; }
        .input-with-icon { position: relative; }
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--dark-gray);
            font-size: 18px;
        }
        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 50px;
            border: 1px solid var(--medium-gray);
            border-radius: 10px;
            font-size: 16px;
            transition: var(--transition);
        }
        .input-with-icon input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-back {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: var(--light-gray);
            color: var(--text-color);
            border: 1px solid var(--medium-gray);
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        .btn-back:hover { background: var(--medium-gray); }

        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid;
        }
        .message.error { background: rgba(230, 57, 70, 0.1); color: var(--error-color); border-color: var(--error-color); }
        .message.success { background: rgba(42, 157, 143, 0.1); color: var(--success-color); border-color: var(--success-color); }

        .security-notice {
            margin-top: 25px;
            padding: 15px;
            background: var(--light-gray);
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: var(--dark-gray);
        }
        .security-notice i { color: var(--success-color); font-size: 16px; margin-top: 2px; }

        @media (max-width: 768px) {
            .reset-container { flex-direction: column; max-width: 500px; }
            .reset-left { border-radius: 20px 20px 0 0; padding: 30px; }
            .reset-right { padding: 30px; }
            .welcome-text h1, .form-header h2 { font-size: 26px; }
        }
        @media (max-width: 480px) {
            .reset-left, .reset-right { padding: 25px; }
            .welcome-text h1, .form-header h2 { font-size: 22px; }
        }

        .btn-submit.loading { pointer-events: none; opacity: 0.8; }
        .btn-submit.loading i { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="reset-container">
    <!-- Left Panel -->
    <div class="reset-left">
        <div class="logo">
            <i class="fas fa-shield-alt logo-icon"></i>
            <span class="logo-text">SecureAdmin</span>
        </div>
        <div class="welcome-text">
            <h1>Forgot Password?</h1>
            <p>No worries — it happens! Enter your email and we’ll help you get back in securely.</p>
        </div>
        <div class="features">
            <div class="feature"><i class="fas fa-lock"></i> Fully encrypted process</div>
            <div class="feature"><i class="fas fa-clock"></i> Reset link expires in 1 hour</div>
            <div class="feature"><i class="fas fa-envelope-shield"></i> Sent only to registered emails</div>
        </div>
    </div>

    <!-- Right Panel - Form -->
    <div class="reset-right">
        <div class="form-header">
            <h2>Recover Your Account</h2>
            <p>Enter your email address to receive a secure reset link</p>
        </div>

        <!-- Messages -->
        <?php if ($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!$success || $error): ?>
        <form method="POST" id="forgotForm">
            <div class="form-group">
                <div class="input-with-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Enter your email address" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-paper-plane"></i>
                <span>Send Reset Link</span>
            </button>
        </form>
        <?php endif; ?>

        <a href="login.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Login
        </a>

        <div class="security-notice">
            <i class="fas fa-info-circle"></i>
            <div>For your security, we never reveal whether an email is registered. If an account exists, you'll receive a reset link shortly.</div>
        </div>
    </div>
</div>

<script>
    document.getElementById('forgotForm')?.addEventListener('submit', function(e) {
        const email = this.email.value.trim();
        const btn = document.getElementById('submitBtn');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!email) {
            e.preventDefault();
            alert('Please enter your email address.');
            return;
        }
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address.');
            return;
        }

        btn.classList.add('loading');
        btn.innerHTML = '<i class="fas fa-spinner"></i> Sending...';
    });
</script>

</body>
</html>