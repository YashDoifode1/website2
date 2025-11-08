<?php
/**
 * Configuration File
 * 
 * Site-wide configuration settings
 */

declare(strict_types=1);

// Load environment variables
require_once __DIR__ . '/includes/env.php';

// Site Information
define('SITE_NAME', env('SITE_NAME', 'Grand Jyothi Construction'));
define('SITE_TAGLINE', env('SITE_TAGLINE', 'Building your vision with excellence and trust'));
define('SITE_URL', env('APP_URL', 'http://localhost/constructioninnagpur'));

// Contact Information
define('CONTACT_EMAIL', env('CONTACT_EMAIL', 'info@grandjyothi.com'));
define('CONTACT_PHONE', env('CONTACT_PHONE', '+91 98765 43210'));
define('CONTACT_ADDRESS', env('CONTACT_ADDRESS', '123 Construction Plaza, Dharampeth, Nagpur - 440010, Maharashtra, India'));

// Social Media Links (optional)
define('FACEBOOK_URL', env('FACEBOOK_URL', 'https://facebook.com/grandjyothi'));
define('TWITTER_URL', env('TWITTER_URL', 'https://twitter.com/grandjyothi'));
define('INSTAGRAM_URL', env('INSTAGRAM_URL', 'https://instagram.com/grandjyothi'));
define('LINKEDIN_URL', env('LINKEDIN_URL', 'https://linkedin.com/company/grandjyothi'));

// Business Hours
define('BUSINESS_HOURS', [
    'Monday - Friday' => env('BUSINESS_HOURS_WEEKDAY', '9:00 AM - 6:00 PM'),
    'Saturday' => env('BUSINESS_HOURS_SATURDAY', '9:00 AM - 2:00 PM'),
    'Sunday' => env('BUSINESS_HOURS_SUNDAY', 'Closed')
]);

// Header/Logo Settings
define('SITE_LOGO_TEXT', env('SITE_LOGO_TEXT', 'Grand Jyothi'));
define('SITE_LOGO_SUBTITLE', env('SITE_LOGO_SUBTITLE', 'Construction'));
define('SHOW_LOGO_ICON', env('SHOW_LOGO_ICON', true));

// SEO Settings
define('META_DESCRIPTION', env('SITE_DESCRIPTION', 'Grand Jyothi Construction - Leading construction company in Nagpur offering residential, commercial, and industrial construction services.'));
define('META_KEYWORDS', env('SITE_KEYWORDS', 'construction, nagpur, residential, commercial, industrial, interior design, renovation'));

// Pagination Settings
define('ITEMS_PER_PAGE', env('ITEMS_PER_PAGE', 12));

// Upload Settings
define('MAX_UPLOAD_SIZE', env('MAX_UPLOAD_SIZE', 5 * 1024 * 1024)); // 5MB
$allowedTypes = env('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,gif,webp');
define('ALLOWED_IMAGE_TYPES', explode(',', $allowedTypes));

// Date Format
define('DATE_FORMAT', 'F d, Y');
define('DATETIME_FORMAT', 'F d, Y \a\t H:i');

// Timezone
date_default_timezone_set(env('TIMEZONE', 'Asia/Kolkata'));

// Error Reporting (set to 0 in production)
$isDebug = env('APP_DEBUG', true);
error_reporting($isDebug ? E_ALL : 0);
ini_set('display_errors', $isDebug ? '1' : '0');

// Session Configuration (only if session not already started)
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_samesite', 'Strict');
}
