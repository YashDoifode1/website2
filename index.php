<?php
/**
 * Home Page – Grand Jyothi Construction
 * Now with: Projects + Services + Blog Slideshow
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config.php';

$page_title = 'Grand Jyothi Construction | Build Your Dream Home';

// ---------- 1. Packages ----------
$packages = executeQuery("
    SELECT id, title, price_per_sqft, description, features 
    FROM packages 
    WHERE is_active = 1 
    ORDER BY display_order ASC, created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ---------- 2. Package sections (accordions) ----------
$sections = executeQuery("
    SELECT package_id, title, content
    FROM package_sections
    WHERE is_active = 1
    ORDER BY display_order ASC
")->fetchAll(PDO::FETCH_ASSOC);

$package_sections = [];
foreach ($sections as $s) {
    $package_sections[$s['package_id']][] = $s;
}

// ---------- 3. Testimonials ----------
$testimonials = executeQuery("
    SELECT t.*, p.title AS project_title 
    FROM testimonials t
    LEFT JOIN projects p ON t.project_id = p.id
    ORDER BY t.created_at DESC LIMIT 6
")->fetchAll();

// ---------- 4. Latest 8 Projects ----------
$featured_projects = executeQuery("
    SELECT p.id, p.title, p.location,
           (SELECT image_path FROM project_images WHERE project_id = p.id ORDER BY `order` ASC, id ASC LIMIT 1) AS image_path
    FROM projects p
    WHERE EXISTS (SELECT 1 FROM project_images WHERE project_id = p.id)
    ORDER BY p.created_at DESC
    LIMIT 8
")->fetchAll();

// ---------- 5. Latest 8 Services ----------
$featured_services = executeQuery("
    SELECT s.id, s.title, s.slug, s.cover_image,
           SUBSTRING(s.description, 1, 100) AS short_desc
    FROM services s
    WHERE s.cover_image IS NOT NULL AND s.cover_image != ''
    ORDER BY s.created_at DESC
    LIMIT 8
")->fetchAll();

// ---------- 6. Latest 8 Blog Articles ----------
$featured_blogs = executeQuery("
    SELECT b.id, b.title, b.slug, b.featured_image, b.excerpt, b.category, b.created_at
    FROM blog_articles b
    WHERE b.is_published = 1 AND b.featured_image IS NOT NULL AND b.featured_image != ''
    ORDER BY b.created_at DESC
    LIMIT 8
")->fetchAll();

// ---------- 7. Sidebar Data ----------
$categories = executeQuery(
    "SELECT SUBSTRING_INDEX(title,' ',1) AS cat, COUNT(*) AS cnt
     FROM packages WHERE is_active=1 GROUP BY cat ORDER BY cat"
)->fetchAll();

$total_packages = executeQuery("SELECT COUNT(*) FROM packages WHERE is_active=1")->fetchColumn();

$popular_packages = executeQuery(
    "SELECT p.title, p.price_per_sqft,
           (SELECT image_path FROM project_images WHERE project_id = p.id ORDER BY `order` ASC, id ASC LIMIT 1) AS thumb
     FROM packages p
     WHERE p.is_active=1 
     ORDER BY p.display_order ASC LIMIT 3"
)->fetchAll();

// Paths
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
    <title><?= $page_title ?></title>

    <!-- Bootstrap + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- Swiper.js -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        :root{
            --primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;
            --light-gray:#f8f9fa;--medium-gray:#e9ecef;--dark:#2d2d2d;
        }
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);line-height:1.6;}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}

        .btn-primary{background:var(--primary-yellow);border:none;color:var(--charcoal);
            font-weight:600;padding:14px 32px;border-radius:8px;transition:all .3s;}
        .btn-primary:hover{background:#e89a1f;color:var(--charcoal);transform:translateY(-2px);}
        .btn-outline-light{border:2px solid rgba(255,255,255,.3);color:var(--white);
            padding:12px 30px;border-radius:30px;transition:all .3s;}
        .btn-outline-light:hover{background:rgba(255,255,255,.1);border-color:var(--white);}
        .btn-outline-primary{border:2px solid var(--primary-yellow);color:var(--primary-yellow);
            padding:10px 25px;border-radius:8px;}
        .btn-outline-primary:hover{background:var(--primary-yellow);color:var(--charcoal);}

        .hero-banner{
            height:100vh;min-height:600px;background:linear-gradient(rgba(26,26,26,.7),rgba(26,26,26,.7)),
            url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
            center/cover no-repeat;display:flex;align-items:center;text-align:center;color:var(--white);
            position:relative;overflow:hidden;
        }
        .hero-banner::before{
            content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.15) 0%,transparent 70%);
        }
        .hero-title{font-size:3.8rem;line-height:1.1;margin-bottom:1rem;}
        .hero-subtitle{font-size:1.3rem;opacity:.9;max-width:700px;margin:0 auto 2rem;}

        .section-padding{padding:90px 0;}
        .section-padding-sm{padding:70px 0;}
        .bg-light-alt{background:#f9f9f9;}

        .section-title{
            font-size:2.2rem;text-align:center;margin-bottom:50px;position:relative;
            padding-bottom:15px;display:inline-block;left:50%;transform:translateX(-50%);
        }
        .section-title::after{
            content:'';position:absolute;bottom:0;left:50%;transform:translateX(-50%);
            width:80px;height:4px;background:var(--primary-yellow);border-radius:2px;
        }

        /* Enhanced Professional Estimator */
        .estimator-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        .estimator-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(26, 26, 26, 0.85);
        }
        .estimator-box{
            background: var(--white);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.15);
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .estimator-header {
            text-align: center;
            margin-bottom: 40px;
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
        .estimator-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--charcoal);
            margin-bottom: 10px;
        }
        .estimator-subtitle {
            color: #666;
            font-size: 1.1rem;
        }
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-yellow);
            box-shadow: 0 0 0 0.2rem rgba(249, 168, 38, 0.25);
        }
        .addons-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 10px;
        }
        .addon-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--white);
        }
        .addon-card:hover, .addon-card.selected {
            border-color: var(--primary-yellow);
            background: rgba(249, 168, 38, 0.05);
            transform: translateY(-2px);
        }
        .addon-card.selected {
            background: rgba(249, 168, 38, 0.1);
        }
        .addon-icon {
            font-size: 1.5rem;
            color: var(--primary-yellow);
            margin-bottom: 10px;
        }
        .addon-price {
            font-weight: 600;
            color: var(--charcoal);
            margin-top: 5px;
        }
        .estimate-result{
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 16px;
            padding: 30px;
            margin-top: 30px;
            text-align: center;
            display: none;
            animation: fadeInUp 0.6s ease-out;
            border: 1px solid rgba(0,0,0,0.05);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .estimate-amount{
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary-yellow);
            margin: 10px 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cost-breakdown {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .cost-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f1f1f1;
        }
        .cost-item:last-child {
            border-bottom: none;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--charcoal);
        }
        .disclaimer {
            font-size: 0.85rem;
            color: #666;
            text-align: center;
            margin-top: 20px;
            line-height: 1.5;
        }

        .package-card{
            background:var(--white);border-radius:16px;overflow:hidden;
            box-shadow:0 8px 25px rgba(0,0,0,.06);transition:transform .3s,box-shadow .3s;
            height:100%;display:flex;flex-direction:column;
        }
        .package-card:hover{
            transform:translateY(-10px);box-shadow:0 20px 40px rgba(0,0,0,.12);
        }
        .package-header{
            background:var(--charcoal);color:var(--white);padding:28px 20px;text-align:center;
        }
        .package-title{margin:0;font-size:1.6rem;}
        .package-body{padding:30px;flex:1;display:flex;flex-direction:column;}
        .package-price{font-size:2.1rem;color:var(--primary-yellow);font-weight:700;margin-bottom:12px;}
        .package-desc{color:#555;font-size:.95rem;margin-bottom:20px;flex:1;}
        .accordion-button{font-weight:600;padding:14px 20px;}
        .accordion-button:not(.collapsed){
            background:var(--primary-yellow);color:var(--charcoal);box-shadow:none;
        }

        /* Enhanced Gallery Section */
        .gallery-section{
            padding: 90px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .gallery-title{
            margin-bottom: 50px;
        }
        .project-gallery-swiper, .services-gallery-swiper, .blog-gallery-swiper {
            padding: 30px 10px;
            border-radius: 20px;
            overflow: hidden;
        }
        .gallery-slide{
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            height: 450px;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .gallery-slide:hover{
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .gallery-slide img{
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.4s ease;
        }
        .gallery-slide:hover img {
            transform: scale(1.05);
        }
        .gallery-overlay{
            position:absolute;
            inset:0;
            background: linear-gradient(transparent 40%, rgba(26,26,26,0.9));
            display:flex;
            flex-direction:column;
            justify-content:flex-end;
            padding: 35px;
            color:var(--white);
            opacity: 0;
            transition: all 0.3s ease;
        }
        .gallery-slide:hover .gallery-overlay {
            opacity: 1;
        }
        .gallery-overlay h3{
            font-size:1.5rem;
            margin:0 0 8px;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        .gallery-overlay p{
            font-size:1rem;
            margin:0;
            opacity:0;
            transform: translateY(20px);
            transition: all 0.3s ease 0.1s;
        }
        .gallery-slide:hover .gallery-overlay h3,
        .gallery-slide:hover .gallery-overlay p {
            transform: translateY(0);
            opacity: 1;
        }
        .blog-category-badge{
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
        .swiper-button-next,
        .swiper-button-prev {
            background: var(--white);
            width: 60px;
            height: 60px;
            border-radius: 50%;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            color: var(--charcoal);
            transition: all 0.3s ease;
        }
        .swiper-button-next:after,
        .swiper-button-prev:after {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background: var(--primary-yellow);
            transform: scale(1.1);
        }
        .swiper-pagination-bullet {
            background: #ccc;
            opacity: 1;
            width: 12px;
            height: 12px;
        }
        .swiper-pagination-bullet-active {
            background: var(--primary-yellow);
        }

        .testimonial-card{
            background:var(--white);border-radius:12px;padding:28px;
            box-shadow:0 6px 20px rgba(0,0,0,.05);height:100%;transition:transform .3s;
        }
        .testimonial-card:hover{transform:translateY(-6px);}
        .testimonial-text{font-style:italic;color:#555;line-height:1.8;margin-bottom:20px;}
        .testimonial-author{display:flex;align-items:center;}
        .testimonial-author img{width:55px;height:55px;border-radius:50%;margin-right:15px;}
        .testimonial-name{font-weight:600;margin:0;font-size:1.1rem;}
        .testimonial-project{color:#777;font-size:.9rem;}

        .sidebar{
            background:var(--light-gray);border-radius:12px;padding:28px;margin-bottom:30px;
        }
        .sidebar-title{
            font-size:1.25rem;margin-bottom:20px;padding-bottom:10px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;
        }
        .search-box{position:relative;margin-bottom:25px;}
        .search-box input{
            width:100%;padding:12px 45px 12px 18px;border-radius:50px;border:1px solid #ddd;
            font-size:.95rem;
        }
        .search-box button{
            position:absolute;right:8px;top:8px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);width:36px;height:36px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;font-size:1rem;
        }
        .category-list{list-style:none;padding:0;margin:0;}
        .category-list a{
            display:flex;justify-content:space-between;align-items:center;
            padding:10px 0;color:var(--charcoal);text-decoration:none;
            border-bottom:1px solid #eee;transition:color .3s;
        }
        .category-list a:hover,.category-list a.active{
            color:var(--primary-yellow);font-weight:600;
        }
        .popular-package{
            display:flex;gap:12px;margin-bottom:18px;padding-bottom:18px;
            border-bottom:1px solid #eee;
        }
        .popular-package:last-child{margin-bottom:0;padding-bottom:0;border:none;}
        .popular-package-image{
            width:60px;height:60px;border-radius:8px;overflow:hidden;flex-shrink:0;
        }
        .popular-package-image img{width:100%;height:100%;object-fit:cover;}
        .popular-package-title a{
            color:var(--charcoal);font-weight:500;text-decoration:none;transition:color .3s;
        }
        .popular-package-title a:hover{color:var(--primary-yellow);}

        .floating-buttons{
            position:fixed;bottom:30px;right:30px;z-index:1000&display:flex;flex-direction:column;gap:15px;
        }
        .floating-btn{
            width:60px;height:60px;border-radius:50%;display:flex;
            align-items:center;justify-content:center;color:var(--white);
            font-size:1.6rem;box-shadow:0 6px 20px rgba(0,0,0,.2);transition:all .3s;
        }
        .floating-btn:hover{transform:translateY(-5px);box-shadow:0 10px 25px rgba(0,0,0,.3);}
        .whatsapp-btn{background:#25D366;}
        .call-btn{background:var(--primary-yellow);color:var(--charcoal);}

        .cta-section{
            background:linear-gradient(135deg,var(--charcoal) 0%,var(--dark) 100%);
            color:var(--white);padding:90px 0;text-align:center;
        }
        .cta-section h2{color:var(--white);margin-bottom:1.5rem;}

        .form-check-input:checked {
            background-color: var(--primary-yellow);
            border-color: var(--primary-yellow);
        }

        @media (max-width:992px){
            .hero-banner{height:80vh;}
            .hero-title{font-size:2.8rem;}
            .section-padding{padding:70px 0;}
            .gallery-slide{height:350px;}
            .estimator-box {padding: 40px 30px;}
        }
        @media (max-width:576px){
            .hero-title{font-size:2.3rem;}
            .hero-subtitle{font-size:1.1rem;}
            .floating-buttons{bottom:20px;right:20px;gap:12px;}
            .floating-btn{width:50px;height:50px;font-size:1.3rem;}
            .gallery-slide{height:280px;}
            .gallery-overlay{padding:20px;}
            .estimator-box {padding: 30px 20px;}
            .estimate-amount {font-size: 2.2rem;}
            .addons-grid {grid-template-columns: 1fr;}
        }
    </style>
</head>
<body>

<!-- ====================== HERO ====================== -->
<section class="hero-banner">
    <div class="container position-relative z-3">
        <h1 class="hero-title">Build Your Dream Home with Confidence</h1>
        <p class="hero-subtitle">Modern designs. Transparent pricing. Trusted craftsmanship.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="#packages" class="btn btn-primary btn-lg">View Packages</a>
            <a href="#estimator" class="btn btn-outline-light btn-lg">Get Estimate</a>
        </div>
    </div>
</section>

<main>

    

    <!-- ==== OUR SERVICES SLIDESHOW ==== -->
    <section class="gallery-section">
        <div class="container">
            <h2 class="section-title gallery-title">Our Construction Services</h2>
            <div class="swiper services-gallery-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($featured_services as $srv): 
                        $img_src = !empty($srv['cover_image']) 
                            ? $base_path . sanitizeOutput($srv['cover_image'])
                            : $service_placeholder;
                        $slug = !empty($srv['slug']) 
                            ? sanitizeOutput($srv['slug']) 
                            : strtolower(preg_replace('/[^a-z0-9]+/', '-', $srv['title']));
                    ?>
                    <div class="swiper-slide">
                        <a href="<?= $base_path ?>/service-info.php?slug=<?= urlencode($slug) ?>" class="gallery-slide">
                            <img src="<?= $img_src ?>" 
                                 alt="<?= sanitizeOutput($srv['title']) ?>" loading="lazy"
                                 onerror="this.src='<?= $service_placeholder ?>'">
                            <div class="gallery-overlay">
                                <h3><?= sanitizeOutput($srv['title']) ?></h3>
                                <p><?= sanitizeOutput($srv['short_desc']) ?>...</p>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="text-center mt-4">
                <a href="<?= $base_path ?>/services.php" class="btn btn-outline-primary btn-lg">
                    View All Services
                </a>
            </div>
        </div>
    </section>

    <!-- ==== GALLERY SLIDESHOW (Latest 8 Projects) ==== -->
    <section class="gallery-section">
        <div class="container">
            <h2 class="section-title gallery-title">Our Latest Projects</h2>
            <div class="swiper project-gallery-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($featured_projects as $proj): 
                        $img_src = $proj['image_path'] 
                            ? $assets_path . '/' . $proj['image_path']
                            : $placeholder;
                    ?>
                    <div class="swiper-slide">
                        <a href="<?= $base_path ?>/project-info.php?id=<?= (int)$proj['id'] ?>" class="gallery-slide">
                            <img src="<?= $img_src ?>" 
                                 alt="<?= sanitizeOutput($proj['title']) ?>" loading="lazy"
                                 onerror="this.src='<?= $placeholder ?>'">
                            <div class="gallery-overlay">
                                <h3><?= sanitizeOutput($proj['title']) ?></h3>
                                <p><?= sanitizeOutput($proj['location']) ?></p>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- ==== PACKAGES + SIDEBAR ==== -->
    <section id="packages" class="section-padding">
        <div class="container">
            <div class="row g-5">

                <!-- MAIN: Packages -->
                <div class="col-lg-8">
                    <div class="text-center mb-5">
                        <h2 class="section-title">Our Construction Packages</h2>
                        <p class="lead">Comprehensive inclusions with full transparency</p>
                    </div>

                    <div class="row g-4">
                        <?php foreach ($packages as $pkg): ?>
                            <div class="col-md-6">
                                <div class="package-card">
                                    <div class="package-header">
                                        <h3 class="package-title"><?= sanitizeOutput($pkg['title']) ?></h3>
                                    </div>
                                    <div class="package-body">
                                        <div class="package-price">₹<?= number_format((float)$pkg['price_per_sqft']) ?>/sq.ft</div>
                                        <p class="package-desc"><?= sanitizeOutput($pkg['description']) ?></p>

                                        <?php if (!empty($package_sections[$pkg['id']])): ?>
                                            <div class="accordion" id="accordion<?= $pkg['id'] ?>">
                                                <?php foreach ($package_sections[$pkg['id']] as $index => $sec): 
                                                    $collapseId = "collapse{$pkg['id']}_{$index}";
                                                ?>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?>" 
                                                                    type="button" data-bs-toggle="collapse" 
                                                                    data-bs-target="#<?= $collapseId ?>">
                                                                <?= sanitizeOutput($sec['title']) ?>
                                                            </button>
                                                        </h2>
                                                        <div id="<?= $collapseId ?>" 
                                                             class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" 
                                                             data-bs-parent="#accordion<?= $pkg['id'] ?>">
                                                            <div class="accordion-body">
                                                                <?= nl2br(sanitizeOutput($sec['content'])) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <a href="<?= $base_path ?>/packages.php?id=<?= (int)$pkg['id'] ?>" 
                                           class="btn btn-outline-primary w-100 mt-3">
                                            View Full Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="text-center mt-5">
                        <a href="<?= $base_path ?>/packages.php" class="btn btn-primary btn-lg">
                            View All Packages
                        </a>
                    </div>
                </div>

                <!-- ASIDE: Sidebar -->
                <aside class="col-lg-4">
                    <div class="sticky-top" style="top:2rem;">

                        <!-- SEARCH -->
                        <div class="sidebar">
                            <h3 class="sidebar-title">Search Packages</h3>
                            <form action="<?= $base_path ?>/packages.php" method="get" class="search-box">
                                <input type="text" name="search" placeholder="Search packages..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                                <button type="submit">Search</button>
                            </form>
                        </div>

                        <!-- CATEGORIES -->
                        <div class="sidebar">
                            <h3 class="sidebar-title">Categories</h3>
                            <ul class="category-list">
                                <li><a href="<?= $base_path ?>/packages.php" class="<?= empty($_GET['category']) ? 'active' : '' ?>">
                                    <span>All Packages</span>
                                    <span class="badge bg-dark text-white"><?= $total_packages ?></span>
                                </a></li>
                                <?php foreach ($categories as $c): ?>
                                    <li><a href="<?= $base_path ?>/packages.php?category=<?= urlencode($c['cat']) ?>"
                                           class="<?= ($_GET['category'] ?? '') === $c['cat'] ? 'active' : '' ?>">
                                        <span><?= ucfirst(sanitizeOutput($c['cat'])) ?></span>
                                        <span class="badge bg-dark text-white"><?= $c['cnt'] ?></span>
                                    </a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    </div>
                </aside>

            </div>
        </div>
    </section>

    <!-- ==== ENHANCED PROFESSIONAL ESTIMATOR ==== -->
    <section id="estimator" class="section-padding estimator-section">
        <div class="container">
            <div class="estimator-box">
                <div class="estimator-header">
                    <div class="estimator-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="estimator-title">Professional Cost Estimator</h3>
                    <p class="estimator-subtitle">Get an accurate construction cost breakdown in real-time</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Built-up Area (sq.ft)</label>
                        <input type="number" id="squareFootage" class="form-control" placeholder="e.g. 1500" min="500" value="1500">
                        <small class="text-muted">Minimum 500 sq.ft</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Construction Package</label>
                        <select id="packageType" class="form-select">
                            <option value="">Select Package</option>
                            <?php foreach ($packages as $pkg): ?>
                                <option value="<?= (float)$pkg['price_per_sqft'] ?>" 
                                        data-name="<?= sanitizeOutput($pkg['title']) ?>">
                                    <?= sanitizeOutput($pkg['title']) ?> 
                                    (₹<?= number_format((float)$pkg['price_per_sqft']) ?>/sq.ft)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="form-label fw-semibold mb-3">Premium Add-ons</label>
                    <div class="addons-grid">
                        <div class="addon-card" data-value="150000" data-name="Solar Panels">
                            <div class="addon-icon">
                                <i class="fas fa-solar-panel"></i>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="addSolar" value="150000">
                                <label class="form-check-label fw-semibold" for="addSolar">Solar Panels</label>
                            </div>
                            <div class="addon-price">+₹1,50,000</div>
                        </div>
                        <div class="addon-card" data-value="80000" data-name="Landscaping">
                            <div class="addon-icon">
                                <i class="fas fa-tree"></i>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="addGarden" value="80000">
                                <label class="form-check-label fw-semibold" for="addGarden">Landscaping</label>
                            </div>
                            <div class="addon-price">+₹80,000</div>
                        </div>
                        <div class="addon-card" data-value="120000" data-name="Smart Home">
                            <div class="addon-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="addSmart" value="120000">
                                <label class="form-check-label fw-semibold" for="addSmart">Smart Home</label>
                            </div>
                            <div class="addon-price">+₹1,20,000</div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary w-100 mt-4 py-3 fw-bold fs-5" onclick="calculateEstimate()">
                    <i class="fas fa-calculator me-2"></i>Calculate Detailed Estimate
                </button>

                <!-- Result -->
                <div class="estimate-result mt-4" id="estimateResult" style="display:none;">
                    <div class="text-center mb-4">
                        <h4 class="mb-2">Your Construction Estimate</h4>
                        <div class="estimate-amount" id="estimateAmount">₹0</div>
                        <div class="text-success fw-bold fs-6" id="packageName"></div>
                    </div>

                    <div class="cost-breakdown">
                        <h6 class="fw-bold mb-3 text-center">Cost Breakdown</h6>
                        <div class="cost-item">
                            <span>Base Construction Cost</span>
                            <span class="fw-semibold" id="baseCost">₹0</span>
                        </div>
                        <div class="cost-item">
                            <span>Selected Add-ons</span>
                            <span class="fw-semibold" id="addonCost">₹0</span>
                        </div>
                        <div class="cost-item">
                            <span>GST (18%)</span>
                            <span class="fw-semibold" id="gstCost">₹0</span>
                        </div>
                        <div class="cost-item">
                            <span class="text-primary fw-bold">Total Estimated Cost</span>
                            <span class="text-primary fw-bold" id="totalCost">₹0</span>
                        </div>
                    </div>

                    <div class="disclaimer">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Note:</strong> This is a preliminary estimate. Final cost may vary ±10% based on site conditions, material availability, and design modifications. Contact us for a detailed site assessment.
                    </div>

                    <div class="d-grid gap-2 mt-4">
                        <a href="<?= $base_path ?>/contact.php?estimate=true" class="btn btn-primary btn-lg">
                            <i class="fas fa-calendar-check me-2"></i>Schedule Free Site Visit
                        </a>
                        <a href="tel:+919075956483" class="btn btn-outline-primary">
                            <i class="fas fa-phone me-2"></i>Call for Consultation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==== LATEST BLOG ARTICLES SLIDESHOW (NEW!) ==== -->
    <section class="gallery-section bg-white">
        <div class="container">
            <h2 class="section-title gallery-title">Latest Blog & Insights</h2>
            <div class="swiper blog-gallery-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($featured_blogs as $blog): 
                        $img_src = !empty($blog['featured_image']) 
                            ? $assets_path . '/' . sanitizeOutput($blog['featured_image'])
                            : $blog_placeholder;
                        $slug = sanitizeOutput($blog['slug']);
                    ?>
                    <div class="swiper-slide">
                        <a href="<?= $base_path ?>/blog-detail.php?slug=<?= urlencode($slug) ?>" class="gallery-slide">
                            <img src="<?= $img_src ?>" 
                                 alt="<?= sanitizeOutput($blog['title']) ?>" loading="lazy"
                                 onerror="this.src='<?= $blog_placeholder ?>'">
                            <?php if ($blog['category']): ?>
                                <div class="blog-category-badge"><?= sanitizeOutput($blog['category']) ?></div>
                            <?php endif; ?>
                            <div class="gallery-overlay">
                                <h3><?= sanitizeOutput($blog['title']) ?></h3>
                                <p><?= sanitizeOutput(substr(strip_tags($blog['excerpt']), 0, 120)) ?>...</p>
                                <small class="text-light opacity-75">
                                    <i class="fas fa-calendar me-1"></i> <?= date('d M Y', strtotime($blog['created_at'])) ?>
                                </small>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="text-center mt-4">
                <a href="<?= $base_path ?>/blog.php" class="btn btn-outline-primary btn-lg">
                    View All Articles
                </a>
            </div>
        </div>
    </section>

    <!-- ==== TESTIMONIALS ==== -->
    <section class="section-padding bg-light-alt">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">What Our Clients Say</h2>
                <p class="lead">Trusted by hundreds of happy homeowners</p>
            </div>
            <div class="row g-4">
                <?php foreach ($testimonials as $t): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="testimonial-card">
                            <p class="testimonial-text">"<?= sanitizeOutput($t['text']) ?>"</p>
                            <div class="testimonial-author">
                                <img src="https://randomuser.me/api/portraits/men/<?= rand(1,99) ?>.jpg" 
                                     alt="<?= sanitizeOutput($t['client_name']) ?>">
                                <div>
                                    <h6 class="testimonial-name"><?= sanitizeOutput($t['client_name']) ?></h6>
                                    <?php if ($t['project_title']): ?>
                                        <small class="testimonial-project"><?= sanitizeOutput($t['project_title']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<!-- ====================== FLOATING BUTTONS ====================== -->
<div class="floating-buttons">
    <a href="https://wa.me/+919075956483" target="_blank" class="floating-btn whatsapp-btn" title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <a href="tel:+919075956483" class="floating-btn call-btn" title="Call Us">
        <i class="fas fa-phone"></i>
    </a>
</div>

<!-- ====================== CTA ====================== -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-4">Ready to Build Your Dream Home?</h2>
                <p class="lead mb-4">Let's discuss your vision. Get a free consultation today.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= $base_path ?>/contact.php" class="btn btn-primary btn-lg">
                        Contact Us
                    </a>
                    <a href="<?= $base_path ?>/projects.php" class="btn btn-outline-light btn-lg">
                        View All Projects
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Your Custom JS -->
<script>
    // Projects Gallery Swiper
    const projectSwiper = new Swiper('.project-gallery-swiper', {
        loop: true,
        autoplay: { delay: 5000, disableOnInteraction: false },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        pagination: { el: '.swiper-pagination', clickable: true },
        spaceBetween: 30,
        breakpoints: {
            320: { slidesPerView: 1 },
            640: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
        }
    });

    // Services Gallery Swiper
    const servicesSwiper = new Swiper('.services-gallery-swiper', {
        loop: true,
        autoplay: { delay: 4500, disableOnInteraction: false },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        pagination: { el: '.swiper-pagination', clickable: true },
        spaceBetween: 30,
        breakpoints: {
            320: { slidesPerView: 1 },
            640: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
        }
    });

    // Blog Gallery Swiper
    const blogSwiper = new Swiper('.blog-gallery-swiper', {
        loop: true,
        autoplay: { delay: 4000, disableOnInteraction: false },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        pagination: { el: '.swiper-pagination', clickable: true },
        spaceBetween: 30,
        breakpoints: {
            320: { slidesPerView: 1 },
            640: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            1024: { slidesPerView: 3 }
        }
    });

    // Estimate Calculator
    function calculateEstimate() {
        const sqft = parseFloat(document.getElementById('squareFootage').value) || 0;
        const select = document.getElementById('packageType');
        const rate = parseFloat(select.value) || 0;
        const pkgName = select.options[select.selectedIndex]?.dataset.name || '';

        const addons = Array.from(document.querySelectorAll('#addSolar, #addGarden, #addSmart'))
            .filter(cb => cb.checked)
            .reduce((sum, cb) => sum + parseFloat(cb.value), 0);

        if (sqft < 500) {
            alert('Please enter at least 500 sq.ft');
            return;
        }
        if (!rate) {
            alert('Please select a package');
            return;
        }

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
        document.getElementById('estimateResult').style.display = 'block';
        
        document.getElementById('estimateResult').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Addon card click
    document.querySelectorAll('.addon-card').forEach(card => {
        card.addEventListener('click', function() {
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            this.classList.toggle('selected', checkbox.checked);
            calculateEstimate();
        });
    });

    // Auto-calculate
    document.getElementById('squareFootage').addEventListener('input', calculateEstimate);
    document.getElementById('packageType').addEventListener('change', calculateEstimate);
    document.querySelectorAll('#addSolar, #addGarden, #addSmart').forEach(cb => {
        cb.add来到了这里

        cb.addEventListener('change', function() {
            const card = this.closest('.addon-card');
            if (card) card.classList.toggle('selected', this.checked);
            calculateEstimate();
        });
    });
</script>

</body>
</html>