<?php
/**
 * includes/header.php
 * Modern, Responsive, Fully Config-Driven Header
 * Compatible with your config.php (2025)
 */

declare(strict_types=1);

// Ensure config is loaded
require_once __DIR__ . '/../config.php';

// Fallback sanitize function
if (!function_exists('sanitizeOutput')) {
    function sanitizeOutput($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

// Current page & full URL
$current_page = basename($_SERVER['PHP_SELF']);
$current_url = SITE_URL . $_SERVER['REQUEST_URI'];

// Navigation items (Home links to root)
$nav_items = [
    ''                => 'Home',
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
    
    <!-- Dynamic SEO -->
    <title><?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?><?= sanitizeOutput(SITE_NAME) ?></title>
    <meta name="description" content="<?= sanitizeOutput(META_DESCRIPTION) ?>">
    <meta name="keywords" content="<?= sanitizeOutput(META_KEYWORDS ?? 'construction nagpur, builders, residential, commercial') ?>">
    <meta name="author" content="<?= sanitizeOutput(SITE_NAME) ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?><?= sanitizeOutput(SITE_NAME) ?>">
    <meta property="og:description" content="<?= sanitizeOutput(META_DESCRIPTION) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= sanitizeOutput($current_url) ?>">
    <meta property="og:site_name" content="<?= sanitizeOutput(SITE_NAME) ?>">
    <meta property="og:image" content="<?= SITE_URL ?>/assets/images/og-image.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?><?= sanitizeOutput(SITE_NAME) ?>">
    <meta name="twitter:description" content="<?= sanitizeOutput(META_DESCRIPTION) ?>">

    <!-- Canonical -->
    <link rel="canonical" href="<?= sanitizeOutput($current_url) ?>">

    <!-- Favicon & Apple Touch -->
    <link rel="icon" href="<?= SITE_URL ?>/assets/images/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/assets/images/apple-touch-icon.png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css?v=<?= time() ?>">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "<?= sanitizeOutput(SITE_NAME) ?>",
      "image": "<?= SITE_URL ?>/assets/images/logo.png",
      "url": "<?= SITE_URL ?>",
      "telephone": "<?= sanitizeOutput(CONTACT_PHONE) ?>",
      "email": "<?= sanitizeOutput(CONTACT_EMAIL) ?>",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "123 Construction Plaza, Dharampeth",
        "addressLocality": "Nagpur",
        "addressRegion": "Maharashtra",
        "postalCode": "440010",
        "addressCountry": "IN"
      },
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
          "opens": "09:00",
          "closes": "18:00"
        },
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": "Saturday",
          "opens": "09:00",
          "closes": "14:00"
        }
      ]
    }
    </script>
</head>
<body>

<!-- Skip to Main Content -->
<a class="skip-link visually-hidden-focusable" href="#main-content" tabindex="0">Skip to main content</a>

<!-- Fixed Navigation -->
<nav class="main-nav" id="mainNav" aria-label="Main navigation">
    <div class="nav-container">
        <!-- Logo -->
        <a href="<?= SITE_URL ?>/" class="nav-logo" aria-label="<?= sanitizeOutput(SITE_NAME) ?> - Home">
            <?php if (defined('SHOW_LOGO_ICON') && SHOW_LOGO_ICON): ?>
                <img src="<?= SITE_URL ?>/assets/images/logo-icon.png" alt="" width="40" height="40" style="margin-right:10px;border-radius:6px;">
            <?php endif; ?>
            <strong><?= sanitizeOutput(SITE_LOGO_TEXT ?? 'Grand Jyothi') ?></strong>
            <?php if (!empty(SITE_LOGO_SUBTITLE)): ?>
                <span style="margin-left:6px;color:var(--primary-yellow);font-weight:500;">
                    <?= sanitizeOutput(SITE_LOGO_SUBTITLE) ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- Mobile Toggle -->
        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="navMenu">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </button>

        <!-- Navigation Menu -->
        <ul class="nav-menu" id="navMenu" role="menubar">
            <?php foreach ($nav_items as $page => $title): 
                $href = $page === '' ? SITE_URL . '/' : SITE_URL . '/' . $page;
                $is_active = ($current_page === 'index.php' && $page === '') || $current_page === $page;
            ?>
                <li role="none">
                    <a href="<?= $href ?>"
                       class="nav-link <?= $is_active ? 'active' : '' ?>"
                       role="menuitem"
                       aria-current="<?= $is_active ? 'page' : 'false' ?>">
                        <?= sanitizeOutput($title) ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <!-- CTA Button -->
            <li class="nav-cta" role="none">
                <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary">
                    Get Quote
                </a>
            </li>
        </ul>
    </div>
</nav>

<main id="main-content">

