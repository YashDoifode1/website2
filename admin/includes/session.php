<?php
/**
 * Session Management Functions
 */

declare(strict_types=1);

/**
 * Create a new admin session record
 */
function createAdminSession(int $admin_id, string $ip_address, string $user_agent): bool {
    $db = getDBConnection();
    
    // Generate unique session ID
    $session_id = bin2hex(random_bytes(32));
    
    $stmt = $db->prepare("
        INSERT INTO admin_sessions 
        (admin_id, session_id, ip_address, user_agent, last_activity, created_at) 
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ");
    
    $result = $stmt->execute([$admin_id, $session_id, $ip_address, $user_agent]);
    
    if ($result) {
        // Store session ID in PHP session
        $_SESSION['admin_session_id'] = $session_id;
        return true;
    }
    
    return false;
}

/**
 * Validate and update admin session
 */
function validateAdminSession(): bool {
    if (!isset($_SESSION['admin_session_id'])) {
        return false;
    }
    
    $db = getDBConnection();
    $stmt = $db->prepare("
        SELECT admin_id FROM admin_sessions 
        WHERE session_id = ? AND last_activity > DATE_SUB(NOW(), INTERVAL 2 HOUR)
    ");
    
    $stmt->execute([$_SESSION['admin_session_id']]);
    $session = $stmt->fetch();
    
    if ($session) {
        // Update last activity
        $update_stmt = $db->prepare("
            UPDATE admin_sessions SET last_activity = NOW() 
            WHERE session_id = ?
        ");
        $update_stmt->execute([$_SESSION['admin_session_id']]);
        return true;
    }
    
    return false;
}

/**
 * Destroy admin session
 */
function destroyAdminSession(): bool {
    if (isset($_SESSION['admin_session_id'])) {
        $db = getDBConnection();
        $stmt = $db->prepare("DELETE FROM admin_sessions WHERE session_id = ?");
        $stmt->execute([$_SESSION['admin_session_id']]);
        unset($_SESSION['admin_session_id']);
    }
    
    return true;
}