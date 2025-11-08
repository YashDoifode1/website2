<?php
/**
 * Security Helper Functions
 * 
 * Provides CSRF protection, rate limiting, and other security features
 */

declare(strict_types=1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function generateCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * 
 * @param string|null $token Token to validate
 * @return bool True if valid, false otherwise
 */
function validateCsrfToken(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF token input field HTML
 * 
 * @return string HTML input field
 */
function getCsrfTokenField(): string
{
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Rate limiting check
 * 
 * @param string $action Action identifier (e.g., 'contact_form', 'login')
 * @param int $maxAttempts Maximum attempts allowed
 * @param int $timeWindow Time window in seconds
 * @return bool True if allowed, false if rate limited
 */
function checkRateLimit(string $action, int $maxAttempts = 5, int $timeWindow = 300): bool
{
    $identifier = $_SERVER['REMOTE_ADDR'] . '_' . $action;
    $key = 'rate_limit_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'attempts' => 1,
            'first_attempt' => time()
        ];
        return true;
    }
    
    $data = $_SESSION[$key];
    $timeElapsed = time() - $data['first_attempt'];
    
    // Reset if time window has passed
    if ($timeElapsed > $timeWindow) {
        $_SESSION[$key] = [
            'attempts' => 1,
            'first_attempt' => time()
        ];
        return true;
    }
    
    // Check if limit exceeded
    if ($data['attempts'] >= $maxAttempts) {
        return false;
    }
    
    // Increment attempts
    $_SESSION[$key]['attempts']++;
    return true;
}

/**
 * Get remaining time for rate limit
 * 
 * @param string $action Action identifier
 * @param int $timeWindow Time window in seconds
 * @return int Remaining seconds
 */
function getRateLimitRemaining(string $action, int $timeWindow = 300): int
{
    $identifier = $_SERVER['REMOTE_ADDR'] . '_' . $action;
    $key = 'rate_limit_' . md5($identifier);
    
    if (!isset($_SESSION[$key])) {
        return 0;
    }
    
    $data = $_SESSION[$key];
    $timeElapsed = time() - $data['first_attempt'];
    $remaining = $timeWindow - $timeElapsed;
    
    return max(0, $remaining);
}

/**
 * Sanitize input data
 * 
 * @param mixed $data Data to sanitize
 * @return mixed Sanitized data
 */
function sanitizeInput($data)
{
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    if (is_string($data)) {
        // Remove null bytes
        $data = str_replace("\0", '', $data);
        // Trim whitespace
        $data = trim($data);
        // Remove control characters except newlines and tabs
        $data = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $data);
    }
    
    return $data;
}

/**
 * Validate and sanitize file upload
 * 
 * @param array $file File from $_FILES
 * @param array $allowedTypes Allowed MIME types
 * @param int $maxSize Maximum file size in bytes
 * @return array ['success' => bool, 'message' => string, 'file' => array|null]
 */
function validateFileUpload(array $file, array $allowedTypes, int $maxSize): array
{
    // Check if file was uploaded
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload', 'file' => null];
    }
    
    // Check for upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'message' => 'File size exceeds limit', 'file' => null];
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'message' => 'No file uploaded', 'file' => null];
        default:
            return ['success' => false, 'message' => 'Upload error occurred', 'file' => null];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size exceeds maximum allowed', 'file' => null];
    }
    
    // Verify MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type', 'file' => null];
    }
    
    // Additional security: Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/gif' => ['gif'],
        'image/webp' => ['webp'],
        'application/pdf' => ['pdf']
    ];
    
    if (isset($allowedExtensions[$mimeType]) && !in_array($extension, $allowedExtensions[$mimeType])) {
        return ['success' => false, 'message' => 'File extension does not match type', 'file' => null];
    }
    
    return ['success' => true, 'message' => 'File is valid', 'file' => $file];
}

/**
 * Generate secure random filename
 * 
 * @param string $originalName Original filename
 * @return string Secure filename
 */
function generateSecureFilename(string $originalName): string
{
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $randomName = bin2hex(random_bytes(16));
    return $randomName . '.' . $extension;
}

/**
 * Prevent clickjacking by setting X-Frame-Options header
 * 
 * @param string $option Option value (DENY, SAMEORIGIN, or ALLOW-FROM uri)
 * @return void
 */
function preventClickjacking(string $option = 'SAMEORIGIN'): void
{
    header("X-Frame-Options: $option");
}

/**
 * Set Content Security Policy header
 * 
 * @param string $policy CSP policy string
 * @return void
 */
function setContentSecurityPolicy(string $policy = null): void
{
    if ($policy === null) {
        $policy = "default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self';";
    }
    header("Content-Security-Policy: $policy");
}

/**
 * Check if request is HTTPS
 * 
 * @return bool True if HTTPS, false otherwise
 */
function isHttps(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
}

/**
 * Enforce HTTPS redirect
 * 
 * @return void
 */
function enforceHttps(): void
{
    if (!isHttps()) {
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('Location: ' . $redirect, true, 301);
        exit;
    }
}

/**
 * Validate password strength
 * 
 * @param string $password Password to validate
 * @param int $minLength Minimum length
 * @return array ['valid' => bool, 'message' => string]
 */
function validatePasswordStrength(string $password, int $minLength = 8): array
{
    if (strlen($password) < $minLength) {
        return ['valid' => false, 'message' => "Password must be at least $minLength characters long"];
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one uppercase letter'];
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one lowercase letter'];
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one number'];
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one special character'];
    }
    
    return ['valid' => true, 'message' => 'Password is strong'];
}

/**
 * Hash password securely
 * 
 * @param string $password Password to hash
 * @return string Hashed password
 */
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_ARGON2ID);
}

/**
 * Verify password against hash
 * 
 * @param string $password Password to verify
 * @param string $hash Hash to verify against
 * @return bool True if valid, false otherwise
 */
function verifyPassword(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * Log security event
 * 
 * @param string $event Event description
 * @param string $level Severity level (INFO, WARNING, ERROR)
 * @return void
 */
function logSecurityEvent(string $event, string $level = 'INFO'): void
{
    $logFile = __DIR__ . '/../logs/security.log';
    $logDir = dirname($logFile);
    
    // Create logs directory if it doesn't exist
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
    
    $logEntry = sprintf(
        "[%s] [%s] IP: %s | Event: %s | User-Agent: %s\n",
        $timestamp,
        $level,
        $ip,
        $event,
        $userAgent
    );
    
    error_log($logEntry, 3, $logFile);
}
