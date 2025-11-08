<?php
/**
 * Common Header Template
 * Modern, Professional Design with Fixed Navigation
 */

declare(strict_types=1);

// Load configuration
require_once __DIR__ . '/../config.php';

// Get current page for active nav highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Grand Jyothi Construction - Building your vision with excellence and trust in Nagpur">
    <meta name="keywords" content="construction, nagpur, residential, commercial, interior design">
    <meta name="author" content="Grand Jyothi Construction">
    
    <title><?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?>Grand Jyothi Construction</title>
    
    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="/constructioninnagpur/assets/css/style.css">
    
    <!-- Feather Icons - Deferred for better performance -->
    <script src="https://unpkg.com/feather-icons" defer></script>
</head>
<body>
    <!-- Fixed Navigation -->
    <nav class="main-nav" id="mainNav">
        <div class="nav-container">
            <a href="/constructioninnagpur/index.php" class="nav-logo">
                <?php if (defined('SHOW_LOGO_ICON') && SHOW_LOGO_ICON): ?>
                <i data-feather="home"></i>
                <?php endif; ?>
                <span><?= sanitizeOutput(defined('SITE_LOGO_TEXT') ? SITE_LOGO_TEXT : 'Grand Jyothi') ?></span> <?= sanitizeOutput(defined('SITE_LOGO_SUBTITLE') ? SITE_LOGO_SUBTITLE : 'Construction') ?>
            </a>
            
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <i data-feather="menu"></i>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                <li class="<?= $current_page === 'index.php' ? 'active' : '' ?>">
                    <a href="/constructioninnagpur/index.php">Home</a>
                </li>
                <li class="<?= $current_page === 'about.php' ? 'active' : '' ?>">
                    <a href="/constructioninnagpur/about.php">About</a>
                </li>
                <li class="<?= $current_page === 'services.php' ? 'active' : '' ?>">
                    <a href="/constructioninnagpur/services.php">Services</a>
                </li>
                <li class="<?= $current_page === 'projects.php' ? 'active' : '' ?>">
                    <a href="/constructioninnagpur/projects.php">Projects</a>
                </li>
                <li class="<?= $current_page === 'packages.php' ? 'active' : '' ?>">
                    <a href="/constructioninnagpur/packages.php">Packages</a>
                </li>
                <li class="<?= $current_page === 'blog.php' ? 'active' : '' ?>">
                    <a href="/constructioninnagpur/blog.php">Blog</a>
                </li>
                <li class="<?= $current_page === 'team.php' ? 'active' : '' ?>">
                    <a href="/constructioninnagpur/team.php">Our Team</a>
                </li>
                <li class="<?= $current_page === 'testimonials.php' ? 'active' : '' ?>">
                    <a href="/constructioninnagpur/testimonials.php">Testimonials</a>
                </li>
                <li class="<?= $current_page === 'contact.php' ? 'active' : '' ?>">
                    <a href="/constructioninnagpur/contact.php">Contact</a>
                </li>
            </ul>
        </div>
    </nav>
    
    <script>
        // Mobile menu toggle
        const navToggle = document.getElementById('navToggle');
        const navMenu = document.getElementById('navMenu');
        
        if (navToggle) {
            navToggle.addEventListener('click', function() {
                navMenu.classList.toggle('active');
            });
        }
        
        // Add shadow on scroll
        const mainNav = document.getElementById('mainNav');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 10) {
                mainNav.classList.add('scrolled');
            } else {
                mainNav.classList.remove('scrolled');
            }
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.main-nav')) {
                navMenu.classList.remove('active');
            }
        });
    </script>
