<?php
/**
 * includes/header.php
 * SAME DESIGN – Now Compact (60px) & Perfect at 100% Zoom
 */

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if (!function_exists('sanitizeOutput')) {
    function sanitizeOutput($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
$current_url = SITE_URL . $_SERVER['REQUEST_URI'];

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
    
    <title><?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?><?= sanitizeOutput(SITE_NAME) ?></title>
    <meta name="description" content="<?= sanitizeOutput(SITE_DESCRIPTION) ?>">
    <meta name="keywords" content="<?= sanitizeOutput(SITE_KEYWORDS) ?>">
    <meta name="author" content="<?= sanitizeOutput(SITE_NAME) ?>">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="<?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?><?= sanitizeOutput(SITE_NAME) ?>">
    <meta property="og:description" content="<?= sanitizeOutput(SITE_DESCRIPTION) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= sanitizeOutput($current_url) ?>">
    <meta property="og:site_name" content="<?= sanitizeOutput(SITE_NAME) ?>">
    <meta property="og:image" content="<?= SITE_URL ?>/assets/images/og-image.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?><?= sanitizeOutput(SITE_NAME) ?>">
    <meta name="twitter:description" content="<?= sanitizeOutput(SITE_DESCRIPTION) ?>">
    <meta name="twitter:image" content="<?= SITE_URL ?>/assets/images/og-image.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?><?= sanitizeOutput(SITE_NAME) ?>">
    <meta name="twitter:description" content="<?= sanitizeOutput(META_DESCRIPTION) ?>">

    <link rel="canonical" href="<?= sanitizeOutput($current_url) ?>">

    
    <!-- ==================== FAVICONS – FIXED & BULLETPROOF (2025) ==================== -->
    <link rel="icon" href="<?= SITE_URL ?>/assets/images/favicon.ico?v=<?= time() ?>" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= SITE_URL ?>/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= SITE_URL ?>/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= SITE_URL ?>/assets/images/apple-touch-icon.png">
    <link rel="manifest" href="<?= SITE_URL ?>/site.webmanifest">
    <meta name="theme-color" content="#F9A826">

    <!-- Open Graph / Facebook -->
    <meta property="og:title" content="<?= isset($page_title) ? sanitizeOutput($page_title) . ' - ' : '' ?><?= sanitizeOutput(SITE_NAME) ?>">
    <meta property="og:description" content="<?= sanitizeOutput(SITE_DESCRIPTION) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= sanitizeOutput($current_url) ?>">
    <meta property="og:site_name" content="<?= sanitizeOutput(SITE_NAME) ?>">
    <meta property="og:image" content="<?= SITE_URL ?>/assets/images/og-image.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css?v=<?= time() ?>">

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
        "streetAddress": "PL NO 55, CHAKRADHAR HO NEAR NAGAR PANCHAYAT BAHADURA ROAD",
        "addressLocality": "Nagpur",
        "addressRegion": "MAHARASHTRA",
        "postalCode": "440034",
        "addressCountry": "IN"
      },
      "openingHoursSpecification": [
        { "@type": "OpeningHoursSpecification", "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"], "opens": " cr 09:00", "closes": "18:00" },
        { "@type": "OpeningHoursSpecification", "dayOfWeek": "Saturday", "opens": "09:00", "closes": "14:00" }
      ]
    }
    </script>
</head>
<body>

<a class="skip-link visually-hidden-focusable" href="#main-content" tabindex="0">Skip to main content</a>

<nav class="main-nav" id="mainNav" aria-label="Main navigation">
    <div class="nav-container">
        <a href="<?= SITE_URL ?>/?ref=top-logo" class="nav-logo" aria-label="<?= sanitizeOutput(SITE_NAME) ?> - Home">
            <?php if (SITE_SETTINGS['logo']['show_icon']): ?>
                <!-- <img src="<?= SITE_URL ?>/assets/images/logo-icon.png" alt="" width="36" height="36" style="margin-right:8px;border-radius:6px;"> -->
            <?php endif; ?>
            <span class="logo-text">
                <strong><?= SITE_SETTINGS['logo']['text'] ?></strong>
                <?php if (!empty(SITE_SETTINGS['logo']['subtitle'])): ?>
                    <span class="logo-subtitle"><?= SITE_SETTINGS['logo']['subtitle'] ?></span>
                <?php endif; ?>
            </span>
        </a>

        <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="navMenu">
            <span class="hamburger-box">
                <span class="hamburger-inner"></span>
            </span>
        </button>

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

            <li class="nav-cta" role="none">
                <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary">
                    Quote
                </a>
            </li>
        </ul>
    </div>
</nav>

<main id="main-content">

