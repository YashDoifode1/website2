<?php
/**
 * Common Header Template
 * Modern, Professional Design with Fixed Navigation
 * Updated: Enhanced performance, better mobile experience, improved SEO
 */

declare(strict_types=1);

// Load configuration
require_once __DIR__ . '/../config.php';

// Get current page for active nav highlighting
$current_page = basename($_SERVER['PHP_SELF']);

// Define navigation items in array for better maintainability
$nav_items = [
    'index.php' => 'Home',
    'about.php' => 'About',
    'services.php' => 'Services',
    'projects.php' => 'Projects',
    'packages.php' => 'Packages',
    'blog.php' => 'Blog',
    'team.php' => 'Team',
    'testimonials.php' => 'Testimonials',
    'contact.php' => 'Contact'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Grand Jyothi Construction - Premium construction services in Nagpur. Residential, commercial, and interior construction with quality and trust.">
    <meta name="keywords" content="construction company nagpur, builders nagpur, residential construction, commercial construction, interior design nagpur, construction services">
    <meta name="author" content="Grand Jyothi Construction">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph Meta Tags for Social Sharing -->
    <meta property="og:title" content="<?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?>Grand Jyothi Construction">
    <meta property="og:description" content="Premium construction services in Nagpur. Building your vision with excellence and trust.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= sanitizeOutput((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?= sanitizeOutput((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
    
    <title><?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?>Grand Jyothi Construction | Nagpur</title>
    
    <!-- Preload Critical Resources -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="/constructioninnagpur/assets/css/style.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
    <!-- DNS Prefetch for external domains -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://unpkg.com">
    
    <!-- Google Fonts - Montserrat and Inter with fallback -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap">
    </noscript>
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="/constructioninnagpur/assets/css/style.css">
    
    <!-- Conditionally load home page specific styles -->
    <?php if (basename($_SERVER['PHP_SELF']) === 'index.php'): ?>
        <link rel="stylesheet" href="/constructioninnagpur/assets/css/home-styles.css">
    <?php endif; ?>
    
    <!-- Conditionally load page specific styles -->
    <?php if (basename($_SERVER['PHP_SELF']) === 'about.php'): ?>
        <link rel="stylesheet" href="/constructioninnagpur/assets/css/about-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'services.php'): ?>
        <link rel="stylesheet" href="/constructioninnagpur/assets/css/services-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'projects.php'): ?>
        <link rel="stylesheet" href="/constructioninnagpur/assets/css/projects-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'packages.php'): ?>
        <link rel="stylesheet" href="/constructioninnagpur/assets/css/packages-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'blog.php'): ?>
        <link rel="stylesheet" href="/constructioninnagpur/assets/css/blog-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'blog-detail.php'): ?>
        <link rel="stylesheet" href="/constructioninnagpur/assets/css/blog-detail-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'testimonials.php'): ?>
        <link rel="stylesheet" href="/constructioninnagpur/assets/css/testimonials-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'team.php'): ?>
        <link rel="stylesheet" href="/constructioninnagpur/assets/css/team-styles.css">
    <?php endif; ?>
    
    <!-- Global Styles for Fixes -->
    <link rel="stylesheet" href="/constructioninnagpur/assets/css/global-styles.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/constructioninnagpur/assets/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/constructioninnagpur/assets/images/apple-touch-icon.png">
    
    <!-- Feather Icons - Deferred for better performance -->
    <script src="https://unpkg.com/feather-icons" defer></script>
    
    <!-- Structured Data for Local Business -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ConstructionBusiness",
        "name": "Grand Jyothi Construction",
        "description": "Premium construction services in Nagpur",
        "url": "<?= sanitizeOutput((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST']) ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Nagpur",
            "addressRegion": "Maharashtra",
            "addressCountry": "IN"
        },
        "openingHours": "Mo-Sa 09:00-18:00"
    }
    </script>
