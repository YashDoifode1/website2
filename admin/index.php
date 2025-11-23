<?php
/**
 * Admin Index Page
 * Handles session-based redirection to login or dashboard
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

// Check if user is already logged in via session
if (isAdminLoggedIn()) {
    // If session management is enabled, validate the session
    if (function_exists('validateAdminSession') && isset($_SESSION['admin_session_id'])) {
        if (validateAdminSession()) {
            // Valid session - redirect to dashboard
            header('Location: ' . SITE_URL . '/admin/dashboard.php');
            exit;
        } else {
            // Session expired or invalid - destroy session and redirect to login
            if (function_exists('destroyAdminSession')) {
                destroyAdminSession();
            }
            session_destroy();
            header('Location: ' . SITE_URL . '/admin/login.php?error=session_expired');
            exit;
        }
    } else {
        // Traditional session without session management - redirect to dashboard
        header('Location: ' . SITE_URL . '/admin/dashboard.php');
        exit;
    }
} else {
    // Not logged in - redirect to login page
    header('Location: ' . SITE_URL . '/admin/login.php');
    exit;
}