<!-- COMPACT & PERFECT STYLES – Only Size Reduced -->
<style>
    :root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --white: #fff;
        --light-gray: #f8f9fa;
        --transition: all 0.3s ease;
    }

    .visually-hidden-focusable:not(:focus) {
        position: absolute !important; width: 1px !important; height: 1px !important;
        padding: 0 !important; margin: -1px !important; overflow: hidden !important;
        clip: rect(0,0,0,0) !important; white-space: nowrap !important; border: 0 !important;
    }

    body {
        font-family: 'Roboto', sans-serif;
        color: var(--charcoal);
        background: var(--white);
        margin: 0;
        padding-top: 60px; /* Reduced from 70px */
        line-height: 1.6;
    }

    h1,h2,h3,h4,h5,h6 { font-family: 'Poppins', sans-serif; font-weight: 600; }

    .main-nav {
        position: fixed;
        top: 0; left: 0; right: 0;
        background: rgba(255,255,255,.95);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(0,0,0,.08);
        z-index: 1000;
        height: 60px; /* Compact */
        transition: var(--transition);
    }

    .main-nav.scrolled {
        background: rgba(255,255,255,.98);
        box-shadow: 0 4px 30px rgba(0,0,0,.1);
    }

    .nav-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.2rem; /* Slightly tighter */
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
    }

    .nav-logo {
        font-size: 1.38rem; /* Reduced from 1.55rem */
        font-weight: 700;
        color: var(--charcoal);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .nav-logo strong { color: var(--primary-yellow); }

    .nav-menu {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 1.9rem; /* Reduced from 2.4rem */
        align-items: center;
    }

    .nav-link {
        color: #444;
        text-decoration: none;
        font-weight: 500;
        padding: .4rem 0; /* Reduced */
        position: relative;
        transition: color .3s;
        font-size: 0.97rem; /* Slightly smaller */
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
        padding: .65rem 1.5rem; /* Smaller */
        border-radius: 9px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        box-shadow: 0 3px 12px rgba(249,168,38,.3);
        transition: var(--transition);
    }

    .nav-cta .btn:hover {
        background: #e89a1f;
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(249,168,38,.4);
    }

    .nav-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: .5rem;
    }

    .hamburger-box { width: 24px; height: 18px; position: relative; display: block; }
    .hamburger-inner,
    .hamburger-inner::before,
    .hamburger-inner::after {
        width: 100%; height: 2.3px; background: var(--charcoal);
        border-radius: 3px; position: absolute; transition: var(--transition);
    }

    .hamburger-inner { top: 50%; transform: translateY(-50%); }
    .hamburger-inner::before { content: ''; top: -7px; }
    .hamburger-inner::after { content: ''; top: 7px; }

    @media (max-width: 992px) {
        .nav-toggle { display: block; }

        .nav-menu {
            position: fixed;
            top: 60px; /* Matches new height */
            left: 0; right: 0;
            background: white;
            flex-direction: column;
            padding: 1.6rem 1.2rem;
            gap: 0;
            box-shadow: 0 12px 35px rgba(0,0,0,.12);
            transform: translateY(-120%);
            opacity: 0;
            visibility: hidden;
            transition: all .38s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: calc(100vh - 60px);
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
            padding: 1rem 0;
            font-size: 1.1rem;
        }

        .nav-cta {
            margin-top: 1.2rem;
            padding-top: 1.2rem;
            border-top: 2px solid #eee;
        }

        .nav-cta .btn {
            width: 100%;
            text-align: center;
            padding: 1rem;
            font-size: 1.05rem;
        }

        .nav-toggle[aria-expanded="true"] .hamburger-inner { background: transparent; }
        .nav-toggle[aria-expanded="true"] .hamburger-inner::before { transform: rotate(45deg); top: 0; }
        .nav-toggle[aria-expanded="true"] .hamburger-inner::after { transform: rotate(-45deg); top: 0; }

        body.nav-open { overflow: hidden; }
    }

    @media (max-width: 480px) {
        .nav-container { padding: 0 1rem; }
        .nav-logo { font-size: 1.28rem; }
    }
</style>

<!-- SAME SCRIPT – Unchanged -->
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

    document.addEventListener('click', e => {
        if (!e.target.closest('.main-nav') && menu.classList.contains('active')) closeMenu();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && menu.classList.contains('active')) closeMenu();
    });

    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 992) closeMenu();
        });
    });

    let scrollTimer;
    window.addEventListener('scroll', () => {
        if (scrollTimer) clearTimeout(scrollTimer);
        scrollTimer = setTimeout(() => {
            nav.classList.toggle('scrolled', window.scrollY > 10);
        }, 10);
    }, { passive: true });
});
</script>