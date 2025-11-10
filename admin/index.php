<?php
/**
 * Admin Login & Dashboard
 * 
 * Handles admin authentication and displays dashboard
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// If already logged in, redirect to dashboard
if (isAdminLoggedIn()) {
    redirect('/constructioninnagpur/admin/dashboard.php');
}

$error_message = '';
$username = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        // Add small delay to prevent timing attacks
        usleep(rand(100000, 300000));
        
        if (authenticateAdmin($username, $password)) {
            // Regenerate session ID for security
            session_regenerate_id(true);
            redirect('/constructioninnagpur/admin/dashboard.php');
        } else {
            $error_message = 'Invalid username or password.';
            // Clear password for security
            $password = '';
        }
    }
}

// Check for error parameter
if (isset($_GET['error']) && $_GET['error'] === 'unauthorized') {
    $error_message = 'Please login to access the admin panel.';
}

// Check for logout
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
    
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        /* Background pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
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
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            background: white;
            padding: 3rem 2.5rem;
            border-radius: 16px;
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.9);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #555, #777);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .login-header h1 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
        }

        .login-header p {
            color: #666;
            font-size: 1rem;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #555, #777);
            border-radius: 12px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-error {
            background: #fdf2f2;
            border: 1px solid #f8d7da;
            color: #721c24;
        }

        .alert-success {
            background: #f0f9f0;
            border: 1px solid #d1f0d1;
            color: #2d5a2d;
        }

        .alert i {
            flex-shrink: 0;
            margin-top: 0.125rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus {
            outline: none;
            border-color: #555;
            box-shadow: 0 0 0 3px rgba(85, 85, 85, 0.1);
            transform: translateY(-1px);
        }

        .form-input::placeholder {
            color: #999;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: #333;
        }

        .btn {
            width: 100%;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #555, #666);
            color: white;
            box-shadow: 0 4px 15px rgba(85, 85, 85, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #444, #555);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(85, 85, 85, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .login-links {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }

        .login-links a {
            color: #555;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-links a:hover {
            color: #333;
        }

        .security-notice {
            text-align: center;
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #666;
        }

        .security-notice small {
            color: #666;
            font-size: 0.8rem;
            line-height: 1.4;
        }

        /* Loading state */
        .btn-loading {
            position: relative;
            color: transparent;
        }

        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-card {
                padding: 2rem 1.5rem;
                margin: 0 0.5rem;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }

            .brand-logo {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }

            body {
                padding: 0.5rem;
            }
        }

        @media (max-width: 360px) {
            .login-card {
                padding: 1.5rem 1rem;
            }

            .form-input {
                padding: 0.75rem 0.875rem;
            }

            .btn {
                padding: 0.875rem 1.25rem;
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
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
                    <span><?= sanitizeOutput($error_message) ?></span>
                </div>
            <?php endif; ?>
            
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i data-feather="check-circle"></i>
                    <span><?= sanitizeOutput($success_message) ?></span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           class="form-input"
                           placeholder="Enter your username" 
                           value="<?= sanitizeOutput($username) ?>"
                           required 
                           autocomplete="username"
                           autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-container">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-input"
                               placeholder="Enter your password" 
                               required
                               autocomplete="current-password">
                        <button type="button" class="toggle-password" id="togglePassword">
                            <i data-feather="eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="loginButton">
                    <i data-feather="log-in"></i> 
                    <span>Sign In</span>
                </button>
            </form>
            
            <div class="login-links">
                <a href="/constructioninnagpur/index.php">
                    <i data-feather="arrow-left"></i> 
                    Back to Website
                </a>
            </div>
            
            <div class="security-notice">
                <small>
                    <i data-feather="shield"></i>
                    Secure admin access only. Default credentials: admin / admin123
                </small>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.setAttribute('data-feather', type === 'password' ? 'eye' : 'eye-off');
                        feather.replace();
                    }
                });
            }

            // Form submission handling
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            
            if (loginForm && loginButton) {
                loginForm.addEventListener('submit', function() {
                    // Add loading state
                    const buttonText = loginButton.querySelector('span');
                    if (buttonText) {
                        buttonText.textContent = 'Signing In...';
                    }
                    loginButton.classList.add('btn-loading');
                    loginButton.disabled = true;
                });
            }

            // Auto-focus username field
            const usernameInput = document.getElementById('username');
            if (usernameInput && !usernameInput.value) {
                usernameInput.focus();
            }

            // Prevent form resubmission on page refresh
            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        });

        // Handle page visibility changes
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'hidden') {
                // Clear sensitive data when tab becomes inactive
                const passwordInput = document.getElementById('password');
                if (passwordInput) {
                    // Don't clear the value, just ensure it's not visible in DOM
                }
            }
        });
    </script>
</body>
</html>