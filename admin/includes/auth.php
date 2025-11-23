<?php
/**
 * Admin Authentication Helper
 * 
 * Handles admin session validation and access control
 */

declare(strict_types=1);

/**
 * Check if a table exists in the database
 */
function tableExists(string $tableName): bool {
    $db = getDBConnection();
    
    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) as table_exists 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE() AND table_name = ?
        ");
        
        $stmt->execute([$tableName]);
        $result = $stmt->fetch();
        
        return $result && $result['table_exists'] > 0;
    } catch (PDOException $e) {
        error_log("Table check error: " . $e->getMessage());
        return false;
    }
}

/**
 * Change admin password
 */
function changeAdminPassword(int $admin_id, string $current_password, string $new_password): bool {
    $db = getDBConnection();
    
    try {
        // Verify current password
        $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch();
        
        if (!$admin || !password_verify($current_password, $admin['password_hash'])) {
            return false;
        }
        
        // Update to new password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Check if password_changed_at column exists
        $has_password_changed_at = tableHasColumn('admin_users', 'password_changed_at');
        
        if ($has_password_changed_at) {
            $update_stmt = $db->prepare("
                UPDATE admin_users 
                SET password_hash = ?, password_changed_at = NOW() 
                WHERE id = ?
            ");
        } else {
            $update_stmt = $db->prepare("
                UPDATE admin_users 
                SET password_hash = ? 
                WHERE id = ?
            ");
        }
        
        return $update_stmt->execute([$new_password_hash, $admin_id]);
    } catch (PDOException $e) {
        error_log("Password change error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if a specific column exists in a table
 */
function tableHasColumn(string $tableName, string $columnName): bool {
    $db = getDBConnection();
    
    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) as column_exists 
            FROM information_schema.columns 
            WHERE table_schema = DATABASE() 
            AND table_name = ? 
            AND column_name = ?
        ");
        
        $stmt->execute([$tableName, $columnName]);
        $result = $stmt->fetch();
        
        return $result && $result['column_exists'] > 0;
    } catch (PDOException $e) {
        error_log("Column check error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get admin data
 */
function getAdminData(int $admin_id): array {
    $db = getDBConnection();
    
    try {
        // Check which columns exist
        $has_password_changed_at = tableHasColumn('admin_users', 'password_changed_at');
        
        if ($has_password_changed_at) {
            $stmt = $db->prepare("
                SELECT username, created_at, password_changed_at 
                FROM admin_users 
                WHERE id = ?
            ");
        } else {
            $stmt = $db->prepare("
                SELECT username, created_at 
                FROM admin_users 
                WHERE id = ?
            ");
        }
        
        $stmt->execute([$admin_id]);
        $data = $stmt->fetch() ?: [];
        
        // Add default value if password_changed_at doesn't exist
        if (!$has_password_changed_at) {
            $data['password_changed_at'] = 'Never';
        }
        
        return $data;
    } catch (PDOException $e) {
        error_log("Get admin data error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get admin sessions
 */
function getAdminSessions(int $admin_id): array {
    $db = getDBConnection();
    
    if (!tableExists('admin_sessions')) {
        return [];
    }
    
    try {
        $stmt = $db->prepare("
            SELECT session_id, ip_address, user_agent, last_activity, created_at 
            FROM admin_sessions 
            WHERE admin_id = ? 
            ORDER BY last_activity DESC
        ");
        $stmt->execute([$admin_id]);
        return $stmt->fetchAll() ?: [];
    } catch (PDOException $e) {
        error_log("Get admin sessions error: " . $e->getMessage());
        return [];
    }
}

/**
 * Logout all sessions except current
 */
function logoutAllSessions(int $admin_id): bool {
    $db = getDBConnection();
    
    if (!tableExists('admin_sessions') || !isset($_SESSION['admin_session_id'])) {
        return true;
    }
    
    try {
        $stmt = $db->prepare("
            DELETE FROM admin_sessions 
            WHERE admin_id = ? AND session_id != ?
        ");
        return $stmt->execute([$admin_id, $_SESSION['admin_session_id']]);
    } catch (PDOException $e) {
        error_log("Logout all sessions error: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete specific session
 */
function deleteSession(string $session_id): bool {
    $db = getDBConnection();
    
    if (!tableExists('admin_sessions')) {
        return true;
    }
    
    try {
        $stmt = $db->prepare("DELETE FROM admin_sessions WHERE session_id = ?");
        return $stmt->execute([$session_id]);
    } catch (PDOException $e) {
        error_log("Delete session error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if user is logged in as admin
 * 
 * @return bool True if logged in
 */
function isAdmin(): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
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
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return $_SESSION['admin_username'] ?? '';
}

/**
 * Get admin ID
 * 
 * @return int Admin ID or 0
 */
function getAdminId(): int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
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
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
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
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
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
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
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
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['admin_login_time'] = time();
}

/**
 * Check if admin is logged in (with session validation)
 */
function isAdminLoggedIn(): bool {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check basic session
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }
    
    // Check session expiration
    if (isSessionExpired()) {
        logoutAdmin();
        return false;
    }
    
    // If using database sessions, validate them
    if (function_exists('validateAdminSession') && isset($_SESSION['admin_session_id'])) {
        return validateAdminSession();
    }
    
    return true;
}

/**
 * Authenticate admin user and return admin ID or false
 */
function authenticateAdmin(string $username, string $password) {
    $db = getDBConnection();
    
    try {
        // Check if security columns exist
        $has_failed_attempts = tableHasColumn('admin_users', 'failed_attempts');
        $has_locked_until = tableHasColumn('admin_users', 'locked_until');
        
        if ($has_failed_attempts && $has_locked_until) {
            $stmt = $db->prepare("
                SELECT id, username, password_hash, failed_attempts, locked_until 
                FROM admin_users 
                WHERE username = ?
            ");
        } else {
            // Fallback for basic table structure
            $stmt = $db->prepare("
                SELECT id, username, password_hash
                FROM admin_users 
                WHERE username = ?
            ");
        }
        
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if (!$admin) {
            return false;
        }
        
        // Check if account is locked (if column exists)
        if ($has_locked_until && isset($admin['locked_until']) && $admin['locked_until'] && strtotime($admin['locked_until']) > time()) {
            return false;
        }
        
        // Verify password
        if (password_verify($password, $admin['password_hash'])) {
            // Reset failed attempts on successful login (if column exists)
            if ($has_failed_attempts && isset($admin['failed_attempts']) && $admin['failed_attempts'] > 0) {
                $reset_stmt = $db->prepare("
                    UPDATE admin_users 
                    SET failed_attempts = 0, locked_until = NULL 
                    WHERE id = ?
                ");
                $reset_stmt->execute([$admin['id']]);
            }
            
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_login_time'] = time();
            
            // Return admin ID for session creation
            return (int)$admin['id'];
        } else {
            // Handle failed attempt (if security columns exist)
            if ($has_failed_attempts) {
                $new_attempts = ($admin['failed_attempts'] ?? 0) + 1;
                $locked_until = null;
                
                // Lock account after 5 failed attempts for 30 minutes
                if ($new_attempts >= 5) {
                    $locked_until = date('Y-m-d H:i:s', strtotime('+30 minutes'));
                }
                
                $update_stmt = $db->prepare("
                    UPDATE admin_users 
                    SET failed_attempts = ?, locked_until = ? 
                    WHERE id = ?
                ");
                $update_stmt->execute([$new_attempts, $locked_until, $admin['id']]);
            }
            
            return false;
        }
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

/**
 * Redirect to login if not authenticated
 */
function requireAdminAuth(): void {
    if (!isAdminLoggedIn()) {
        header('Location: ' . SITE_URL . '/admin/login.php?error=unauthorized');
        exit;
    }
}

/**
 * Logout admin (comprehensive)
 */
function adminLogout(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Destroy database session if exists
    if (function_exists('destroyAdminSession') && isset($_SESSION['admin_session_id'])) {
        destroyAdminSession();
    }
    
    // Clear all session data
    $_SESSION = [];
    
    // Delete session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Get admin user by ID
 */
function getAdminUserById(int $admin_id): array {
    $db = getDBConnection();
    
    try {
        $stmt = $db->prepare("
            SELECT id, username, created_at 
            FROM admin_users 
            WHERE id = ?
        ");
        $stmt->execute([$admin_id]);
        return $stmt->fetch() ?: [];
    } catch (PDOException $e) {
        error_log("Get admin user error: " . $e->getMessage());
        return [];
    }
}

/**
 * Update admin last activity
 */
function updateAdminLastActivity(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['admin_login_time'] = time();
    
    // Update database session if exists
    if (function_exists('validateAdminSession') && isset($_SESSION['admin_session_id'])) {
        validateAdminSession(); // This updates last_activity
    }
}