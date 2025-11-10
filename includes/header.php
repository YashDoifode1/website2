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
    <link rel="preload" href="assets/css/style.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    
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
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Conditionally load home page specific styles -->
    <?php if (basename($_SERVER['PHP_SELF']) === 'index.php'): ?>
        <link rel="stylesheet" href="assets/css/home-styles.css">
    <?php endif; ?>
    
    <!-- Conditionally load page specific styles -->
    <?php if (basename($_SERVER['PHP_SELF']) === 'about.php'): ?>
        <link rel="stylesheet" href="assets/css/about-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'services.php'): ?>
        <link rel="stylesheet" href="assets/css/services-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'projects.php'): ?>
        <link rel="stylesheet" href="assets/css/projects-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'packages.php'): ?>
        <link rel="stylesheet" href="assets/css/packages-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'blog.php'): ?>
        <link rel="stylesheet" href="assets/css/blog-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'blog-detail.php'): ?>
        <link rel="stylesheet" href="assets/css/blog-detail-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'testimonials.php'): ?>
        <link rel="stylesheet" href="assets/css/testimonials-styles.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) === 'team.php'): ?>
        <link rel="stylesheet" href="assets/css/team-styles.css">
    <?php endif; ?>
    
    <!-- Global Styles for Fixes -->
    <link rel="stylesheet" href="assets/css/global-styles.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">
    
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
            <a href="index.php" class="nav-logo" aria-label="Grand Jyothi Construction Home">
                <span class="logo-text">
                    <strong>Grand Jyothi Construction</strong>
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
                    <a href="<?= $page ?>" 
                       class="<?= $current_page === $page ? 'active' : '' ?>" 
                       role="menuitem"
                       aria-current="<?= $current_page === $page ? 'page' : 'false' ?>">
                        <?= sanitizeOutput($title) ?>
                    </a>
                </li>
                <?php endforeach; ?>
                
                <!-- Call to Action Button -->
                <li class="nav-cta" role="none">
                    <a href="contact.php" class="btn btn-primary" role="button">
                         Quote
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <main id="main-content">

<style>
/* Header Navigation Styles */

:root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --white: #FFFFFF;
        --light-gray: #f8f9fa;
        --medium-gray: #e9ecef;
    }

    body {
        font-family: 'Roboto', sans-serif;
        color: var(--charcoal);
        background-color: var(--white);
        line-height: 1.6;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
    }
.main-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid #e0e0e0;
    z-index: 1000;
    transition: all 0.3s ease;
}

.main-nav.scrolled {
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

.nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 70px;
}

.nav-logo {
    text-decoration: none;
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.logo-text {
    font-family: 'Montserrat', sans-serif;
}

.logo-text strong {
    color: #333;
    font-weight: 700;
}

/* Navigation Menu */
.nav-menu {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
    align-items: center;
    
}

.nav-menu li {
    margin: 0;
}

.nav-menu a {
    text-decoration: none;
    color: #555;
    font-weight: 500;
    font-family: 'Inter', sans-serif;
    padding: 0.5rem 0;
    position: relative;
    transition: color 0.3s ease;
}

.nav-menu a:hover {
    color: #333;
}

.nav-menu a.active {
    color: #333;
    font-weight: 600;
}

.nav-menu a.active::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    right: 0;
    height: 2px;
    background: #555;
    border-radius: 2px;
}

/* CTA Button */
.nav-cta .btn {
    background: #555;
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 4px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
    display: inline-block;
}

.nav-cta .btn:hover {
    background: #444;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Mobile Menu Toggle */
.nav-toggle {
    display: none;
    flex-direction: column;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    width: 30px;
    height: 30px;
    justify-content: center;
    align-items: center;
}

.hamburger-box {
    width: 20px;
    height: 16px;
    position: relative;
}

.hamburger-inner {
    width: 20px;
    height: 2px;
    background: #333;
    border-radius: 2px;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    transition: all 0.3s ease;
}

.hamburger-inner::before,
.hamburger-inner::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 2px;
    background: #333;
    border-radius: 2px;
    transition: all 0.3s ease;
}

.hamburger-inner::before {
    top: -6px;
}

.hamburger-inner::after {
    top: 6px;
}

/* Mobile Styles */
@media (max-width: 768px) {
    .nav-toggle {
        display: flex;
    }

    .nav-menu {
        position: fixed;
        top: 70px;
        left: 0;
        right: 0;
        background: white;
        flex-direction: column;
        padding: 2rem;
        gap: 0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-100%);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .nav-menu.active {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }

    .nav-menu li {
        width: 100%;
        border-bottom: 1px solid #f0f0f0;
    }

    .nav-menu li:last-child {
        border-bottom: none;
    }

    .nav-menu a {
        display: block;
        padding: 1rem 0;
        width: 100%;
        font-size: 1.1rem;
    }

    .nav-cta {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid #e0e0e0;
    }

    .nav-cta .btn {
        width: 100%;
        text-align: center;
        padding: 1rem;
        font-size: 1.1rem;
    }

    /* Hamburger animation */
    .nav-toggle[aria-expanded="true"] .hamburger-inner {
        background: transparent;
    }

    .nav-toggle[aria-expanded="true"] .hamburger-inner::before {
        transform: rotate(45deg);
        top: 0;
    }

    .nav-toggle[aria-expanded="true"] .hamburger-inner::after {
        transform: rotate(-45deg);
        top: 0;
    }

    /* Prevent body scroll when menu is open */
    body.nav-open {
        overflow: hidden;
    }
}

/* Small mobile devices */
@media (max-width: 480px) {
    .nav-container {
        padding: 0 0.75rem;
    }

    .nav-logo {
        font-size: 1.25rem;
    }

    .nav-menu {
        top: 70px;
        padding: 1.5rem;
    }
}

/* Skip link for accessibility */
.skip-link {
    position: absolute;
    top: -40px;
    left: 6px;
    background: #333;
    color: white;
    padding: 8px;
    text-decoration: none;
    z-index: 1001;
    border-radius: 4px;
    font-weight: 600;
}

.skip-link:focus {
    top: 6px;
}
</style>

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
            if (navMenu) navMenu.classList.remove('active');
            if (navToggle) navToggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('nav-open');
        }
    });
</script>