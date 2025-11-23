<?php
/**
 * Admin Header Template
 * Professional Dashboard with Sidebar Navigation
 * SITE_URL Integrated – No Hardcoded Paths
 */

declare(strict_types=1);

// Load config for SITE_URL
require_once __DIR__ . '/../../config.php'; // Adjust path if needed
// Site configuration

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?>Rakhi Construction - Admin</title>
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>
    
    <!-- Admin Styles -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <a href="<?= SITE_URL ?>/admin/dashboard.php" class="sidebar-logo">
                    <i data-feather="shield"></i>
                    Rakhi Construction
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
                            <i data-feather="home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/services.php" class="<?= $current_page === 'services.php' ? 'active' : '' ?>">
                            <i data-feather="briefcase"></i>
                            <span>Services</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/projects.php" class="<?= $current_page === 'projects.php' ? 'active' : '' ?>">
                            <i data-feather="folder"></i>
                            <span>Projects</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/packages.php" class="<?= $current_page === 'packages.php' ? 'active' : '' ?>">
                            <i data-feather="package"></i>
                            <span>Packages</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/blog.php" class="<?= $current_page === 'blog.php' ? 'active' : '' ?>">
                            <i data-feather="file-text"></i>
                            <span>Blog</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/team.php" class="<?= $current_page === 'team.php' ? 'active' : '' ?>">
                            <i data-feather="users"></i>
                            <span>Team</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/testimonials.php" class="<?= $current_page === 'testimonials.php' ? 'active' : '' ?>">
                            <i data-feather="message-square"></i>
                            <span>Testimonials</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/messages.php" class="<?= $current_page === 'messages.php' ? 'active' : '' ?>">
                            <i data-feather="inbox"></i>
                            <span>Messages</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/mail.php" class="<?= $current_page === 'mail.php' ? 'active' : '' ?>">
                            <i data-feather="send"></i>
                            <span>Email Notifications</span>
                        </a>
                    </li>
                </ul>
                
                <div class="sidebar-divider"></div>
                
                <ul>
                     <li>
                        <a href="<?= SITE_URL ?>/admin/accounts.php" class="<?= $current_page === 'settings.php' ? 'active' : '' ?>">
                            <i data-feather="settings"></i>
                            <span>Account Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/settings.php" class="<?= $current_page === 'settings.php' ? 'active' : '' ?>">
                            <i data-feather="settings"></i>
                            <span>Site Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/env-settings.php" class="<?= $current_page === 'env-settings.php' ? 'active' : '' ?>">
                            <i data-feather="sliders"></i>
                            <span>Environment Settings</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/index.php" target="_blank">
                            <i data-feather="external-link"></i>
                            <span>View Website</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/admin/logout.php">
                            <i data-feather="log-out"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Main Content Area -->
        <div class="admin-main">
            <!-- Top Bar -->
            <header class="admin-topbar">
                <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                    <i data-feather="menu"></i>
                </button>
                
                <h1 class="topbar-title"><?= isset($page_title) ? sanitizeOutput($page_title) : 'Admin Panel' ?></h1>
                
                <div class="topbar-user">
                    <i data-feather="user"></i>
                    <span class="topbar-username"><?= sanitizeOutput($_SESSION['admin_username'] ?? 'Admin') ?></span>
                </div>
            </header>
            
            <!-- Content Area -->
            <main class="admin-content">

    <!-- Initialize Feather Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            feather.replace();
        });

        // Mobile sidebar toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', function() {
                adminSidebar.classList.toggle('active');
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 1024) {
                if (!event.target.closest('.admin-sidebar') && !event.target.closest('.mobile-menu-toggle')) {
                    adminSidebar.classList.remove('active');
                }
            }
        });
    </script>