<!-- ================== STYLES ================== -->
<style>
    :root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --white: #fff;
        --light-gray: #f8f9fa;
        --transition: all 0.3s ease;
    }

    /* Accessibility */
    .visually-hidden-focusable:not(:focus) {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0,0,0,0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }

    /* Base */
    body {
        font-family: 'Roboto', sans-serif;
        color: var(--charcoal);
        background: var(--white);
        margin: 0;
        padding-top: 70px;
        line-height: 1.6;
    }

    h1,h2,h3,h4,h5,h6 { font-family: 'Poppins', sans-serif; font-weight: 600; }

    /* Navigation */
    .main-nav {
        position: fixed;
        top: 0; left: 0; right: 0;
        background: rgba(255,255,255,.95);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(0,0,0,.08);
        z-index: 1000;
        height: 70px;
        transition: var(--transition);
    }

    .main-nav.scrolled {
        background: rgba(255,255,255,.98);
        box-shadow: 0 4px 30px rgba(0,0,0,.1);
    }

    .nav-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
    }

    .nav-logo {
        font-size: 1.55rem;
        font-weight: 700;
        color: var(--charcoal);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .nav-logo strong { color: var(--primary-yellow); }

    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 2.4rem;
        align-items: center;
    }

    .nav-link {
        color: #444;
        text-decoration: none;
        font-weight: 500;
        padding: .5rem 0;
        position: relative;
        transition: color .3s;
    }

    .nav-link:hover { color: var(--charcoal); }
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
        padding: .75rem 1.8rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        box-shadow: 0 4px 15px rgba(249,168,38,.3);
        transition: var(--transition);
    }

    .nav-cta .btn:hover {
        background: #e89a1f;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(249,168,38,.4);
    }

    /* Hamburger */
    .nav-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: .6rem;
    }

    .hamburger-box { width: 26px; height: 20px; position: relative; display: block; }
    .hamburger-inner,
    .hamburger-inner::before,
    .hamburger-inner::after {
        width: 100%; height: 2.5px; background: var(--charcoal);
        border-radius: 3px; position: absolute; transition: var(--transition);
    }

    .hamburger-inner { top: 50%; transform: translateY(-50%); }
    .hamburger-inner::before { content: ''; top: -8px; }
    .hamburger-inner::after { content: ''; top: 8px; }

    /* Mobile Menu */
    @media (max-width: 992px) {
        .nav-toggle { display: block; }

        .nav-menu {
            position: fixed;
            top: 70px; left: 0; right: 0;
            background: white;
            flex-direction: column;
            padding: 2rem 1.5rem;
            gap: 0;
            box-shadow: 0 15px 40px rgba(0,0,0,.12);
            transform: translateY(-120%);
            opacity: 0;
            visibility: hidden;
            transition: all .4s cubic-bezier(0.4, 0, 0.2, 1);
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

        .nav-menu li:last-child { border: none; }

        .nav-link {
            display: block;
            padding: 1.2rem 0;
            font-size: 1.15rem;
        }

        .nav-cta {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #eee;
        }

        .nav-cta .btn {
            width: 100%;
            text-align: center;
            padding: 1.1rem;
            font-size: 1.1rem;
        }

        /* Hamburger → X */
        .nav-toggle[aria-expanded="true"] .hamburger-inner { background: transparent; }
        .nav-toggle[aria-expanded="true"] .hamburger-inner::before { transform: rotate(45deg); top: 0; }
        .nav-toggle[aria-expanded="true"] .hamburger-inner::after { transform: rotate(-45deg); top: 0; }

        body.nav-open { overflow: hidden; }
    }

    @media (max-width: 480px) {
        .nav-container { padding: 0 1rem; }
        .nav-logo { font-size: 1.35rem; }
    }
</style>

<!-- ================== SCRIPT ================== -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('navToggle');
    const menu = document.getElementById('navMenu');
    const nav = document.getElementById('mainNav');

    if (!toggle || !menu) return;

    function closeMenu() {
        menu.classList.remove('active');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('nav-open');
    }

    toggle.addEventListener('click', () => {
        const expanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', !expanded);
        menu.classList.toggle('active');
        document.body.classList.toggle('nav-open');
    });

    // Close on outside click
    document.addEventListener('click', e => {
        if (!e.target.closest('.main-nav') && menu.classList.contains('active')) closeMenu();
    });

    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && menu.classList.contains('active')) closeMenu();
    });

    // Close on link click (mobile)
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 992) closeMenu();
        });
    });

    // Scroll effect
    let scrollTimer;
    window.addEventListener('scroll', () => {
        if (scrollTimer) clearTimeout(scrollTimer);
        scrollTimer = setTimeout(() => {
            nav.classList.toggle('scrolled', window.scrollY > 10);
        }, 10);
    }, { passive: true });
});
</script>