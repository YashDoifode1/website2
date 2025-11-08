<?php
/**
 * Environment File Manager
 * 
 * Functions to read and update .env file from admin panel
 */

declare(strict_types=1);

/**
 * Update a value in .env file
 * 
 * @param string $key Variable name
 * @param string $value New value
 * @return bool Success status
 */
function updateEnvValue(string $key, string $value): bool
{
    $envPath = __DIR__ . '/../.env';
    
    if (!file_exists($envPath)) {
        return false;
    }
    
    // Read current .env file
    $envContent = file_get_contents($envPath);
    if ($envContent === false) {
        return false;
    }
    
    // Escape special characters in value
    $escapedValue = addslashes($value);
    
    // Check if value needs quotes
    $needsQuotes = (
        strpos($value, ' ') !== false || 
        strpos($value, '#') !== false ||
        strpos($value, '=') !== false ||
        empty($value)
    );
    
    // Format the new value
    if ($needsQuotes) {
        $newValue = '"' . $escapedValue . '"';
    } else {
        $newValue = $value;
    }
    
    // Pattern to match the key
    $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
    
    // Check if key exists
    if (preg_match($pattern, $envContent)) {
        // Update existing key
        $envContent = preg_replace($pattern, $key . '=' . $newValue, $envContent);
    } else {
        // Add new key at the end
        $envContent .= "\n" . $key . '=' . $newValue;
    }
    
    // Write back to file
    $result = file_put_contents($envPath, $envContent);
    
    if ($result !== false) {
        // Update the $_ENV array immediately
        $_ENV[$key] = $value;
        putenv("$key=$value");
        return true;
    }
    
    return false;
}

/**
 * Update multiple .env values at once
 * 
 * @param array $values Associative array of key => value pairs
 * @return array ['success' => count, 'failed' => count, 'errors' => array]
 */
function updateEnvValues(array $values): array
{
    $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => []
    ];
    
    foreach ($values as $key => $value) {
        if (updateEnvValue($key, $value)) {
            $results['success']++;
        } else {
            $results['failed']++;
            $results['errors'][] = $key;
        }
    }
    
    return $results;
}

/**
 * Get all .env values
 * 
 * @return array Associative array of all env variables
 */
function getAllEnvValues(): array
{
    $envPath = __DIR__ . '/../.env';
    
    if (!file_exists($envPath)) {
        return [];
    }
    
    $values = [];
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
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
            
            $values[$key] = $value;
        }
    }
    
    return $values;
}

/**
 * Backup .env file
 * 
 * @return bool Success status
 */
function backupEnvFile(): bool
{
    $envPath = __DIR__ . '/../.env';
    $backupPath = __DIR__ . '/../.env.backup.' . date('Y-m-d-His');
    
    if (!file_exists($envPath)) {
        return false;
    }
    
    return copy($envPath, $backupPath);
}

/**
 * Restore .env from backup
 * 
 * @param string $backupFile Backup filename
 * @return bool Success status
 */
function restoreEnvFile(string $backupFile): bool
{
    $backupPath = __DIR__ . '/../' . $backupFile;
    $envPath = __DIR__ . '/../.env';
    
    if (!file_exists($backupPath)) {
        return false;
    }
    
    return copy($backupPath, $envPath);
}

/**
 * Validate .env file syntax
 * 
 * @return array ['valid' => bool, 'errors' => array]
 */
function validateEnvFile(): array
{
    $envPath = __DIR__ . '/../.env';
    
    $result = [
        'valid' => true,
        'errors' => []
    ];
    
    if (!file_exists($envPath)) {
        $result['valid'] = false;
        $result['errors'][] = '.env file not found';
        return $result;
    }
    
    if (!is_readable($envPath)) {
        $result['valid'] = false;
        $result['errors'][] = '.env file is not readable';
        return $result;
    }
    
    $lines = file($envPath, FILE_IGNORE_NEW_LINES);
    
    foreach ($lines as $lineNum => $line) {
        $line = trim($line);
        
        // Skip empty lines and comments
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        // Check for valid KEY=VALUE format
        if (strpos($line, '=') === false) {
            $result['valid'] = false;
            $result['errors'][] = "Line " . ($lineNum + 1) . ": Invalid format (missing =)";
        }
    }
    
    return $result;
}

/**
 * Get .env file permissions
 * 
 * @return array File permission info
 */
function getEnvFilePermissions(): array
{
    $envPath = __DIR__ . '/../.env';
    
    if (!file_exists($envPath)) {
        return [
            'exists' => false,
            'readable' => false,
            'writable' => false
        ];
    }
    
    return [
        'exists' => true,
        'readable' => is_readable($envPath),
        'writable' => is_writable($envPath),
        'size' => filesize($envPath),
        'modified' => filemtime($envPath)
    ];
}
