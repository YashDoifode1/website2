<?php
/**
 * Authentication System
 * 
 * Handles admin authentication and session management
 */

declare(strict_types=1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

/**
 * Check if admin is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function isAdminLoggedIn(): bool
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin authentication
 * Redirects to login page if not authenticated
 * 
 * @return void
 */
function requireAdmin(): void
{
    if (!isAdminLoggedIn()) {
        redirect('/constructioninnagpur/admin/index.php?error=unauthorized');
    }
}

/**
 * Authenticate admin user
 * 
 * @param string $username Username
 * @param string $password Password
 * @return bool True if authenticated, false otherwise
 */
function authenticateAdmin(string $username, string $password): bool
{
    try {
        $sql = "SELECT id, username, password_hash FROM admin_users WHERE username = ? LIMIT 1";
        $stmt = executeQuery($sql, [$username]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log('Authentication Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Logout admin user
 * 
 * @return void
 */
function logoutAdmin(): void
{
    $_SESSION = [];
    
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    
    session_destroy();
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCsrfToken(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
