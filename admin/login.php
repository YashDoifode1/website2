<?php
/**
 * Admin Login Page
 * Now with Google reCAPTCHA v2 Protection and Session Management
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/../config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in with valid session
if (isAdminLoggedIn()) {
    if (function_exists('validateAdminSession')) {
        if (validateAdminSession()) {
            header('Location: ' . SITE_URL . '/admin/dashboard.php');
            exit;
        }
    } else {
        header('Location: ' . SITE_URL . '/admin/dashboard.php');
        exit;
    }
}

$error_message = '';
$username = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // === reCAPTCHA Validation ===
    if (empty($recaptcha_response)) {
        $error_message = 'Please complete the reCAPTCHA verification.';
    } else {
        $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
        $response = file_get_contents(
            $verify_url . 
            '?secret=' . urlencode(RECAPTCHA_SECRET_KEY) . 
            '&response=' . urlencode($recaptcha_response) .
            '&remoteip=' . ($_SERVER['REMOTE_ADDR'] ?? '')
        );
        $response_data = json_decode($response);

        if (!$response_data || !$response_data->success) {
            $error_message = 'reCAPTCHA verification failed. Please try again.';
        }
    }

    // === Login Validation (only if reCAPTCHA passed) ===
    if (empty($error_message)) {
        if (empty($username) || empty($password)) {
            $error_message = 'Please enter both username and password.';
        } else {
            usleep(rand(200000, 500000)); // Prevent timing attacks

            // Authenticate user and get admin ID
            $admin_id = authenticateAdmin($username, $password);
            if ($admin_id) {
                // Create session record
                $session_created = createAdminSession($admin_id, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');
                
                if ($session_created) {
                    session_regenerate_id(true);
                    header('Location: ' . SITE_URL . '/admin/dashboard.php');
                    exit;
                } else {
                    $error_message = 'Failed to create session. Please try again.';
                }
            } else {
                $error_message = 'Invalid username or password.';
            }
        }
    }
}

// URL messages
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'unauthorized':
            $error_message = 'Please login to access the admin panel.';
            break;
        case 'session_expired':
            $error_message = 'Your session has expired. Please login again.';
            break;
        case 'system_error':
            $error_message = 'A system error occurred. Please try again.';
            break;
    }
}

if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $success_message = 'You have been successfully logged out.';
}

$page_title = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Grand Jyothi Construction</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        /* Your existing CSS styles remain the same */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(102, 102, 102, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(102, 102, 102, 0.1) 0%, transparent 50%);
            z-index: -1;
        }

        .login-container {
            max-width: 440px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: white;
            padding: 3rem 2.5rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(90deg, #555, #777);
        }

        .login-header { text-align: center; margin-bottom: 2.5rem; }
        .login-header h1 { font-family: 'Montserrat', sans-serif; font-weight: 700; color: #333; margin-bottom: 0.5rem; font-size: 1.75rem; }
        .login-header p { color: #666; font-size: 1rem; }

        .brand-logo {
            width: 60px; height: 60px;
            background: linear-gradient(135deg, #555, #777);
            border-radius: 12px; margin: 0 auto 1.5rem;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.5rem; font-weight: 700;
        }

        .alert {
            padding: 1rem 1.25rem; border-radius: 8px; margin-bottom: 1.5rem;
            display: flex; align-items: flex-start; gap: 0.75rem; animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .alert-error { background: #fdf2f2; border: 1px solid #f8d7da; color: #721c24; }
        .alert-success { background: #f0f9f0; border: 1px solid #d1f0d1; color: #2d5a2d; }
        .alert i { flex-shrink: 0; margin-top: 0.125rem; }

        .form-group { margin-bottom: 1.5rem; }
        .form-label {
            display: block; margin-bottom: 0.5rem; font-weight: 600; color: #333;
            font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%; padding: 0.875rem 1rem; border: 2px solid #e0e0e0;
            border-radius: 8px; font-size: 1rem; transition: all 0.3s ease;
            background: white; font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none; border-color: #555;
            box-shadow: 0 0 0 3px rgba(85, 85, 85, 0.1); transform: translateY(-1px);
        }

        .form-input::placeholder { color: #999; }

        .password-container { position: relative; }
        .toggle-password {
            position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #666; cursor: pointer;
            padding: 0.25rem; border-radius: 4px; transition: color 0.3s ease;
        }
        .toggle-password:hover { color: #333; }

        /* reCAPTCHA Container */
        .recaptcha-container {
            margin: 1.8rem 0;
            display: flex;
            justify-content: center;
            transform: scale(0.95);
        }

        .btn {
            width: 100%; padding: 1rem 1.5rem; border: none; border-radius: 8px;
            font-size: 1rem; font-weight: 600; cursor: pointer;
            transition: all 0.3s ease; font-family: 'Montserrat', sans-serif;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #555, #666);
            color: white; box-shadow: 0 4px 15px rgba(85, 85, 85, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #444, #555);
            transform: translateY(-2px); box-shadow: 0 6px 20px rgba(85, 85, 85, 0.4);
        }

        .btn-primary:active { transform: translateY(0); }

        .login-links {
            text-align: center; margin-top: 2rem; padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }

        .login-links a {
            color: #555; text-decoration: none; font-size: 0.9rem;
            display: inline-flex; align-items: center; gap: 0.5rem;
            transition: color 0.3s ease;
        }
        .login-links a:hover { color: #333; }

        .security-notice {
            text-align: center; margin-top: 1.5rem; padding: 1rem;
            background: #f8f9fa; border-radius: 8px; border-left: 4px solid #666;
        }
        .security-notice small { color: #666; font-size: 0.8rem; line-height: 1.4; }

        .btn-loading { position: relative; color: transparent; }
        .btn-loading::after {
            content: ''; position: absolute; width: 20px; height: 20px;
            border: 2px solid transparent; border-top: 2px solid white;
            border-radius: 50%; animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        @media (max-width: 480px) {
            .login-card { padding: 2rem 1.5rem; margin: 0 0.5rem; }
            .login-header h1 { font-size: 1.5rem; }
            .brand-logo { width: 50px; height: 50px; font-size: 1.25rem; }
            .recaptcha-container { transform: scale(0.88); }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="brand-logo">GJ</div>
            
            <div class="login-header">
                <h1>Admin Panel</h1>
                <p>Grand Jyothi Construction</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i data-feather="alert-circle"></i>
                    <span><?= htmlspecialchars($error_message) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i data-feather="check-circle"></i>
                    <span><?= htmlspecialchars($success_message) ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm" novalidate>
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-input"
                           placeholder="Enter your username" value="<?= htmlspecialchars($username) ?>"
                           required autocomplete="username" autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-container">
                        <input type="password" id="password" name="password" class="form-input"
                               placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="toggle-password" id="togglePassword" aria-label="Toggle password visibility">
                            <i data-feather="eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Google reCAPTCHA v2 -->
                <div class="recaptcha-container">
                    <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY ?>"></div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="loginButton">
                    <i data-feather="log-in"></i>
                    <span>Sign In</span>
                </button>
            </form>
            
            <div class="login-links">
                <a href="<?= SITE_URL ?>/index.php">
                    <i data-feather="arrow-left"></i> Back to Website
                </a>
            </div>
            
            <div class="security-notice">
                <small>
                    <i data-feather="shield"></i>
                    Protected with Google reCAPTCHA • Secure Session Management
                </small>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();

            // Password toggle
            const toggleBtn = document.getElementById('togglePassword');
            const passInput = document.getElementById('password');
            if (toggleBtn && passInput) {
                toggleBtn.addEventListener('click', () => {
                    const type = passInput.type === 'password' ? 'text' : 'password';
                    passInput.type = type;
                    const icon = toggleBtn.querySelector('i');
                    icon.setAttribute('data-feather', type === 'password' ? 'eye' : 'eye-off');
                    feather.replace();
                });
            }

            // Form submit
            const form = document.getElementById('loginForm');
            const button = document.getElementById('loginButton');
            if (form && button) {
                form.addEventListener('submit', function(e) {
                    const response = grecaptcha.getResponse();
                    if (!response) {
                        e.preventDefault();
                        alert('Please complete the reCAPTCHA verification.');
                        return;
                    }

                    const span = button.querySelector('span');
                    if (span) span.textContent = 'Signing In...';
                    button.classList.add('btn-loading');
                    button.disabled = true;
                });
            }

            // Auto-focus
            const usernameField = document.getElementById('username');
            if (usernameField && !usernameField.value) usernameField.focus();
        });
    </script>
</body>
</html>