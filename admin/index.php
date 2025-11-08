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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        if (authenticateAdmin($username, $password)) {
            redirect('/constructioninnagpur/admin/dashboard.php');
        } else {
            $error_message = 'Invalid username or password.';
        }
    }
}

// Check for error parameter
if (isset($_GET['error']) && $_GET['error'] === 'unauthorized') {
    $error_message = 'Please login to access the admin panel.';
}

$page_title = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Grand Jyothi Construction</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
        }
        
        .login-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h1 {
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Admin Panel</h1>
                <p>Grand Jyothi Construction</p>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert-error">
                    <i data-feather="alert-circle"></i>
                    <?= sanitizeOutput($error_message) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <label for="username">
                    Username
                    <input type="text" 
                           id="username" 
                           name="username" 
                           placeholder="Enter username" 
                           required 
                           autofocus>
                </label>
                
                <label for="password">
                    Password
                    <input type="password" 
                           id="password" 
                           name="password" 
                           placeholder="Enter password" 
                           required>
                </label>
                
                <button type="submit">
                    <i data-feather="log-in"></i> Login
                </button>
            </form>
            
            <p style="text-align: center; margin-top: 1rem;">
                <small>
                    <a href="/constructioninnagpur/index.php">← Back to Website</a>
                </small>
            </p>
            
            <p style="text-align: center; margin-top: 2rem;">
                <small style="color: #6c757d;">
                    Default credentials: admin / admin123
                </small>
            </p>
        </div>
    </div>
    
    <script>
        feather.replace();
    </script>
</body>
</html>
