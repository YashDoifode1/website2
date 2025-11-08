<?php
/**
 * Database Connection using PDO
 * 
 * This file establishes a secure database connection using PDO
 * with proper error handling and UTF-8 encoding.
 */

declare(strict_types=1);

// Load environment variables
require_once __DIR__ . '/env.php';

// Database configuration
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_NAME', env('DB_NAME', 'constructioninnagpur'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

/**
 * Get database connection
 * 
 * @return PDO Database connection object
 * @throws PDOException If connection fails
 */
function getDbConnection(): PDO
{
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_NAME,
            DB_CHARSET
        );
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_PERSISTENT         => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database Connection Error: ' . $e->getMessage());
            die('Database connection failed. Please try again later.');
        }
    }
    
    return $pdo;
}

/**
 * Execute a prepared statement and return results
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return PDOStatement Executed statement
 */
function executeQuery(string $sql, array $params = []): PDOStatement
{
    $pdo = getDbConnection();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Sanitize output to prevent XSS attacks
 * 
 * @param mixed $data Data to sanitize
 * @return string Sanitized data
 */
function sanitizeOutput($data): string
{
    // Convert to string if not already
    if ($data === null) {
        return '';
    }
    
    if (is_array($data) || is_object($data)) {
        return htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');
    }
    
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 * 
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 * @return void
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}
