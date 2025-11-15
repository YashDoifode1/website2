<?php
/**
 * includes/header.php
 * Modern, Fully Responsive Header – Mobile + Desktop
 */

declare(strict_types=1);

// Fallback sanitize function
if (!function_exists('sanitizeOutput')) {
    function sanitizeOutput($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

// Get current page
$current_page = basename($_SERVER['PHP_SELF']);

// Navigation items
$nav_items = [
    'index.php'       => 'Home',
    'about.php'       => 'About',
    'services.php'    => 'Services',
    'projects.php'    => 'Projects',
    'packages.php'    => 'Packages',
    'blog.php'        => 'Blog',
    'team.php'        => 'Team',
    'testimonials.php'=> 'Testimonials',
    'contact.php'     => 'Contact'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Grand Jyothi Construction - Premium construction services in Nagpur.">
    <meta name="keywords" content="construction nagpur, builders nagpur, residential, commercial, interior design">
    <meta name="author" content="Grand Jyothi Construction">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?>Grand Jyothi Construction">
    <meta property="og:description" content="Premium construction services in Nagpur.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= sanitizeOutput('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">

    <!-- Canonical -->
    <link rel="canonical" href="<?= sanitizeOutput('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">

    <title><?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?>Grand Jyothi Construction | Nagpur</title>

    <!-- Favicon -->
    <link rel="icon" href="/constructioninnagpur/assets/images/favicon.ico" type="image/x-icon">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- Main Styles (optional: move to style.css) -->
    <link rel="stylesheet" href="/constructioninnagpur/assets/css/style.css">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ConstructionBusiness",
        "name": "Grand Jyothi Construction",
        "url": "https://<?= $_SERVER['HTTP_HOST'] ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Nagpur",
            "addressRegion": "MH",
            "addressCountry": "IN"
        }
    }
    </script>
</head>
<body>

<!-- Skip Link -->
<a class="skip-link" href="#main-content" tabindex="0">Skip to main content</a>

<!-- Fixed Navigation -->
<nav class="main-nav" id="mainNav" aria-label="Main Navigation">
    <div class="nav-container">
        <!-- Logo -->
        <a href="/constructioninnagpur/index.php" class="nav-logo" aria-label="Grand Jyothi Construction - Home">
            <strong>Grand Jyothi</strong>
        </a>

        <!-- Mobile Toggle Button -->
        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="navMenu">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </button>

        <!-- Navigation Menu -->
        <ul class="nav-menu" id="navMenu" role="menubar">
            <?php foreach ($nav_items as $page => $title): ?>
                <li role="none">
                    <a href="/constructioninnagpur/<?= $page ?>"
                       class="nav-link <?= $current_page === $page ? 'active' : '' ?>"
                       role="menuitem"
                       aria-current="<?= $current_page === $page ? 'page' : 'false' ?>">
                        <?= sanitizeOutput($title) ?>
                    </a>
                </li>
            <?php endforeach; ?>
            <li class="nav-cta" role="none">
                <a href="/constructioninnagpur/contact.php" class="btn btn-primary" role="button">
                    Get Quote
                </a>
            </li>
        </ul>
    </div>
</nav>

<main id="main-content">

<!-- ================== RESPONSIVE STYLES ================== -->
<style>
    :root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --white: #fff;
        --light-gray: #f8f9fa;
        --transition: all 0.3s ease;
    }

    /* Base */
    body {
        font-family: 'Roboto', sans-serif;
        color: var(--charcoal);
        background: var(--white);
        margin: 0;
        padding-top: 70px; /* Fixed nav height */
        line-height: 1.6;
    }

    h1,h2,h3,h4,h5,h6 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
    }

    /* Skip Link */
    .skip-link {
        position: absolute;
        top: -40px;
        left: 6px;
        background: var(--charcoal);
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-weight: 600;
        z-index: 1001;
        transition: top .3s;
        text-decoration: none;
    }
    .skip-link:focus {
        top: 6px;
    }

    /* Navigation */
    .main-nav {
        position: fixed;
        top: 0; left: 0; right: 0;
        background: rgba(255,255,255,.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid #eee;
        z-index: 1000;
        height: 70px;
        transition: var(--transition);
    }

    .main-nav.scrolled {
        background: rgba(255,255,255,.98);
        box-shadow: 0 2px 20px rgba(0,0,0,.1);
    }

    .nav-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
    }

    .nav-logo {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--charcoal);
        text-decoration: none;
        display: flex;
        align-items: center;
    }

    .nav-logo strong {
        color: var(--primary-yellow);
    }

    /* Desktop Menu */
    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 2rem;
        align-items: center;
        transition: var(--transition);
    }

    .nav-link {
        color: #444;
        text-decoration: none;
        font-weight: 500;
        position: relative;
        padding: .5rem 0;
        transition: color .3s;
    }

    .nav-link:hover {
        color: var(--charcoal);
    }

    .nav-link.active {
        color: var(--charcoal);
        font-weight: 600;
    }

    .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--primary-yellow);
        border-radius: 2px;
    }

    .nav-cta .btn {
        background: var(--primary-yellow);
        color: var(--charcoal);
        padding: .6rem 1.4rem;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
        box-shadow: 0 2px 8px rgba(249,168,38,.3);
    }

    .nav-cta .btn:hover {
        background: #e89a1f;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(249,168,38,.4);
    }

    /* Hamburger Toggle */
    .nav-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: .5rem;
        z-index: 1001;
    }

    .hamburger-box {
        width: 24px;
        height: 18px;
        position: relative;
        display: block;
    }

    .hamburger-inner,
    .hamburger-inner::before,
    .hamburger-inner::after {
        width: 100%;
        height: 2px;
        background: var(--charcoal);
        border-radius: 2px;
        position: absolute;
        transition: var(--transition);
    }

    .hamburger-inner {
        top: 50%;
        transform: translateY(-50%);
    }

    .hamburger-inner::before {
        content: '';
        top: -7px;
    }

    .hamburger-inner::after {
        content: '';
        top: 7px;
    }

    /* Mobile Menu */
    @media (max-width: 992px) {
        .nav-toggle {
            display: block;
        }

        .nav-menu {
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: white;
            flex-direction: column;
            padding: 1.5rem 1rem;
            gap: 0;
            box-shadow: 0 10px 30px rgba(0,0,0,.1);
            transform: translateY(-100%);
            opacity: 0;
            visibility: hidden;
            transition: all .35s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: calc(100vh - 70px);
            overflow-y: auto;
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
            border: none;
        }

        .nav-link {
            display: block;
            padding: 1rem 0;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .nav-cta {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
        }

        .nav-cta .btn {
            width: 100%;
            text-align: center;
            padding: 1rem;
            font-size: 1.1rem;
        }

        /* Hamburger to X */
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

        body.nav-open {
            overflow: hidden;
        }
    }

    /* Tablet Optimization */
    @media (max-width: 768px) {
        .nav-container {
            padding: 0 1rem;
        }
        .nav-logo {
            font-size: 1.3rem;
        }
    }

    @media (max-width: 480px) {
        .nav-logo {
            font-size: 1.2rem;
        }
        .nav-toggle {
            padding: .4rem;
        }
    }
</style>

<!-- ================== HEADER SCRIPT ================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('navToggle');
    const menu = document.getElementById('navMenu');
    const nav = document.getElementById('mainNav');

    if (!toggle || !menu) return;

    // Toggle menu
    toggle.addEventListener('click', function () {
        const expanded = this.getAttribute('aria-expanded') === 'true';
        this.setAttribute('aria-expanded', !expanded);
        menu.classList.toggle('active');
        document.body.classList.toggle('nav-open');
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.main-nav') && menu.classList.contains('active')) {
            closeMenu();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && menu.classList.contains('active')) {
            closeMenu();
        }
    });

    // Close on link click (mobile)
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', function () {
            if (window.innerWidth <= 992) closeMenu();
        });
    });

    function closeMenu() {
        menu.classList.remove('active');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('nav-open');
    }

    // Scroll effect
    let scrollTimer;
    window.addEventListener('scroll', function () {
        if (scrollTimer) clearTimeout(scrollTimer);
        scrollTimer = setTimeout(() => {
            nav.classList.toggle('scrolled', window.scrollY > 10);
        }, 10);
    }, { passive: true });
});
</script>