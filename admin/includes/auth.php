<?php
/**
 * Admin Authentication Helper
 * 
 * Handles admin session validation and access control
 */

declare(strict_types=1);

/**
 * Check if user is logged in as admin
 * 
 * @return bool True if logged in
 */
function isAdmin(): bool
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin authentication
 * Redirects to login if not authenticated
 * 
 * @return void
 */
function requireAdmin(): void
{
    if (!isAdmin()) {
        header('Location: /constructioninnagpur/admin/index.php');
        exit;
    }
}

/**
 * Get admin username
 * 
 * @return string Admin username or empty string
 */
function getAdminUsername(): string
{
    return $_SESSION['admin_username'] ?? '';
}

/**
 * Get admin ID
 * 
 * @return int Admin ID or 0
 */
function getAdminId(): int
{
    return $_SESSION['admin_id'] ?? 0;
}

/**
 * Login admin user
 * 
 * @param int $id Admin ID
 * @param string $username Admin username
 * @return void
 */
function loginAdmin(int $id, string $username): void
{
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $id;
    $_SESSION['admin_username'] = $username;
    $_SESSION['admin_login_time'] = time();
}

/**
 * Logout admin user
 * 
 * @return void
 */
function logoutAdmin(): void
{
    $_SESSION = [];
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Check if admin session is expired
 * 
 * @param int $timeout Timeout in seconds (default 2 hours)
 * @return bool True if expired
 */
function isSessionExpired(int $timeout = 7200): bool
{
    if (!isset($_SESSION['admin_login_time'])) {
        return true;
    }
    
    return (time() - $_SESSION['admin_login_time']) > $timeout;
}

/**
 * Refresh admin session
 * 
 * @return void
 */
function refreshAdminSession(): void
{
    $_SESSION['admin_login_time'] = time();
}
