<?php
/**
 * verify_otp.php
 * Page shown after correct password: collects OTP, verifies, then creates session
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If user already logged in, redirect
if (isAdminLoggedIn() && validateAdminSession()) {
    header('Location: ' . SITE_URL . '/admin/dashboard.php');
    exit;
}

// Ensure we have a pending admin id
if (!isset($_SESSION['pending_admin_id'])) {
    header('Location: login.php');
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    if ($otp === '') {
        $error_message = 'Please enter the OTP sent to your email.';
    } else {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare("SELECT id, username, email, otp_hash, otp_expires FROM admin_users WHERE id = ? LIMIT 1");
            $stmt->execute([$_SESSION['pending_admin_id']]);
            $admin = $stmt->fetch();

            if (!$admin) {
                $error_message = 'Invalid request. Please login again.';
            } elseif (empty($admin['otp_hash']) || empty($admin['otp_expires']) || strtotime($admin['otp_expires']) < time()) {
                $error_message = 'OTP expired or not found. Please login again to request a new OTP.';
            } else {
                if (password_verify($otp, $admin['otp_hash'])) {
                    // Clear OTP fields
                    $clear = $pdo->prepare("UPDATE admin_users SET otp_hash = NULL, otp_expires = NULL WHERE id = ?");
                    $clear->execute([$admin['id']]);

                    // Create session now (use your createAdminSession function)
                    createAdminSession($admin['id'], $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');
                    session_regenerate_id(true);

                    // Set other session flags if your system expects them
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_login_time'] = time();

                    // Remove pending fields
                    unset($_SESSION['pending_admin_id'], $_SESSION['pending_admin_username'], $_SESSION['pending_admin_ip'], $_SESSION['pending_admin_ua']);

                    header('Location: ' . SITE_URL . '/admin/dashboard.php');
                    exit;
                } else {
                    $error_message = 'Incorrect OTP. Please try again.';
                }
            }
        } catch (Exception $e) {
            error_log('OTP verify error: ' . $e->getMessage());
            $error_message = 'An error occurred while verifying OTP. Please try again.';
        }
    }
}

// If user requests resend (optional)
if (isset($_GET['resend']) && $_GET['resend'] === '1') {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT id, username, email FROM admin_users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['pending_admin_id']]);
        $admin = $stmt->fetch();

        if ($admin) {
            $otp_plain = random_int(100000, 999999);
            $otp_hash = password_hash((string)$otp_plain, PASSWORD_DEFAULT);
            $otp_expires = date('Y-m-d H:i:s', time() + 300);

            $update = $pdo->prepare("UPDATE admin_users SET otp_hash = ?, otp_expires = ? WHERE id = ?");
            $update->execute([$otp_hash, $otp_expires, $admin['id']]);

            // Send via PHPMailer
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = SMTP_HOST;
                $mail->SMTPAuth   = true;
                $mail->Username   = SMTP_USER;
                $mail->Password   = SMTP_PASS;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = SMTP_PORT;

                $mail->setFrom(ADMIN_EMAIL, "SecureAdmin");
                $mail->addAddress($admin['email'], $admin['username']);
                $mail->isHTML(true);
                $mail->Subject = "Your SecureAdmin Login OTP (Resent)";

                $mail->Body = "
                    <div style='font-family:Arial, sans-serif; padding:20px; max-width:600px;'>
                        <h2 style='color:#4361ee'>Your Login OTP</h2>
                        <p>Use the following one-time code to complete your login. Expires in 5 minutes.</p>
                        <p style='font-size:28px; font-weight:700; text-align:center; margin:20px 0;'>$otp_plain</p>
                        <p style='color:#999; font-size:12px;'>© " . date('Y') . " SecureAdmin</p>
                    </div>";
                $mail->send();
                $success_message = 'A new OTP has been sent to your email.';
            } catch (Exception $e) {
                error_log('OTP resend mail error: ' . $e->getMessage());
                $success_message = 'A new OTP was generated. If the email is valid you will receive it shortly.';
            }
        } else {
            $error_message = 'Unable to resend OTP. Please login again.';
        }
    } catch (Exception $e) {
        error_log('OTP resend error: ' . $e->getMessage());
        $error_message = 'Unable to resend OTP. Please login again.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Verify OTP | SecureAdmin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    /* Use the same CSS as login.php for consistent look */
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
    .login-container {
        display: flex;
        width: 100%;
        max-width: 1000px;
        min-height: 620px;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: var(--box-shadow);
    }
    .login-left {
        flex: 1;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .login-left::before, .login-left::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
    }
    .login-left::before { width: 220px; height: 220px; top: -60px; left: -60px; }
    .login-left::after { width: 160px; height: 160px; bottom: -60px; right: -60px; }

    .logo { display: flex; align-items: center; margin-bottom: 30px; }
    .logo-icon { font-size: 32px; margin-right: 12px; }
    .logo-text { font-size: 28px; font-weight: 700; }

    .welcome-text h1 { font-size: 36px; margin-bottom: 12px; }
    .welcome-text p { opacity: 0.9; font-size: 16px; }

    .login-right {
        flex: 1;
        padding: 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .form-header { margin-bottom: 30px; text-align: center; }
    .form-header h2 { font-size: 30px; color: var(--text-color); margin-bottom: 8px; }
    .form-header p { color: var(--dark-gray); }

    .form-group { margin-bottom: 20px; position: relative; }
    .input-with-icon { position: relative; }
    .input-with-icon i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--dark-gray); font-size: 18px; }
    .input-with-icon input { width: 100%; padding: 16px 16px 16px 50px; border: 1px solid var(--medium-gray); border-radius: 12px; font-size: 16px; transition: var(--transition); }
    .input-with-icon input:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.2); }

    .btn-login { width: 100%; padding: 16px; background: var(--primary-color); color: white; border: none; border-radius: 12px; font-size: 17px; font-weight: 600; cursor: pointer; transition: var(--transition); display:flex; align-items:center; justify-content:center; gap:10px;}
    .btn-login:hover { background: var(--primary-dark); transform: translateY(-3px); box-shadow: 0 8px 20px rgba(67,97,238,0.3); }

    .message { padding: 16px; border-radius: 12px; margin-bottom: 20px; font-weight: 500; display:flex; align-items:center; gap:12px; border-left:5px solid; }
    .message.error { background: rgba(230,57,70,0.1); color: var(--error-color); border-color: var(--error-color); }
    .message.success { background: rgba(42,157,143,0.1); color: var(--success-color); border-color: var(--success-color); }

    .resend { text-align:center; margin-top:12px; font-size:14px; }
    .resend a { color: var(--primary-color); text-decoration:none; font-weight:600; }
    .resend a:hover { text-decoration: underline; }

    @media (max-width: 768px) {
        .login-container { flex-direction: column; max-width: 500px; min-height: auto; }
        .login-left { padding: 40px 30px; border-radius: 20px 20px 0 0; }
        .login-right { padding: 40px 30px; }
        .welcome-text h1 { font-size: 28px; }
        .form-header h2 { font-size: 26px; }
    }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="logo">
                <i class="fas fa-shield-alt logo-icon"></i>
                <span class="logo-text">SecureAdmin</span>
            </div>
            <div class="welcome-text">
                <h1>Verify OTP</h1>
                <p>We've sent a 6-digit code to your registered email. Enter it below to finish signing in.</p>
            </div>
        </div>

        <div class="login-right">
            <div class="form-header">
                <h2>Two-Factor Authentication</h2>
                <p>Enter the verification code</p>
            </div>

            <?php if ($error_message): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="otpForm">
                <div class="form-group">
                    <div class="input-with-icon">
                        <i class="fas fa-key"></i>
                        <input type="text" name="otp" maxlength="6" placeholder="Enter 6-digit code" required autofocus>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="verifyBtn">
                    <i class="fas fa-check-circle"></i>
                    <span>Verify & Sign In</span>
                </button>
            </form>

            <div class="resend">
                <p>Didn't receive the code? <a href="?resend=1">Resend OTP</a> or <a href="login.php">Start over</a></p>
            </div>

        </div>
    </div>

    <script>
    document.getElementById('otpForm')?.addEventListener('submit', function(e) {
        const otp = this.otp.value.trim();
        if (!/^\d{6}$/.test(otp)) {
            e.preventDefault();
            alert('Please enter the 6-digit OTP.');
            return;
        }
        const btn = document.getElementById('verifyBtn');
        btn.classList.add('loading');
        btn.innerHTML = '<i class="fas fa-spinner"></i> Verifying...';
    });
    </script>
</body>
</html>
