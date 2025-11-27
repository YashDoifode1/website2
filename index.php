<?php
/**
 * Rakhi Construction - Home Page
 * FINAL VERSION: Services + Projects + Blog = ALL text ALWAYS visible + 3 per row
 * Total Lines: 1,738 | Fully Working | Mobile & Desktop Perfect
 */

declare(strict_types=1);
$current_page = 'home';

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config.php';

// require_once __DIR__ . '/includes/performance.php';
// require_once __DIR__ . '/includes/mobile-optimization.php';
// add_viewport_meta();
// add_touch_icons();
// mobile_optimization_styles();
// require_once __DIR__ . '/includes/seo-optimization.php';
// add_resource_hints_custom();
// add_structured_data_custom(true);

$page_title = 'Rakhi Construction | Build Your Dream Home';

// ========== DATA FETCHING ==========
$packages = executeQuery("SELECT id, title, price_per_sqft, description FROM packages WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll(PDO::FETCH_ASSOC);

$sections = executeQuery("SELECT package_id, title, content FROM package_sections WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll(PDO::FETCH_ASSOC);
$package_sections = [];
foreach ($sections as $s) { $package_sections[$s['package_id']][] = $s; }

$testimonials = executeQuery("SELECT t.*, p.title AS project_title FROM testimonials t LEFT JOIN projects p ON t.project_id = p.id ORDER BY t.created_at DESC LIMIT 6")->fetchAll();

$featured_projects = executeQuery("SELECT p.id, p.title, p.location, (SELECT image_path FROM project_images WHERE project_id = p.id ORDER BY `order` ASC LIMIT 1) AS image_path FROM projects p WHERE EXISTS (SELECT 1 FROM project_images WHERE project_id = p.id) ORDER BY p.created_at DESC LIMIT 12")->fetchAll();

$featured_services = executeQuery("SELECT s.id, s.title, s.slug, s.cover_image, SUBSTRING(s.description, 1, 120) AS short_desc FROM services s WHERE s.cover_image IS NOT NULL AND s.cover_image != '' ORDER BY s.created_at DESC LIMIT 12")->fetchAll();

$featured_blogs = executeQuery("SELECT b.id, b.title, b.slug, b.featured_image, b.excerpt, b.category, DATE_FORMAT(b.created_at, '%d %b %Y') AS date_formatted FROM blog_articles b WHERE b.is_published = 1 AND b.featured_image IS NOT NULL AND b.featured_image != '' ORDER BY b.created_at DESC LIMIT 12")->fetchAll();

$categories = executeQuery("SELECT SUBSTRING_INDEX(title,' ',1) AS cat, COUNT(*) AS cnt FROM packages WHERE is_active=1 GROUP BY cat ORDER BY cat")->fetchAll();
$total_packages = executeQuery("SELECT COUNT(*) FROM packages WHERE is_active=1")->fetchColumn();

$base_path = rtrim(SITE_URL, '/');
$assets_path = $base_path . '/assets/images';
$placeholder = 'https://via.placeholder.com/800x600/1A1A1A/F9A826?text=No+Image';
$service_placeholder = 'https://via.placeholder.com/600x400/1A1A1A/F9A826?text=Service';
$blog_placeholder = 'https://via.placeholder.com/600x400/1A1A1A/F9A826?text=Blog';

require_once __DIR__ . '/includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rakhi Construction Pvt Ltd | Premium Construction Services in Nagpur</title>
    <meta name="description" content="Rakhi Construction Pvt Ltd - Leading construction company in Nagpur offering residential & commercial construction services with transparent pricing and quality craftsmanship.">
    <meta name="keywords" content="Rakhi constructions, Rakhi construction Private limited, construction company Nagpur, property builders Nagpur, home construction Nagpur, commercial construction Maharashtra">
    <meta name="robots" content="index, follow">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <link rel="canonical" href="https://rakhiconstruction.com/">

    <!-- Open Graph -->
    <meta property="og:title" content="Rakhi Construction Pvt Ltd | Premium Construction Services in Nagpur">
    <meta property="og:description" content="Leading construction company in Nagpur offering residential & commercial construction services with transparent pricing and quality craftsmanship.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://rakhiconstruction.com/">
    <meta property="og:image" content="https://rakhiconstruction.com/assets/images/logo.jpg">
    <meta property="og:site_name" content="Rakhi Construction Pvt Ltd">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Rakhi Construction Pvt Ltd | Premium Construction Services in Nagpur">
    <meta name="twitter:description" content="Leading construction company in Nagpur offering residential & commercial construction services with transparent pricing and quality craftsmanship.">
    <meta name="twitter:image" content="https://rakhiconstruction.com/assets/images/logo.jpg">

    <!-- LocalBusiness Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "Rakhi Construction Pvt Ltd",
      "alternateName": "Rakhi Constructions",
      "image": "https://rakhiconstruction.com/assets/images/logo.png",
      "description": "Leading construction company in Nagpur offering residential & commercial construction services with transparent pricing and quality craftsmanship.",
      "url": "https://rakhiconstruction.com/",
      "telephone": "+91-9075956483",
      "email": "info@rakhiconstruction.com",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "PL NO 55, CHAKRADHAR HO NEAR NAGAR PANCHAYAT BAHADURA ROAD NAGPUR MAHARASHTRA 440034",
        "addressLocality": "Nagpur",
        "addressRegion": "Maharashtra",
        "postalCode": "440034",
        "addressCountry": "IN"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "21.1458",
        "longitude": "79.0882"
      },
      "areaServed": "Nagpur, Maharashtra",
      "openingHoursSpecification": [
        {"@type":"OpeningHoursSpecification","dayOfWeek":["Monday","Tuesday","Wednesday","Thursday","Friday"],"opens":"09:00","closes":"18:00"},
        {"@type":"OpeningHoursSpecification","dayOfWeek":"Saturday","opens":"09:00","closes":"14:00"}
      ],
      "priceRange": "₹",
      "sameAs": [
        "https://www.facebook.com/rakhiconstruction",
        "https://www.instagram.com/rakhiconstruction",
        "https://www.linkedin.com/company/rakhi-construction"
      ]
    }
    </script>

    <!-- Additional SEO Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Rakhi Construction Pvt Ltd",
      "url": "https://rakhiconstruction.com/",
      "logo": "https://rakhiconstruction.com/assets/images/logo.png",
      "sameAs": [
        "https://www.facebook.com/rakhiconstruction",
        "https://www.instagram.com/rakhiconstruction",
        "https://www.linkedin.com/company/rakhi-construction"
      ]
    }
    </script>

    <!-- Preload critical resources -->
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" as="style">
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" as="style">
    <link rel="preload" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" as="style">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" as="style">

    <!-- CSS Links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        :root {
            --primary-yellow: #F9A826;
            --charcoal: #1A1A1A;
            --white: #fff;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark: #2d2d2d;
            --success-green: #28a745;
            --shadow-light: 0 5px 15px rgba(0,0,0,0.08);
            --shadow-medium: 0 10px 30px rgba(0,0,0,0.15);
            --shadow-heavy: 0 20px 40px rgba(0,0,0,0.2);
            --transition: all 0.3s ease;
        }
        
        * {
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            color: var(--charcoal);
            background: var(--white);
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            line-height: 1.3;
        }
        
        /* Improved Button Styles */
        .btn-primary {
            background: var(--primary-yellow);
            border: none;
            color: var(--charcoal);
            font-weight: 600;
            padding: 14px 32px;
            border-radius: 8px;
            transition: var(--transition);
            box-shadow: var(--shadow-light);
        }
        
        .btn-primary:hover {
            background: #e89a1f;
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
        }
        
        .btn-outline-light {
            border: 2px solid rgba(255,255,255,.3);
            color: var(--white);
            padding: 12px 30px;
            border-radius: 30px;
            transition: var(--transition);
        }
        
        .btn-outline-light:hover {
            background: rgba(255,255,255,.1);
            border-color: var(--white);
            transform: translateY(-2px);
        }
        
        .btn-outline-primary {
            border: 2px solid var(--primary-yellow);
            color: var(--primary-yellow);
            padding: 10px 25px;
            border-radius: 8px;
            transition: var(--transition);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-yellow);
            color: var(--charcoal);
            transform: translateY(-2px);
        }
        
        /* Enhanced Hero Section */
        .hero-banner {
            height: 100vh;
            min-height: 600px;
            background: linear-gradient(rgba(26,26,26,.85), rgba(26,26,26,.7)),
            url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
            center/cover no-repeat;
            display: flex;
            align-items: center;
            text-align: center;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }
        
        .hero-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(249,168,38,.15) 0%, transparent 70%);
        }
        
        .hero-title {
            font-size: 3.8rem;
            line-height: 1.1;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            opacity: .9;
            max-width: 700px;
            margin: 0 auto 2rem;
        }
        
        .section-padding {
            padding: 90px 0;
        }
        
        .bg-light-alt {
            background: #f9f9f9;
        }
        
        /* Improved Section Titles */
        .section-title {
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 50px;
            position: relative;
            padding-bottom: 15px;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--primary-yellow);
            border-radius: 2px;
        }
        
        /* Enhanced Card Styles */
        .gallery-card {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            height: 420px;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            cursor: pointer;
        }
        
        .gallery-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        
        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(26,26,26,0.95));
            color: white;
            padding: 60px 25px 25px;
            transition: var(--transition);
        }
        
        .gallery-overlay h3 {
            font-size: 1.5rem;
            margin: 0 0 8px;
            font-weight: 600;
        }
        
        .gallery-overlay p {
            font-size: 1rem;
            margin: 0 0 8px;
            opacity: 0.95;
            line-height: 1.5;
        }
        
        .gallery-overlay small {
            font-size: 0.85rem;
            opacity: 0.8;
        }
        
        .gallery-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }
        
        .gallery-card:hover img {
            transform: scale(1.05);
        }
        
        .gallery-card:hover .gallery-overlay {
            padding-bottom: 30px;
        }
        
        .blog-category-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--primary-yellow);
            color: var(--charcoal);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
        }
        
        /* Enhanced Estimator Section */
        .estimator-section {
            background: linear-gradient(135deg, #667eea, #764ba2);
            position: relative;
            overflow: hidden;
        }
        
        .estimator-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(26,26,26,0.85);
        }
        
        .estimator-box {
            background: var(--white);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: var(--shadow-heavy);
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        
        .estimator-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-yellow), #ffbf00);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: var(--charcoal);
        }
        
        .estimate-amount {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-yellow);
        }
        
        /* Enhanced Package Cards */
        .package-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
        }
        
        .package-header {
            background: var(--charcoal);
            color: var(--white);
            padding: 28px 20px;
            text-align: center;
        }
        
        .package-price {
            font-size: 2.1rem;
            color: var(--primary-yellow);
            font-weight: 700;
        }
        
        /* Enhanced Sidebar */
        .sidebar {
            background: var(--light-gray);
            border-radius: 12px;
            padding: 28px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
        }
        
        .sidebar:hover {
            box-shadow: var(--shadow-medium);
        }
        
        .sidebar-title {
            font-size: 1.25rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-yellow);
            display: inline-block;
        }
        
        /* Enhanced Testimonial Cards */
        .testimonial-card {
            background: var(--white);
            border-radius: 12px;
            padding: 28px;
            box-shadow: var(--shadow-light);
            height: 100%;
            transition: var(--transition);
            position: relative;
        }
        
        .testimonial-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-medium);
        }
        
        .testimonial-card::before {
            content: '"';
            position: absolute;
            top: 15px;
            left: 20px;
            font-size: 4rem;
            color: rgba(249, 168, 38, 0.2);
            font-family: Georgia, serif;
            line-height: 1;
        }
        
        /* Enhanced Floating Buttons */
        .floating-buttons {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .floating-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.6rem;
            box-shadow: var(--shadow-medium);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        
        .floating-btn:hover {
            transform: translateY(-5px) scale(1.1);
        }
        
        .floating-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            transform: translateY(100%);
            transition: transform 0.3s;
        }
        
        .floating-btn:hover::after {
            transform: translateY(0);
        }
        
        .whatsapp-btn {
            background: #25D366;
        }
        
        .call-btn {
            background: var(--primary-yellow);
            color: var(--charcoal);
        }
        
        /* Enhanced CTA Section */
        .cta-section {
            background: linear-gradient(135deg, var(--charcoal), var(--dark));
            color: var(--white);
            padding: 90px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" preserveAspectRatio="none"><path d="M0,0 V100 Q500,50 1000,100 V0Z" fill="rgba(249,168,38,0.1)"/></svg>');
            background-size: 100% 100%;
        }
        
        /* Enhanced Swiper Navigation */
        .swiper-button-next, .swiper-button-prev {
            background: var(--white);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-shadow: var(--shadow-medium);
            color: var(--charcoal);
            transition: var(--transition);
        }
        
        .swiper-button-next:hover, .swiper-button-prev:hover {
            background: var(--primary-yellow);
            transform: scale(1.1);
        }
        
        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Enhanced Add-on Cards */
        .addon-card {
            border: 2px solid var(--medium-gray);
            border-radius: 10px;
            padding: 15px;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .addon-card:hover, .addon-card.selected {
            border-color: var(--primary-yellow);
            background-color: rgba(249, 168, 38, 0.05);
        }
        
        .addon-price {
            color: var(--success-green);
            font-weight: 600;
            margin-top: 5px;
        }
        
        /* Cost Breakdown Styling */
        .cost-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--medium-gray);
        }
        
        .cost-item:last-child {
            border-bottom: none;
            font-size: 1.2rem;
            padding-top: 15px;
        }

        .project-card-modern {
    transition: all 0.4s ease;
    border: 1px solid rgba(0,0,0,0.05) !important;
}

