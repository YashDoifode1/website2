<?php
/**
 * Environment Configuration Loader
 * 
 * Simple .env file parser without external dependencies
 */

declare(strict_types=1);

/**
 * Load environment variables from .env file
 * 
 * @param string $path Path to .env file
 * @return bool True if loaded successfully
 */
function loadEnv(string $path): bool
{
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes from value
            $value = trim($value, '"\'');
            
            // Set environment variable
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
    
    return true;
}

/**
 * Get environment variable value
 * 
 * @param string $key Variable name
 * @param mixed $default Default value if not found
 * @return mixed Variable value or default
 */
function env(string $key, $default = null)
{
    // Check $_ENV first
    if (isset($_ENV[$key])) {
        return parseEnvValue($_ENV[$key]);
    }
    
    // Check getenv()
    $value = getenv($key);
    if ($value !== false) {
        return parseEnvValue($value);
    }
    
    return $default;
}

/**
 * Parse environment variable value
 * 
 * @param string $value Raw value
 * @return mixed Parsed value
 */
function parseEnvValue(string $value)
{
    // Handle boolean values
    $lower = strtolower($value);
    if ($lower === 'true' || $lower === '(true)') {
        return true;
    }
    if ($lower === 'false' || $lower === '(false)') {
        return false;
    }
    
    // Handle null
    if ($lower === 'null' || $lower === '(null)') {
        return null;
    }
    
    // Handle empty
    if ($lower === 'empty' || $lower === '(empty)') {
        return '';
    }
    
    // Handle numeric values
    if (is_numeric($value)) {
        return strpos($value, '.') !== false ? (float)$value : (int)$value;
    }
    
    return $value;
}

/**
 * Check if environment variable exists
 * 
 * @param string $key Variable name
 * @return bool True if exists
 */
function hasEnv(string $key): bool
{
    return isset($_ENV[$key]) || getenv($key) !== false;
}

// Load .env file automatically
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    loadEnv($envPath);
}
