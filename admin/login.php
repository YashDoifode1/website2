<?php
/**
 * Admin Login Page (Password -> OTP)
 * Preserves original UI/styles; sends OTP after password verification.
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/session.php'; // for create/validate session functions if needed later
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already fully logged in and session valid, go to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && function_exists('validateAdminSession') && validateAdminSession()) {
    header('Location: ' . SITE_URL . '/admin/dashboard.php');
    exit;
}

$error_message = '';
$success_message = '';
$username = '';

// Handle POST — validate username/password, then send OTP (do NOT create final session)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // reCAPTCHA Validation (keep your existing behaviour)
    if (empty($recaptcha_response)) {
        $error_message = 'Please complete the reCAPTCHA verification.';
    } else {
        // Validate with Google
        $verify = @file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret=" . urlencode(RECAPTCHA_SECRET_KEY) .
            "&response=" . urlencode($recaptcha_response) .
            "&remoteip=" . ($_SERVER['REMOTE_ADDR'] ?? '')
        );
        $response = $verify ? json_decode($verify) : null;
        if (!$response || !$response->success) {
            $error_message = 'reCAPTCHA verification failed. Please try again.';
        }
    }

    if (empty($error_message)) {
        if (empty($username) || empty($password)) {
            $error_message = 'Please enter both username and password.';
        } else {
            // Anti-timing delay (keeps original behaviour)
            usleep(random_int(300000, 600000));

            try {
                $pdo = getDbConnection();

                // Fetch admin by username (and ensure active)
                $stmt = $pdo->prepare("SELECT id, username, email, password_hash, is_active, otp_expires FROM admin_users WHERE username = ? LIMIT 1");
                $stmt->execute([$username]);
                $admin = $stmt->fetch();

                if (!$admin || (int)$admin['is_active'] !== 1) {
                    // Generic error so attackers can't enumerate
                    $error_message = 'Invalid username or password.';
                } else {
                    // Check account lock/failed logic is handled by your existing auth; here we do password verify only
                    if (!isset($admin['password_hash']) || !password_verify($password, $admin['password_hash'])) {
                        // Optional: update failed_attempts / locked_until here if you want (or rely on authenticateAdmin)
                        $error_message = 'Invalid username or password.';
                    } else {
                        // Password correct — generate OTP and send email
                        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                        $otp_hash = password_hash($otp, PASSWORD_DEFAULT);
                        $expires = date('Y-m-d H:i:s', time() + 300); // 5 minutes

                        // Store OTP hash and expiry in DB
                        $update = $pdo->prepare("UPDATE admin_users SET otp_hash = ?, otp_expires = ? WHERE id = ?");
                        $update->execute([$otp_hash, $expires, $admin['id']]);

                        // Save pending admin id in session (not logged in yet)
                        $_SESSION['pending_admin_id'] = (int)$admin['id'];
                        $_SESSION['pending_admin_username'] = $admin['username'] ?? $username;
                        $_SESSION['otp_requested_at'] = time();

                        // Send OTP email with PHPMailer (similar to your forgot_password implementation)
                        $mail_sent = false;
                        try {
                            $mail = new PHPMailer(true);
                            $mail->isSMTP();
                            $mail->Host       = SMTP_HOST;
                            $mail->SMTPAuth   = true;
                            $mail->Username   = SMTP_USER;
                            $mail->Password   = SMTP_PASS;
                            // use STARTTLS as in your sample
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port       = SMTP_PORT;

                            $mail->setFrom(ADMIN_EMAIL, "SecureAdmin Support");
                            $mail->addAddress((string)$admin['email']);
                            $mail->isHTML(true);
                            $mail->Subject = "Your SecureAdmin Login OTP";

                            $otp_display = htmlspecialchars($otp, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

                            $mail->Body = "
                            <div style='font-family: Arial, sans-serif; max-width:600px;margin:auto;padding:20px;'>
                                <h2 style='color:#4361ee;'>Your SecureAdmin OTP</h2>
                                <p>Hello <strong>" . htmlspecialchars($admin['username']) . "</strong>,</p>
                                <p>Your One-Time Password (OTP) to complete sign in is:</p>
                                <p style='font-size:24px;font-weight:700;letter-spacing:4px;text-align:center;margin:20px 0;'>$otp_display</p>
                                <p>This code is valid for <strong>5 minutes</strong>. If you did not request this, please contact support.</p>
                                <hr style='margin:20px 0;border:1px solid #eee;'>
                                <p style='color:#999;font-size:12px;'>© " . date('Y') . " SecureAdmin — This is an automated message.</p>
                            </div>";

                            $mail->send();
                            $mail_sent = true;
                        } catch (Exception $e) {
                            error_log("OTP mail error: " . $e->getMessage());
                            // Do not reveal mail errors to user. We'll still redirect so UX is consistent.
                            $mail_sent = false;
                        }

                        // Redirect to OTP verification page regardless of mail send success to avoid leaking info
                        header('Location: verify_otp.php');
                        exit;
                    }
                }
            } catch (Throwable $e) {
                error_log("Login error: " . $e->getMessage());
                $error_message = 'An unexpected error occurred. Please try again later.';
            }
        }
    }
}

// URL messages handling (preserve original)
if (isset($_GET['error'])) {
    $error_message = match ($_GET['error']) {
        'unauthorized' => 'Please login to access the admin panel.',
        'session_expired' => 'Your session has expired. Please login again.',
        default => 'Access denied. Please login.'
    };
}
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success_message = 'You have been successfully logged out.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Secure Access</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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

        .features { margin-top: 40px; }
        .feature { display: flex; align-items: center; margin-bottom: 18 dedicati; }
        .feature i { margin-right: 12px; font-size: 20px; }

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
        .input-with-icon i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--dark-gray);
            font-size: 18px;
        }
        .input-with-icon input {
            width: 100%;
            padding: 16px 16px 16px 50px;
            border: 1px solid var(--medium-gray);
            border-radius: 12px;
            font-size: 16px;
            transition: var(--transition);
        }
        .input-with-icon input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.2);
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--dark-gray);
            cursor: pointer;
            font-size: 18px;
        }

        .recaptcha-container {
            margin: 25px 0;
            display: flex;
            justify-content: center;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
        }

        .message {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 5px solid;
        }
        .message.error { background: rgba(230, 57, 70, 0.1); color: var(--error-color); border-color: var(--error-color); }
        .message.success { background: rgba(42, 157, 143, 0.1); color: var(--success-color); border-color: var(--success-color); }

        .security-notice {
            margin-top: 30px;
            padding: 18px;
            background: var(--light-gray);
            border-radius: 12px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            color: var(--dark-gray);
        }
        .security-notice i { color: var(--success-color); font-size: 18px; margin-top: 2px; }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }
        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
        }
        .forgot-password a:hover { text-decoration: underline; }

        @media (max-width: 768px) {
            .login-container { flex-direction: column; max-width: 500px; min-height: auto; }
            .login-left { padding: 40px 30px; border-radius: 20px 20px 0 0; }
            .login-right { padding: 40px 30px; }
            .welcome-text h1 { font-size: 28px; }
            .form-header h2 { font-size: 26px; }
        }
        @media (max-width: 480px) {
            .login-left, .login-right { padding: 30px 25px; }
            .welcome-text h1 { font-size: 24px; }
            .form-header h2 { font-size: 24px; }
        }

        .btn-login.loading { pointer-events: none; opacity: 0.8; }
        .btn-login.loading i { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Panel -->
        <div class="login-left">
            <div class="logo">
                <i class="fas fa-shield-alt logo-icon"></i>
                <span class="logo-text">SecureAdmin</span>
            </div>
            <div class="welcome-text">
                <h1>Welcome Back</h1>
                <p>Secure access to your admin dashboard. Your data is protected with enterprise-grade security.</p>
            </div>
            <div class="features">
                <div class="feature"><i class="fas fa-lock"></i> End-to-end encryption</div>
                <div class="feature"><i class="fas fa-robot"></i> Google reCAPTCHA protected</div>
                <div class="feature"><i class="fas fa-shield-check"></i> Session fingerprinting</div>
                <div class="feature"><i class="fas fa-mobile-alt"></i> Fully responsive design</div>
            </div>
        </div>

        <!-- Right Panel -->
        <div class="login-right">
            <div class="form-header">
                <h2>Admin Login</h2>
                <p>Enter your credentials to access the panel</p>
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

            <form method="POST" id="loginForm">
                <div class="form-group">
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-with-icon">
                        <i class="fas fa-key"></i>
                        <input type="password" name="password" id="password" placeholder="Password" required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="recaptcha-container">
                    <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In Securely</span>
                </button>
            </form>

            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>

            <div class="security-notice">
                <i class="fas fa-info-circle"></i>
                <div>
                    This admin panel is protected with Google reCAPTCHA, secure session management, and encrypted connections.
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');
            const loginBtn = document.getElementById('loginBtn');

            toggleBtn?.addEventListener('click', () => {
                const type = passwordField.type === 'password' ? 'text' : 'password';
                passwordField.type = type;
                toggleBtn.innerHTML = type === 'password' 
                    ? '<i class="fas fa-eye"></i>' 
                    : '<i class="fas fa-eye-slash"></i>';
            });

            document.getElementById('loginForm').addEventListener('submit', function(e) {
                const response = grecaptcha.getResponse();
                if (!response) {
                    e.preventDefault();
                    alert('Please complete the reCAPTCHA verification.');
                    return;
                }

                loginBtn.classList.add('loading');
                loginBtn.innerHTML = '<i class="fas fa-spinner"></i> Authenticating...';
            });
        });
    </script>
</body>
</html>