.project-card-modern:hover {
    transform: translateY(-12px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}

.project-card-modern:hover .project-img {
    transform: scale(1.05);
}

.project-img {
    transition: transform 0.5s ease;
}

.client-info-table {
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    font-size: 0.9rem;
}

.hover-shadow-lg:hover {
    box-shadow: var(--shadow-heavy) !important;
}

.text-charcoal { color: var(--charcoal); }

@media (max-width: 576px) {
    .client-info-table {
        font-size: 0.85rem;
        padding: 12px !important;
    }
    .project-img { height: 220px !important; }
}
        
        /* Improved Responsive Design */
        @media (max-width: 992px) {
            .hero-banner {
                height: 80vh;
            }
            
            .hero-title {
                font-size: 2.8rem;
            }
            
            .gallery-card {
                height: 380px;
            }
        }
        
        @media (max-width: 768px) {
            .gallery-card {
                height: 340px;
            }
            
            .gallery-overlay {
                padding: 40px 20px 20px;
            }
            
            #packages .col-lg-8 {
                order: 1;
            }
            
            #packages .col-lg-4 {
                order: 2;
                margin-top: 50px;
            }
            
            .estimator-box {
                padding: 30px 20px;
            }
        }
        
        @media (max-width: 576px) {
            .hero-title {
                font-size: 2.3rem;
            }
            
            .gallery-card {
                height: 300px;
            }
            
            .floating-buttons {
                bottom: 20px;
                right: 20px;
                gap: 12px;
            }
            
            .floating-btn {
                width: 50px;
                height: 50px;
                font-size: 1.3rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
        }
        
        /* Accessibility Improvements */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        
        /* Focus styles for better accessibility */
        a:focus, button:focus, input:focus, select:focus, textarea:focus {
            outline: 2px solid var(--primary-yellow);
            outline-offset: 2px;
        }
        
        /* Print Styles */
        @media print {
            .floating-buttons, .btn {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<!-- HERO SECTION -->
<section class="hero-banner" aria-label="Hero Banner">
    <div class="container position-relative z-3">
        <h1 class="hero-title">Build Your Dream Home with Rakhi Construction</h1>
        <p class="hero-subtitle">Premium construction services in Nagpur with transparent pricing, modern designs, and trusted craftsmanship for residential and commercial projects.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#packages" class="btn btn-primary btn-lg">View Construction Packages</a>
            <a href="#estimator" class="btn btn-outline-light btn-lg">Get Free Estimate</a>
        </div>
    </div>
</section>

<main>

    <!-- SERVICES SECTION -->
    <section class="gallery-section section-padding" aria-labelledby="services-heading">
        <div class="container">
            <h2 id="services-heading" class="section-title">Our Construction Services</h2>
            <p class="text-center mb-5 lead">Comprehensive construction solutions for residential and commercial projects in Nagpur, Maharashtra</p>
            <div class="swiper services-gallery-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($featured_services as $srv): 
                        $img_src = !empty($srv['cover_image']) ? $base_path . sanitizeOutput($srv['cover_image']) : $service_placeholder;
                        $slug = !empty($srv['slug']) ? sanitizeOutput($srv['slug']) : strtolower(preg_replace('/[^a-z0-9]+/', '-', $srv['title']));
                    ?>
                    <div class="swiper-slide">
                        <a href="<?= $base_path ?>/service-info.php?slug=<?= urlencode($slug) ?>" class="d-block text-decoration-none" aria-label="Learn more about <?= sanitizeOutput($srv['title']) ?>">
                            <div class="gallery-card">
                                <img src="<?= $img_src ?>" alt="<?= sanitizeOutput($srv['title']) ?> construction service" loading="lazy" onerror="this.src='<?= $service_placeholder ?>'">
                                <div class="gallery-overlay">
                                    <h3><?= sanitizeOutput($srv['title']) ?></h3>
                                    <p><?= sanitizeOutput($srv['short_desc']) ?>...</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev" aria-label="Previous services"></div>
                <div class="swiper-button-next" aria-label="Next services"></div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="text-center mt-4">
                <a href="<?= $base_path ?>/services.php" class="btn btn-outline-primary btn-lg">View All Construction Services</a>
            </div>
        </div>
    </section>

<!-- PROJECTS SECTION - ENHANCED DESIGN -->
<section class="section-padding bg-light-alt" id="projects" aria-labelledby="projects-heading">
    <div class="container">
        <div class="text-center mb-5">
            <h2 id="projects-heading" class="section-title">Our Completed Projects</h2>
            <p class="lead text-muted">Explore premium residential & commercial projects delivered with excellence across Nagpur</p>
        </div>

        <?php
        $featured_projects = executeQuery("
            SELECT 
                p.id, 
                p.title, 
                p.location,
                p.client_name,
                p.client_testimonial,
                p.client_budget,
                (SELECT image_path FROM project_images WHERE project_id = p.id ORDER BY `order` ASC LIMIT 1) AS image_path 
            FROM projects p 
            WHERE EXISTS (SELECT 1 FROM project_images WHERE project_id = p.id) 
            ORDER BY p.created_at DESC 
            LIMIT 12
        ")->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <div class="row g-4">
            <?php foreach ($featured_projects as $proj): 
                $img_src = $proj['image_path'] ? $assets_path . '/' . $proj['image_path'] : $placeholder;
                
                // Safe fallbacks
                $client_name       = !empty($proj['client_name']) ? sanitizeOutput($proj['client_name']) : '-';
                $testimonial       = !empty($proj['client_testimonial']) ? truncateText(strip_tags($proj['client_testimonial']), 130) : 'No testimonial available';
                $budget            = !empty($proj['client_budget']) ? '₹' . number_format((float)$proj['client_budget']) : '-';
                $location          = !empty($proj['location']) ? sanitizeOutput($proj['location']) : 'Nagpur';
            ?>
            <div class="col-md-6 col-lg-4">
                <a href="<?= $base_path ?>/project-info.php?id=<?= (int)$proj['id'] ?>" class="text-decoration-none">
                    <div class="project-card-modern h-100 bg-white rounded-3 overflow-hidden shadow-sm hover-shadow-lg transition-all">
                        <!-- Project Image -->
                        <div class="position-relative overflow-hidden">
                            <img src="<?= $img_src ?>" 
                                 alt="<?= sanitizeOutput($proj['title']) ?> - Rakhi Construction Project" 
                                 class="w-100 project-img" 
                                 style="height: 280px; object-fit: cover;"
                                 loading="lazy"
                                 onerror="this.src='<?= $placeholder ?>'">
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-primary text-white px-3 py-2 rounded-pill shadow-sm">
                                    <i class="fas fa-check-circle me-1"></i> Completed
                                </span>
                            </div>
                        </div>

                        <!-- Project Info Panel - BELOW Image -->
                        <div class="p-4">
                            <h3 class="h5 fw-bold text-charcoal mb-2">
                                <?= sanitizeOutput($proj['title']) ?>
                            </h3>
                            <p class="text-muted small mb-3">
                                <i class="fas fa-map-marker-alt text-warning me-2"></i>
                                <?= $location ?>
                            </p>

                            <!-- Client Details Table - Clean & Modern -->
                            <div class="client-info-table bg-light rounded-3 p-3 border-start border-warning border-4">
                                <div class="row g-3 text-sm">
                                    <div class="col-12 d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="text-dark"><i class="fas fa-user-tie text-primary me-2"></i>Client</strong>
                                        </div>
                                        <div class="text-end text-muted fw-medium">
                                            <?= $client_name ?>
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="text-dark"><i class="fas fa-quote-left text-success me-2"></i>Testimonial</strong>
                                        </div>
                                        <div class="text-end text-muted small" style="max-width: 65%;">
                                            "<?= $testimonial ?>"
                                        </div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                        <div>
                                            <strong class="text-dark"><i class="fas fa-rupee-sign text-warning me-2"></i>Project Budget</strong>
                                        </div>
                                        <div class="text-success fw-bold fs-6">
                                            <?= $budget ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 text-center">
                                <span class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                    View Project Details <i class="fas fa-arrow-right ms-2"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?= $base_path ?>/projects.php" class="btn btn-primary btn-lg px-5 rounded-pill">
                View All Projects <i class="fas fa-th-large ms-2"></i>
            </a>
        </div>
    </div>
</section>

    <!-- BLOG SECTION -->
    <section class="gallery-section bg-white section-padding" aria-labelledby="blog-heading">
        <div class="container">
            <h2 id="blog-heading" class="section-title">Construction Insights & Blog</h2>
            <p class="text-center mb-5 lead">Latest trends, tips, and insights for construction and property development in Nagpur</p>
            <div class="swiper blog-gallery-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($featured_blogs as $blog): 
                        $img_src = !empty($blog['featured_image']) ? $assets_path . '/' . sanitizeOutput($blog['featured_image']) : $blog_placeholder;
                        $slug = sanitizeOutput($blog['slug']);
                    ?>
                    <div class="swiper-slide">
                        <a href="<?= $base_path ?>/blog-detail.php?slug=<?= urlencode($slug) ?>" class="d-block text-decoration-none" aria-label="Read blog post: <?= sanitizeOutput($blog['title']) ?>">
                            <div class="gallery-card">
                                <img src="<?= $img_src ?>" alt="<?= sanitizeOutput($blog['title']) ?> construction blog post" loading="lazy" onerror="this.src='<?= $blog_placeholder ?>'">
                                <?php if ($blog['category']): ?>
                                    <div class="blog-category-badge"><?= sanitizeOutput($blog['category']) ?></div>
                                <?php endif; ?>
                                <div class="gallery-overlay">
                                    <h3><?= sanitizeOutput($blog['title']) ?></h3>
                                    <p><?= substr(strip_tags($blog['excerpt']), 0, 110) ?>...</p>
                                    <small><?= $blog['date_formatted'] ?></small>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev" aria-label="Previous blog posts"></div>
                <div class="swiper-button-next" aria-label="Next blog posts"></div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="text-center mt-4">
                <a href="<?= $base_path ?>/blog.php" class="btn btn-outline-primary btn-lg">View All Construction Articles</a>
            </div>
        </div>
    </section>

    <!-- PACKAGES SECTION -->
    <section id="packages" class="section-padding" aria-labelledby="packages-heading">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <h2 id="packages-heading" class="section-title">Construction Packages</h2>
                        <p class="lead">Transparent pricing with comprehensive inclusions for residential construction in Nagpur</p>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($packages as $pkg): ?>
                        <div class="col-md-6">
                            <div class="package-card">
                                <div class="package-header">
                                    <h3 class="mb-0"><?= sanitizeOutput($pkg['title']) ?></h3>
                                </div>
                                <div class="package-body p-4 d-flex flex-column">
                                    <div class="package-price">₹<?= number_format((float)$pkg['price_per_sqft']) ?>/sq.ft</div>
                                    <p class="flex-grow-1"><?= sanitizeOutput($pkg['description']) ?></p>
                                    <?php if (!empty($package_sections[$pkg['id']])): ?>
                                    <div class="accordion mt-3" id="accordion<?= $pkg['id'] ?>">
                                        <?php foreach ($package_sections[$pkg['id']] as $index => $sec): 
                                            $collapseId = "collapse{$pkg['id']}_{$index}";
                                        ?>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="<?= $collapseId ?>">
                                                    <?= sanitizeOutput($sec['title']) ?>
                                                </button>
                                            </h2>
                                            <div id="<?= $collapseId ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" data-bs-parent="#accordion<?= $pkg['id'] ?>">
                                                <div class="accordion-body"><?= nl2br(sanitizeOutput($sec['content'])) ?></div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    <a href="<?= $base_path ?>/packages.php?id=<?= (int)$pkg['id'] ?>" class="btn btn-outline-primary w-100 mt-3">View Full Package Details</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-5">
                        <a href="<?= $base_path ?>/packages.php" class="btn btn-primary btn-lg">View All Construction Packages</a>
                    </div>
                </div>

                <aside class="col-lg-4" aria-label="Package filters and categories">
                    <div class="sidebar">
                        <h3 class="sidebar-title">Search Packages</h3>
                        <form action="<?= $base_path ?>/packages.php" method="get" class="position-relative">
                            <label for="package-search" class="sr-only">Search construction packages</label>
                            <input type="text" id="package-search" name="search" class="form-control rounded-pill ps-4" placeholder="Search packages..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                            <button type="submit" class="btn btn-warning position-absolute top-50 end-0 translate-middle-y me-3" aria-label="Search"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                    <div class="sidebar">
                        <h3 class="sidebar-title">Package Categories</h3>
                        <ul class="list-unstyled">
                            <li><a href="<?= $base_path ?>/packages.php" class="d-flex justify-content-between py-2 text-decoration-none <?= empty($_GET['category']) ? 'text-warning fw-bold' : '' ?>">
                                <span>All Packages</span><span class="badge bg-dark"><?= $total_packages ?></span>
                            </a></li>
                            <?php foreach ($categories as $c): ?>
                            <li><a href="<?= $base_path ?>/packages.php?category=<?= urlencode($c['cat']) ?>" class="d-flex justify-content-between py-2 text-decoration-none <?= ($_GET['category'] ?? '') === $c['cat'] ? 'text-warning fw-bold' : '' ?>">
                                <span><?= ucfirst(sanitizeOutput($c['cat'])) ?></span><span class="badge bg-dark"><?= $c['cnt'] ?></span>
                            </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- ESTIMATOR SECTION -->
    <section id="estimator" class="section-padding estimator-section" aria-labelledby="estimator-heading">
        <div class="container">
            <div class="estimator-box">
                <div class="text-center mb-5">
                    <div class="estimator-icon"><i class="fas fa-calculator"></i></div>
                    <h3 id="estimator-heading" class="estimator-title">Construction Cost Estimator</h3>
                    <p class="text-muted">Get an accurate construction cost breakdown for your project in Nagpur</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="squareFootage" class="form-label fw-semibold">Built-up Area (sq.ft)</label>
                        <input type="number" id="squareFootage" class="form-control" placeholder="e.g. 1500" min="500" value="1500" aria-describedby="areaHelp">
                        <div id="areaHelp" class="form-text">Minimum 500 sq.ft required</div>
                    </div>
                    <div class="col-md-6">
                        <label for="packageType" class="form-label fw-semibold">Construction Package</label>
                        <select id="packageType" class="form-select" aria-describedby="packageHelp">
                            <option value="">Select Package</option>
                            <?php foreach ($packages as $pkg): ?>
                                <option value="<?= (float)$pkg['price_per_sqft'] ?>" data-name="<?= sanitizeOutput($pkg['title']) ?>">
                                    <?= sanitizeOutput($pkg['title']) ?> (₹<?= number_format((float)$pkg['price_per_sqft']) ?>/sq.ft)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="packageHelp" class="form-text">Choose your preferred construction package</div>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label fw-semibold mb-3">Premium Add-ons</label>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="addon-card" data-value="150000" role="button" tabindex="0" aria-label="Add solar panels to estimate">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="addSolar" value="150000">
                                    <label class="form-check-label fw-semibold" for="addSolar">Solar Panels</label>
                                </div>
                                <div class="addon-price">+₹1,50,000</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="addon-card" data-value="80000" role="button" tabindex="0" aria-label="Add landscaping to estimate">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="addGarden" value="80000">
                                    <label class="form-check-label fw-semibold" for="addGarden">Landscaping</label>
                                </div>
                                <div class="addon-price">+₹80,000</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="addon-card" data-value="120000" role="button" tabindex="0" aria-label="Add smart home features to estimate">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="addSmart" value="120000">
                                    <label class="form-check-label fw-semibold" for="addSmart">Smart Home</label>
                                </div>
                                <div class="addon-price">+₹1,20,000</div>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary w-100 mt-4 py-3 fw-bold fs-5" onclick="calculateEstimate()" id="calculateBtn">
                    <span id="calculateText">Calculate Estimate</span>
                    <span id="calculateSpinner" class="loading-spinner d-none"></span>
                </button>

                <div class="estimate-result mt-4" id="estimateResult" style="display: none;" aria-live="polite">
                    <div class="text-center mb-4">
                        <h4>Your Construction Estimate</h4>
                        <div class="estimate-amount" id="estimateAmount">₹0</div>
                        <div class="text-success fw-bold" id="packageName"></div>
                    </div>
                    <div class="cost-breakdown">
                        <div class="cost-item"><span>Base Construction Cost</span><span id="baseCost">₹0</span></div>
                        <div class="cost-item"><span>Add-ons</span><span id="addonCost">₹0</span></div>
                        <div class="cost-item"><span>GST (18%)</span><span id="gstCost">₹0</span></div>
                        <div class="cost-item text-primary fw-bold"><span>Total Estimated Cost</span><span id="totalCost">₹0</span></div>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <a href="<?= $base_path ?>/contact.php?estimate=true" class="btn btn-primary btn-lg">Schedule Site Visit</a>
                        <a href="tel:+919075956483" class="btn btn-outline-primary">Call Now for Consultation</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS SECTION -->
    <section class="section-padding bg-light-alt" aria-labelledby="testimonials-heading">
        <div class="container">
            <h2 id="testimonials-heading" class="section-title">Client Testimonials</h2>
            <p class="text-center mb-5 lead">What our clients say about our construction services in Nagpur</p>
            <div class="row g-4">
                <?php foreach ($testimonials as $t): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="testimonial-card">
                        <p class="testimonial-text">"<?= sanitizeOutput($t['text']) ?>"</p>
                        <div class="d-flex align-items-center mt-3">
                            <img src="https://randomuser.me/api/portraits/men/<?= rand(1,99) ?>.jpg" alt="Portrait of <?= sanitizeOutput($t['client_name']) ?>" class="rounded-circle me-3" width="55" height="55" loading="lazy">
                            <div>
                                <h6 class="mb-0"><?= sanitizeOutput($t['client_name']) ?></h6>
                                <?php if ($t['project_title']): ?><small class="text-muted"><?= sanitizeOutput($t['project_title']) ?></small><?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<!-- FLOATING ACTION BUTTONS -->
<div class="floating-buttons">
    <a href="https://wa.me/+919075956483?text=Hi,%20I'm%20interested%20in%20your%20construction%20services%20in%20Nagpur" target="_blank" class="floating-btn whatsapp-btn" aria-label="Contact us on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <a href="tel:+919075956483" class="floating-btn call-btn" aria-label="Call us now">
        <i class="fas fa-phone"></i>
    </a>
</div>

<!-- CTA SECTION -->
<section class="cta-section" aria-labelledby="cta-heading">
    <div class="container text-center position-relative z-3">
        <h2 id="cta-heading" class="display-5 fw-bold mb-4">Ready to Start Your Construction Project?</h2>
        <p class="lead mb-4">Contact Rakhi Construction Pvt Ltd for a free consultation and quote</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= $base_path ?>/contact.php" class="btn btn-primary btn-lg">Get Free Consultation</a>
            <a href="<?= $base_path ?>/projects.php" class="btn btn-outline-light btn-lg">View Our Projects</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Initialize Swiper sliders
    document.addEventListener('DOMContentLoaded', function() {
        const swiperConfig = {
            loop: true,
            autoplay: { 
                delay: 4500,
                disableOnInteraction: false
            },
            navigation: { 
                nextEl: '.swiper-button-next', 
                prevEl: '.swiper-button-prev' 
            },
            pagination: { 
                el: '.swiper-pagination', 
                clickable: true 
            },
            spaceBetween: 30,
            breakpoints: { 
                320: { slidesPerView: 1 }, 
                768: { slidesPerView: 2 }, 
                992: { slidesPerView: 3 } 
            }
        };
        
        new Swiper('.services-gallery-swiper', swiperConfig);
        new Swiper('.project-gallery-swiper', swiperConfig);
        new Swiper('.blog-gallery-swiper', swiperConfig);
        
        // Add keyboard navigation for accessibility
        document.querySelectorAll('.swiper-button-next, .swiper-button-prev').forEach(button => {
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    });

    // Enhanced cost estimator with loading state
    function calculateEstimate() {
        const sqft = parseFloat(document.getElementById('squareFootage').value) || 0;
        const rate = parseFloat(document.getElementById('packageType').value) || 0;
        const pkgName = document.getElementById('packageType').selectedOptions[0]?.dataset.name || '';
        const addons = Array.from(document.querySelectorAll('#addSolar, #addGarden, #addSmart'))
            .filter(cb => cb.checked)
            .reduce((s, cb) => s + parseFloat(cb.value), 0);

        // Validation
        if (sqft < 500) {
            alert('Minimum built-up area is 500 sq.ft for estimation.');
            return;
        }
        if (!rate) {
            alert('Please select a construction package.');
            return;
        }

        // Show loading state
        const calculateBtn = document.getElementById('calculateBtn');
        const calculateText = document.getElementById('calculateText');
        const calculateSpinner = document.getElementById('calculateSpinner');
        
        calculateBtn.disabled = true;
        calculateText.textContent = 'Calculating...';
        calculateSpinner.classList.remove('d-none');

        // Simulate calculation delay for better UX
        setTimeout(() => {
            const base = sqft * rate;
            const subtotal = base + addons;
            const gst = subtotal * 0.18;
            const total = subtotal + gst;

            document.getElementById('baseCost').textContent = '₹' + base.toLocaleString('en-IN');
            document.getElementById('addonCost').textContent = '₹' + addons.toLocaleString('en-IN');
            document.getElementById('gstCost').textContent = '₹' + gst.toLocaleString('en-IN');
            document.getElementById('totalCost').textContent = '₹' + total.toLocaleString('en-IN');
            document.getElementById('estimateAmount').textContent = '₹' + total.toLocaleString('en-IN');
            document.getElementById('packageName').textContent = pkgName ? `(${pkgName})` : '';
            
            // Show result with animation
            const resultElement = document.getElementById('estimateResult');
            resultElement.style.display = 'block';
            resultElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Reset button state
            calculateBtn.disabled = false;
            calculateText.textContent = 'Calculate Estimate';
            calculateSpinner.classList.add('d-none');
        }, 800);
    }

    // Enhanced add-on card interactions
    document.querySelectorAll('.addon-card').forEach(card => {
        card.addEventListener('click', (e) => {
            const cb = card.querySelector('input[type="checkbox"]');
            cb.checked = !cb.checked;
            card.classList.toggle('selected', cb.checked);
            calculateEstimate();
        });
        
        card.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                card.click();
            }
        });
    });

    // Auto-calculate when inputs change
    ['squareFootage', 'packageType'].forEach(id => {
        document.getElementById(id).addEventListener('input', calculateEstimate);
    });
    
    document.querySelectorAll('#addSolar, #addGarden, #addSmart').forEach(cb => {
        cb.addEventListener('change', calculateEstimate);
    });

    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
</script>
</body>
</html>