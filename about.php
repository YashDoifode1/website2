<?php
/**
 * About Page – Grand Jyothi Construction
 * 100% aligned with the rest of the site
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'About Us | Grand Jyothi Construction';

/* ---------- Sidebar data ---------- */
$categories = executeQuery(
    "SELECT SUBSTRING_INDEX(title,' ',1) AS cat, COUNT(*) AS cnt
     FROM packages WHERE is_active=1 GROUP BY cat ORDER BY cat"
)->fetchAll();

$total_packages = executeQuery("SELECT COUNT(*) FROM packages WHERE is_active=1")->fetchColumn();

$popular_packages = executeQuery(
    "SELECT title, price_per_sqft FROM packages
     WHERE is_active=1 ORDER BY display_order ASC LIMIT 3"
)->fetchAll();

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

    <style>
        :root{
            --primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;
            --light-gray:#f8f9fa;--medium-gray:#e9ecef;
        }
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);line-height:1.6;}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}

        /* ==== BUTTONS ==== */
        .btn-primary{background:var(--primary-yellow);border-color:var(--primary-yellow);
            color:var(--charcoal);font-weight:600;padding:12px 30px;border-radius:8px;}
        .btn-primary:hover{background:#e89a1f;border-color:#e89a1f;color:var(--charcoal);}
        .btn-outline-light{border:2px solid rgba(255,255,255,.3);color:var(--white);
            padding:12px 30px;border-radius:30px;}
        .btn-outline-light:hover{background:rgba(255,255,255,.1);border-color:var(--white);}

        /* ==== HERO ==== */
        .about-banner{height:500px;background:linear-gradient(rgba(26,26,26,.6),rgba(26,26,26,.6)),
            url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
            center/cover no-repeat;display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;}
        .about-banner::before{content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.1) 0%,transparent 70%);}
        .banner-title{font-size:3rem;margin-bottom:20px;line-height:1.2;}
        .banner-subtitle{font-size:1.2rem;opacity:.9;}

        /* ==== BREADCRUMB ==== */
        .breadcrumb{background:transparent;padding:0;margin-bottom:20px;}
        .breadcrumb-item a{color:rgba(255,255,255,.8);text-decoration:none;}
        .breadcrumb-item.active{color:var(--primary-yellow);}

        /* ==== CONTENT ==== */
        .about-section{padding:80px 0;}
        .section-title{font-size:1.8rem;margin-bottom:30px;padding-bottom:15px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;position:relative;}
        .section-title::after{content:'';position:absolute;bottom:-15px;left:0;
            width:80px;height:4px;background:var(--primary-yellow);}

        /* ==== STATS ==== */
        .stats-container{display:flex;justify-content:space-around;flex-wrap:wrap;margin-top:50px;}
        .stat-item{text-align:center;padding:20px;flex:1;min-width:180px;}
        .stat-number{font-size:3rem;font-weight:700;color:var(--primary-yellow);margin-bottom:10px;}
        .stat-label{font-size:1rem;color:#666;}

        /* ==== MISSION / VISION ==== */
        .mission-vision-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
            gap:40px;}
        .mission-card,.vision-card{background:var(--white);border-radius:10px;padding:40px;
            box-shadow:0 10px 30px rgba(0,0,0,.08);text-align:center;transition:transform .3s;}
        .mission-card:hover,.vision-card:hover{transform:translateY(-10px);}
        .mission-icon,.vision-icon{font-size:3.5rem;color:var(--primary-yellow);margin-bottom:25px;}

        /* ==== WHY CHOOSE ==== */
        .point-item{display:flex;align-items:flex-start;margin-bottom:30px;transition:transform .3s;}
        .point-item:hover{transform:translateX(5px);}
        .point-icon{width:40px;height:40px;display:flex;align-items:center;justify-content:center;
            color:var(--primary-yellow);margin-right:15px;font-size:1.4rem;}

        /* ==== SIDEBAR ==== */
        .sidebar{background:var(--light-gray);border-radius:10px;padding:30px;margin-bottom:30px;}
        .sidebar-title{font-size:1.2rem;margin-bottom:20px;padding-bottom:10px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;}
        .search-box{position:relative;margin-bottom:30px;}
        .search-box input{width:100%;padding:12px 40px 12px 15px;border-radius:50px;border:1px solid #ddd;}
        .search-box button{position:absolute;right:8px;top:8px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);width:36px;height:36px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;}
        .category-list{list-style:none;padding:0;}
        .category-list a{display:flex;justify-content:space-between;align-items:center;
            padding:10px 0;color:var(--charcoal);text-decoration:none;border-bottom:1px solid #eee;}
        .category-list a:hover,.category-list a.active{color:var(--primary-yellow);font-weight:600;}
        .popular-package{display:flex;gap:12px;margin-bottom:15px;padding-bottom:15px;
            border-bottom:1px solid #eee;}
        .popular-package:last-child{margin-bottom:0;padding-bottom:0;border:none;}
        .popular-package-image{width:60px;height:60px;border-radius:8px;overflow:hidden;flex-shrink:0;}
        .popular-package-image img{width:100%;height:100%;object-fit:cover;}
        .popular-package-title a{color:var(--charcoal);font-weight:500;text-decoration:none;}
        .popular-package-title a:hover{color:var(--primary-yellow);}

        /* ==== FLOATING BUTTONS ==== */
        .floating-buttons{position:fixed;bottom:30px;right:30px;z-index:1000;}
        .floating-btn{width:60px;height:60px;border-radius:50%;display:flex;
            align-items:center;justify-content:center;color:var(--white);
            font-size:1.5rem;margin-bottom:15px;box-shadow:0 5px 15px rgba(0,0,0,.2);
            transition:all .3s;}
        .floating-btn:hover{transform:translateY(-5px);}
        .whatsapp-btn{background:#25D366;}
        .call-btn{background:var(--primary-yellow);color:var(--charcoal);}

        /* ==== CTA ==== */
        .cta-section{background:linear-gradient(135deg,var(--charcoal) 0%,#2d2d2d 100%);
            color:var(--white);padding:80px 0;text-align:center;}
        .cta-section h2{color:var(--white);margin-bottom:1.5rem;}

        /* ==== RESPONSIVE ==== */
        @media (max-width:992px){
            .about-banner{height:400px;padding:40px 0;}
            .banner-title{font-size:2.2rem;}
            .about-section .row{flex-direction:column-reverse;}
        }
        @media (max-width:576px){
            .floating-buttons{bottom:20px;right:20px;}
            .floating-btn{width:50px;height:50px;font-size:1.2rem;}
        }
    </style>
</head>
<body>

<!-- ====================== HERO ====================== -->
<section class="about-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/constructioninnagpur/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About</li>
            </ol>
        </nav>
        <h1 class="banner-title">Crafting Excellence Since 2005</h1>
        <p class="banner-subtitle">Your Trusted Partner for Premium Construction Solutions in Nagpur</p>
    </div>
</section>

<!-- ====================== MAIN + ASIDE ====================== -->
<main class="about-section bg-light">
    <div class="container">
        <div class="row g-5">

            <!-- ==== MAIN CONTENT ==== -->
            <div class="col-lg-8">

                <!-- Company Overview -->
                <section class="mb-5">
                    <h2 class="section-title">Company Overview</h2>
                    <p>Founded in 2005, <strong>Grand Jyothi Construction</strong> has evolved from a local contractor into Nagpur's premier construction partner. Our journey is defined by an unwavering commitment to quality, innovation, and client satisfaction that has stood the test of time.</p>
                    <p>Over nearly two decades, we've transformed visions into reality through hundreds of residential, commercial, and industrial projects across Maharashtra. Our multidisciplinary team of architects, engineers, and craftsmen brings unparalleled expertise to every project, ensuring exceptional results that endure.</p>
                    <p>What sets us apart is our personalized approach. From initial consultation to final handover, we collaborate closely with clients to ensure every detail reflects their unique vision while meeting our exacting standards of excellence.</p>

                    <div class="stats-container">
                        <div class="stat-item">
                            <div class="stat-number">18+</div>
                            <div class="stat-label">Years Experience</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Projects Completed</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">450+</div>
                            <div class="stat-label">Happy Clients</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Quality Commitment</div>
                        </div>
                    </div>
                </section>

                <!-- Mission & Vision -->
                <section class="mb-5">
                    <h2 class="section-title">Our Mission & Vision</h2>
                    <div class="mission-vision-container">
                        <div class="mission-card">
                            <h3>Our Mission</h3>
                            <p>To redefine construction excellence by delivering innovative, sustainable solutions that exceed client expectations. We combine cutting-edge techniques with time-honored craftsmanship to build structures that inspire confidence and stand as testaments to quality.</p>
                        </div>
                        <div class="vision-card">
                            <h3>Our Vision</h3>
                            <p>To be Central India's most trusted construction partner, recognized for setting industry benchmarks in quality, reliability, and client satisfaction. We envision shaping sustainable communities through transformative construction practices.</p>
                        </div>
                    </div>
                </section>

                <!-- Why Choose Us -->
                <section>
                    <h2 class="section-title">Why Partner With Grand Jyothi Construction?</h2>
                    <div class="why-choose-points">
                        <div class="point-item">
                            <div class="point-icon"><i class="fas fa-award"></i></div>
                            <div>
                                <h4 class="h6 fw-bold">18+ Years of Excellence</h4>
                                <p class="small mb-0">Proven expertise in residential and commercial construction across Maharashtra.</p>
                            </div>
                        </div>
                        <div class="point-item">
                            <div class="point-icon"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <h4 class="h6 fw-bold">Quality Assurance</h4>
                                <p class="small mb-0">Rigorous quality control processes ensure superior craftsmanship and materials.</p>
                            </div>
                        </div>
                        <div class="point-item">
                            <div class="point-icon"><i class="fas fa-clock"></i></div>
                            <div>
                                <h4 class="h6 fw-bold">Timely Completion</h4>
                                <p class="small mb-0">Efficient project management delivers projects on schedule, every time.</p>
                            </div>
                        </div>
                        <div class="point-item">
                            <div class="point-icon"><i class="fas fa-dollar-sign"></i></div>
                            <div>
                                <h4 class="h6 fw-bold">Transparent Pricing</h4>
                                <p class="small mb-0">Honest quotes with no hidden costs - exceptional value for your investment.</p>
                            </div>
                        </div>
                        <div class="point-item">
                            <div class="point-icon"><i class="fas fa-users"></i></div>
                            <div>
                                <h4 class="h6 fw-bold">Expert Team</h4>
                                <p class="small mb-0">Certified professionals dedicated to realizing your construction vision.</p>
                            </div>
                        </div>
                        <div class="point-item">
                            <div class="point-icon"><i class="fas fa-headset"></i></div>
                            <div>
                                <h4 class="h6 fw-bold">Dedicated Support</h4>
                                <p class="small mb-0">Comprehensive after-sales service and 10-year structural warranty.</p>
                            </div>
                        </div>
                    </div>
                </section>

            </div>

            <!-- ==== ASIDE: Sidebar ==== -->
            <aside class="col-lg-4">
                <div class="sticky-top" style="top:2rem;">

                    <!-- SEARCH -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Search Packages</h3>
                        <form action="/constructioninnagpur/packages.php" method="get" class="search-box">
                            <input type="text" name="search" placeholder="Search packages..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- CATEGORIES -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Categories</h3>
                        <ul class="category-list">
                            <li><a href="/constructioninnagpur/packages.php" class="<?= empty($_GET['category']) ? 'active' : '' ?>">
                                <span>All Packages</span>
                                <span class="badge bg-dark text-white"><?= $total_packages ?></span>
                            </a></li>
                            <?php foreach ($categories as $c): ?>
                                <li><a href="/constructioninnagpur/packages.php?category=<?= urlencode($c['cat']) ?>"
                                       class="<?= ($_GET['category'] ?? '') === $c['cat'] ? 'active' : '' ?>">
                                    <span><?= ucfirst(sanitizeOutput($c['cat'])) ?></span>
                                    <span class="badge bg-dark text-white"><?= $c['cnt'] ?></span>
                                </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- POPULAR -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Popular Packages</h3>
                        <?php foreach ($popular_packages as $p): ?>
                            <div class="popular-package">
                                <div class="popular-package-image">
                                    <img src="https://via.placeholder.com/60" alt="">
                                </div>
                                <div>
                                    <div class="popular-package-title">
                                        <a href="/constructioninnagpur/select-plan.php?plan=<?= urlencode($p['title']) ?>">
                                            <?= sanitizeOutput($p['title']) ?>
                                        </a>
                                    </div>
                                    <small class="text-muted">
                                        <?php if ($p['price_per_sqft'] > 0): ?>
                                            ₹<?= number_format((float)$p['price_per_sqft']) ?>/sq.ft
                                        <?php else: ?> Custom <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </aside>

        </div>
    </div>
</main>

<!-- ====================== FLOATING BUTTONS ====================== -->
<div class="floating-buttons">
    <a href="https://wa.me/919876543210" target="_blank" class="floating-btn whatsapp-btn" title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <a href="tel:+919876543210" class="floating-btn call-btn" title="Call Us">
        <i class="fas fa-phone"></i>
    </a>
</div>

<!-- ====================== CTA ====================== -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-4">Ready to Build Your Dream?</h2>
                <p class="lead mb-4">Let’s discuss your vision and create something extraordinary together</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="/constructioninnagpur/contact.php" class="btn btn-primary btn-lg">
                        Get Free Consultation
                    </a>
                    <a href="/constructioninnagpur/packages.php" class="btn btn-outline-light btn-lg">
                        View All Packages
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>