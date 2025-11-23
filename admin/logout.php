<?php
/**
 * Admin Logout
 * 
 * Logs out the admin user and redirects to login page
 * Now with comprehensive session management and logging
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

// Get admin info before logout for logging/messages
$admin_id = $_SESSION['admin_id'] ?? 0;
$username = $_SESSION['admin_username'] ?? 'Admin';
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

// Log the logout action (optional - if you have logging functionality)
logAdminAction($admin_id, 'logout', "User {$username} logged out from IP: {$ip_address}");

// Perform comprehensive logout
adminLogout();

// Redirect to login page with success message
header('Location: ' . SITE_URL . '/admin/login.php?logout=success&user=' . urlencode($username));
exit;

/**
 * Log admin actions (optional helper function)
 */
function logAdminAction(int $admin_id, string $action, string $description): void {
    // If you have an admin_logs table, you can implement this
    // This is optional but recommended for security auditing
    
    /*
    $db = getDBConnection();
    try {
        $stmt = $db->prepare("
            INSERT INTO admin_logs (admin_id, action, description, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $admin_id, 
            $action, 
            $description,
            $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
        ]);
    } catch (PDOException $e) {
        // Silent fail - don't break logout if logging fails
        error_log("Admin log error: " . $e->getMessage());
    }
    */
}