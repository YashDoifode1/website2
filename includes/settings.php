<?php
/**
 * Site Settings Helper Functions
 * 
 * Functions to retrieve and manage site settings
 */

declare(strict_types=1);

// Include database connection if not already included
if (!function_exists('executeQuery')) {
    require_once __DIR__ . '/db.php';
}

/**
 * Get a site setting value
 * 
 * @param string $key Setting key
 * @param mixed $default Default value if setting not found
 * @return mixed Setting value or default
 */
function getSetting(string $key, $default = '') {
    try {
        $stmt = executeQuery("SELECT setting_value FROM site_settings WHERE setting_key = ?", [$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? $result['setting_value'] : $default;
    } catch (PDOException $e) {
        error_log('Get Setting Error: ' . $e->getMessage());
        return $default;
    } catch (Exception $e) {
        error_log('Get Setting Error: ' . $e->getMessage());
        return $default;
    }
}

/**
 * Get all site settings as an associative array
 * 
 * @return array All settings as key => value pairs
 */
function getAllSettings(): array {
    try {
        $stmt = executeQuery("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    } catch (PDOException $e) {
        error_log('Get All Settings Error: ' . $e->getMessage());
        return [];
    } catch (Exception $e) {
        error_log('Get All Settings Error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Update a site setting
 * 
 * @param string $key Setting key
 * @param mixed $value Setting value
 * @param string $type Setting type (text, textarea, email, number, etc.)
 * @return bool Success status
 */
function updateSetting(string $key, $value, string $type = 'text'): bool {
    try {
        $stmt = executeQuery("
            INSERT INTO site_settings (setting_key, setting_value, setting_type) 
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE setting_value = ?, setting_type = ?
        ", [$key, $value, $type, $value, $type]);
        
        return true;
    } catch (PDOException $e) {
        error_log('Update Setting Error: ' . $e->getMessage());
        return false;
    } catch (Exception $e) {
        error_log('Update Setting Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get contact information as an array
 * 
 * @return array Contact information
 */
function getContactInfo(): array {
    return [
        'email' => getSetting('contact_email', 'info@grandjyothi.com'),
        'phone' => getSetting('contact_phone', '+91 98765 43210'),
        'address' => getSetting('contact_address', 'Nagpur, Maharashtra, India'),
    ];
}

/**
 * Get social media links as an array
 * 
 * @return array Social media URLs
 */
function getSocialLinks(): array {
    return [
        'facebook' => getSetting('facebook_url', ''),
        'twitter' => getSetting('twitter_url', ''),
        'instagram' => getSetting('instagram_url', ''),
        'linkedin' => getSetting('linkedin_url', ''),
    ];
}
