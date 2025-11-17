<?php
/**
 * Admin Logout
 * 
 * Logs out the admin user and redirects to login page
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config.php';

logoutAdmin();
redirect(SITE_URL . '/admin/index.php');
