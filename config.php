<?php
/**
 * Configuration File
 * 
 * Site-wide configuration settings
 */

declare(strict_types=1);

// Load environment variables
require_once __DIR__ . '/includes/env.php';

// ====================== SITE CONFIGURATION ======================

// Core Site Information
define('SITE_NAME', env('SITE_NAME', 'Rakhi Construction & Consultancy Pvt Ltd'));
define('SITE_TAGLINE', env('SITE_TAGLINE', 'Building your vision with excellence and trust'));
define('SITE_URL', env('APP_URL', 'https://rakhiconstruction.com'));
define('SITE_DESCRIPTION', env('SITE_DESCRIPTION', 'Leading construction and consultancy firm in Nagpur offering end-to-end construction solutions.'));
define('SITE_KEYWORDS', env('SITE_KEYWORDS', 'construction, nagpur, consultancy, residential, commercial, industrial, construction services'));

// Google reCAPTCHA
define('RECAPTCHA_SITE_KEY', env('RECAPTCHA_SITE_KEY', 'your-site-key-here'));
define('RECAPTCHA_SECRET_KEY', env('RECAPTCHA_SECRET_KEY', 'your-secret-key-here'));

// ====================== CONTACT INFORMATION ======================

// Primary Contact
define('CONTACT_EMAIL', env('CONTACT_EMAIL', 'info@rakhiconstruction.com'));
define('CONTACT_PHONE', env('CONTACT_PHONE', '+91 90759 56483'));
define('CONTACT_ADDRESS', env('CONTACT_ADDRESS', 'PL NO 55, CHAKRADHAR HO NEAR NAGAR PANCHAYAT BAHADURA ROAD, Nagpur, MAHARASHTRA - 440034'));

// Contact Numbers
define('WHATSAPP_NUMBER', env('WHATSAPP_NUMBER', '+919075956483'));
define('PHONE_NUMBER', env('PHONE_NUMBER', '+919075956483'));
define('SECONDARY_PHONE', env('SECONDARY_PHONE', '+919112841057'));

// ====================== BUSINESS HOURS ======================

define('BUSINESS_HOURS', [
    'Monday - Friday' => env('BUSINESS_HOURS_WEEKDAY', '9:00 AM - 6:00 PM'),
    'Saturday' => env('BUSINESS_HOURS_SATURDAY', '9:00 AM - 2:00 PM'),
    'Sunday' => env('BUSINESS_HOURS_SUNDAY', 'Closed')
]);

// ====================== SOCIAL MEDIA ======================

define('SOCIAL_MEDIA', [
    'facebook' => [
        'url' => env('FACEBOOK_URL', 'https://facebook.com/rakhiconstruction'),
        'icon' => 'fab fa-facebook-f',
        'name' => 'Facebook'
    ],
    'twitter' => [
        'url' => env('TWITTER_URL', 'https://twitter.com/grandjyothi'),
        'icon' => 'fab fa-twitter',
        'name' => 'Twitter'
    ],
    'instagram' => [
        'url' => env('INSTAGRAM_URL', 'https://instagram.com/grandjyothi'),
        'icon' => 'fab fa-instagram',
        'name' => 'Instagram'
    ],
    'linkedin' => [
        'url' => env('LINKEDIN_URL', 'https://linkedin.com/company/grandjyothi'),
        'icon' => 'fab fa-linkedin-in',
        'name' => 'LinkedIn'
    ],
    'youtube' => [
        'url' => env('YOUTUBE_URL', 'https://youtube.com/grandjyothi'),
        'icon' => 'fab fa-youtube',
        'name' => 'YouTube'
    ],
    'pinterest' => [
        'url' => env('PINTEREST_URL', 'https://pinterest.com/grandjyothi'),
        'icon' => 'fab fa-pinterest-p',
        'name' => 'Pinterest'
    ]
]);

// ====================== SITE SETTINGS ======================

define('SITE_SETTINGS', [
    'logo' => [
        'text' => env('SITE_LOGO_TEXT', 'Grand Jyothi'),
        'subtitle' => env('SITE_LOGO_SUBTITLE', ''),
        'show_icon' => env('SHOW_LOGO_ICON', true) === 'true' || env('SHOW_LOGO_ICON', true) === true
    ],
    'header' => [
        'sticky' => env('HEADER_STICKY', true) === 'true' || env('HEADER_STICKY', true) === true,
        'transparent' => env('HEADER_TRANSPARENT', false) === 'true' || env('HEADER_TRANSPARENT', false) === true
    ],
    'footer' => [
        'show_newsletter' => env('FOOTER_SHOW_NEWSLETTER', true) === 'true' || env('FOOTER_SHOW_NEWSLETTER', true) === true,
        'copyright' => env('FOOTER_COPYRIGHT', '© ' . date('Y') . ' Grand Jyothi Construction. All Rights Reserved.')
    ]
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