</head>
<body>
    <!-- Skip to main content for accessibility -->
    <a class="skip-link" href="#main-content">Skip to main content</a>
    
    <!-- Fixed Navigation -->
    <nav class="main-nav" id="mainNav" aria-label="Main Navigation">
        <div class="nav-container">
            <!-- Logo Section -->
            <a href="/constructioninnagpur/index.php" class="nav-logo" aria-label="Grand Jyothi Construction Home">
                <?php if (defined('SHOW_LOGO_ICON') && SHOW_LOGO_ICON): ?>
                <i data-feather="home" aria-hidden="true"></i>
                <?php endif; ?>
                <span class="logo-text">
                    <strong><?= sanitizeOutput(defined('SITE_LOGO_TEXT') ? SITE_LOGO_TEXT : 'Grand Jyothi') ?></strong>
                    <span class="logo-subtitle"><?= sanitizeOutput(defined('SITE_LOGO_SUBTITLE') ? SITE_LOGO_SUBTITLE : '') ?></span>
                </span>
            </a>
            
            <!-- Mobile Menu Toggle -->
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="navMenu">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
            
            <!-- Main Navigation Menu -->
            <ul class="nav-menu" id="navMenu" role="menubar">
                <?php foreach ($nav_items as $page => $title): ?>
                <li role="none">
                    <a href="/constructioninnagpur/<?= $page ?>" 
                       class="<?= $current_page === $page ? 'active' : '' ?>" 
                       role="menuitem"
                       aria-current="<?= $current_page === $page ? 'page' : 'false' ?>">
                        <?= sanitizeOutput($title) ?>
                    </a>
                </li>
                <?php endforeach; ?>
                
                <!-- Call to Action Button -->
                <li class="nav-cta" role="none">
                    <a href="/constructioninnagpur/contact.php" class="btn btn-primary" role="button">
                        <i data-feather="phone" aria-hidden="true"></i>
                         Quote
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <br><br><br>
    <!-- Main Content Wrapper -->
    <main id="main-content">
    
    <script>
        // Enhanced mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const navToggle = document.getElementById('navToggle');
            const navMenu = document.getElementById('navMenu');
            const mainNav = document.getElementById('mainNav');
            
            // Mobile menu toggle with accessibility
            if (navToggle && navMenu) {
                navToggle.addEventListener('click', function() {
                    const isExpanded = this.getAttribute('aria-expanded') === 'true';
                    this.setAttribute('aria-expanded', !isExpanded);
                    navMenu.classList.toggle('active');
                    document.body.classList.toggle('nav-open');
                });
            }
            
            // Add shadow on scroll with performance optimization
            let scrollTimer;
            window.addEventListener('scroll', function() {
                if (scrollTimer) {
                    clearTimeout(scrollTimer);
                }
                
                scrollTimer = setTimeout(function() {
                    if (window.scrollY > 10) {
                        mainNav.classList.add('scrolled');
                    } else {
                        mainNav.classList.remove('scrolled');
                    }
                }, 10);
            }, { passive: true });
            
            // Close mobile menu when clicking outside or pressing Escape
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.main-nav') && navMenu.classList.contains('active')) {
                    closeMobileMenu();
                }
            });
            
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && navMenu.classList.contains('active')) {
                    closeMobileMenu();
                }
            });
            
            // Close mobile menu when clicking on a link
            const navLinks = document.querySelectorAll('.nav-menu a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeMobileMenu();
                    }
                });
            });
            
            function closeMobileMenu() {
                navMenu.classList.remove('active');
                navToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('nav-open');
            }
            
            // Update navigation active state on scroll for single page apps or long pages
            function updateActiveNavOnScroll() {
                const sections = document.querySelectorAll('section[id]');
                const scrollPos = window.scrollY + 100;
                
                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    const sectionId = section.getAttribute('id');
                    
                    if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                        const currentActive = document.querySelector('.nav-menu a.active');
                        if (currentActive) {
                            currentActive.classList.remove('active');
                            currentActive.removeAttribute('aria-current');
                        }
                        
                        const newActive = document.querySelector(`.nav-menu a[href*="${sectionId}"]`);
                        if (newActive) {
                            newActive.classList.add('active');
                            newActive.setAttribute('aria-current', 'page');
                        }
                    }
                });
            }
            
            // Only run if it's a single page with sections
            if (window.location.pathname === '/constructioninnagpur/index.php') {
                window.addEventListener('scroll', updateActiveNavOnScroll, { passive: true });
            }
        });
        
        // Feather icons replacement after page load
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script>