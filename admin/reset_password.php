<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config.php';

session_start();

// Read token from URL
$token = $_GET['token'] ?? '';
$error = '';
$success = '';

// Validate token exists
if (!$token) {
    die("Invalid password reset link.");
}

// Get DB connection
$pdo = getDbConnection();

// Check token validity
$stmt = $pdo->prepare("SELECT id, reset_expires FROM admin_users WHERE reset_token = ? LIMIT 1");
$stmt->execute([$token]);
$admin = $stmt->fetch();

if (!$admin) {
    die("Invalid reset token.");
}

if (strtotime($admin['reset_expires']) < time()) {
    die("This reset link has expired. Please request a new one.");
}

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    // Validation
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Hash new password
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Update admin password and clear token
        $update = $pdo->prepare("
            UPDATE admin_users 
            SET password_hash = ?, reset_token = NULL, reset_expires = NULL
            WHERE id = ?
        ");
        $update->execute([$hash, $admin['id']]);

        $success = "Your password has been successfully updated!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Secure Admin</title>
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
        .welcome-text p { opacity: 0.9; }

        .features { margin-top: 30px; }
        .feature { display: flex; align-items: center; margin-bottom: 15px; }
        .feature i { margin-right: 10px; font-size: 18px; }

        .form-header { margin-bottom: 30px; }
        .form-header h2 { font-size: 28px; color: var(--text-color); margin-bottom: 10px; }
        .form-header p { color: var(--dark-gray); }

        .form-group { margin-bottom: 20px; position: relative; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--text-color); }

        .input-with-icon { position: relative; }
        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--dark-gray);
        }
        .input-with-icon input {
            width: 100%;
            padding: 15px 15px 15px 45px;
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
        }
        .btn-submit i { margin-right: 10px; }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .btn-back {
            display: inline-flex;
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
            display: none;
            border-left: 4px solid;
        }
        .message.error { background: rgba(230, 57, 70, 0.1); color: var(--error-color); border-color: var(--error-color); display: block; }
        .message.success { background: rgba(42, 157, 143, 0.1); color: var(--success-color); border-color: var(--success-color); display: block; }

        .security-notice {
            margin-top: 25px;
            padding: 15px;
            background: var(--light-gray);
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            align-items: flex-start;
        }
        .security-notice i { color: var(--success-color); margin-right: 10px; margin-top: 2px; }

        /* Responsive */
        @media (max-width: 768px) {
            .reset-container { flex-direction: column; max-width: 500px; }
            .reset-left { border-radius: 20px 20px 0 0; padding: 30px; }
            .welcome-text h1, .form-header h2 { font-size: 26px; }
        }
        @media (max-width: 480px) {
            .reset-left, .reset-right { padding: 25px; }
            .welcome-text h1, .form-header h2 { font-size: 22px; }
        }

        /* Loading */
        .btn-submit.loading { pointer-events: none; opacity: 0.8; }
        .btn-submit.loading i { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="reset-container">
        <!-- Left Side - Branding -->
        <div class="reset-left">
            <div class="logo">
                <i class="fas fa-shield-alt logo-icon"></i>
                <span class="logo-text">SecureAdmin</span>
            </div>
            <div class="welcome-text">
                <h1>Set New Password</h1>
                <p>Create a strong, unique password to keep your account secure.</p>
            </div>
            <div class="features">
                <div class="feature"><i class="fas fa-lock"></i><span>Encrypted connection</span></div>
                <div class="feature"><i class="fas fa-shield-check"></i><span>Password strength enforced</span></div>
                <div class="feature"><i class="fas fa-key"></i><span>Token automatically cleared after use</span></div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="reset-right">
            <div class="form-header">
                <h2>Create New Password</h2>
                <p>Enter your new password below.</p>
            </div>

            <!-- Messages -->
            <?php if ($error): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form class="reset-form" method="POST" id="resetForm">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Enter new password" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm">Confirm Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm" name="confirm" placeholder="Confirm new password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-key"></i> Update Password
                    </button>
                </form>
            <?php endif; ?>

            <a href="login.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>

            <div class="security-notice">
                <i class="fas fa-info-circle"></i>
                <div>Your session is protected. After changing your password, you will be securely logged out of all other devices.</div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const pwd = document.getElementById('password').value;
            const confirm = document.getElementById('confirm').value;
            const btn = document.getElementById('submitBtn');

            if (pwd.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return;
            }
            if (pwd !== confirm) {
                e.preventDefault();
                alert('Passwords do not match.');
                return;
            }

            btn.classList.add('loading');
            btn.innerHTML = '<i class="fas fa-spinner"></i> Updating...';
        });
    </script>
</body>
</html>