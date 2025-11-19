<?php
/**
 * Testimonials Page – Rakhi Construction & Consultancy
 * 100% aligned with the rest of the site
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config.php';

$page_title = 'Client Testimonials | Rakhi Construction & Consultancy';

// ---------- 1. Fetch testimonials ----------
$sql = "SELECT t.*, p.title AS project_title, p.location AS project_location 
        FROM testimonials t 
        LEFT JOIN projects p ON t.project_id = p.id 
        ORDER BY t.created_at DESC";
$stmt = executeQuery($sql);
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ---------- 2. Sidebar data ----------
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
        .testimonials-banner{height:500px;background:linear-gradient(rgba(26,26,26,.6),rgba(26,26,26,.6)),
            url('https://images.unsplash.com/photo-1581093450021-4a7360e9a6b5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
            center/cover no-repeat;display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;}
        .testimonials-banner::before{content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.1) 0%,transparent 70%);}
        .banner-title{font-size:3rem;margin-bottom:20px;line-height:1.2;}
        .banner-subtitle{font-size:1.2rem;opacity:.9;}

        /* ==== BREADCRUMB ==== */
        .breadcrumb{background:transparent;padding:0;margin-bottom:20px;}
        .breadcrumb-item a{color:rgba(255,255,255,.8);text-decoration:none;}
        .breadcrumb-item.active{color:var(--primary-yellow);}

        /* ==== CONTENT ==== */
        .testimonials-section{padding:80px 0;}
        .section-title{font-size:1.8rem;margin-bottom:30px;padding-bottom:15px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;position:relative;}
        .section-title::after{content:'';position:absolute;bottom:-15px;left:0;
            width:80px;height:4px;background:var(--primary-yellow);}

        /* ==== TESTIMONIALS GRID ==== */
        .testimonials-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
            gap:30px;}
        .testimonial-card{background:var(--white);border-radius:10px;padding:30px;
            box-shadow:0 10px 30px rgba(0,0,0,.08);transition:transform .3s,box-shadow .3s;
            display:flex;flex-direction:column;height:100%;}
        .testimonial-card:hover{transform:translateY(-10px);box-shadow:0 20px 40px rgba(0,0,0,.12);}
        .testimonial-content{flex-grow:1;margin-bottom:20px;}
        .testimonial-icon{color:var(--primary-yellow);font-size:2rem;margin-bottom:15px;}
        .testimonial-content blockquote p{font-style:italic;line-height:1.7;margin:0;
            color:#555;font-size:1.05rem;}
        .testimonial-footer{border-top:1px solid #eee;padding-top:20px;}
        .client-name{margin:0 0 8px;font-size:1.1rem;color:var(--charcoal);}
        .project-info,.testimonial-date{display:flex;align-items:center;gap:8px;
            margin:4px 0;font-size:.9rem;color:#777;}
        .project-info i,.testimonial-date i{color:var(--primary-yellow);width:16px;}

        /* ==== TRUST CARDS ==== */
        .trust-card{background:var(--white);border-radius:10px;padding:25px;
            box-shadow:0 5px 15px rgba(0,0,0,.05);transition:transform .3s,box-shadow .3s;text-align:center;}
        .trust-card:hover{transform:translateY(-8px);box-shadow:0 15px 30px rgba(0,0,0,.12);}
        .trust-icon{width:60px;height:60px;background:#fff3cd;border-radius:50%;
            display:flex;align-items:center;justify-content:center;margin:0 auto 15px;
            color:var(--primary-yellow);font-size:1.8rem;}

        /* ==== STATS ==== */
        .stats-container{display:flex;justify-content:space-around;flex-wrap:wrap;gap:20px;margin-top:50px;}
        .stat-item{text-align:center;flex:1;min-width:180px;padding:20px;}
        .stat-number{font-size:3rem;font-weight:700;color:var(--primary-yellow);margin-bottom:8px;}
        .stat-label{font-size:1.1rem;color:var(--charcoal);}

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
            .testimonials-banner{height:400px;padding:40px 0;}
            .banner-title{font-size:2.2rem;}
            .testimonials-section .row{flex-direction:column-reverse;}
        }
        @media (max-width:576px){
            .floating-buttons{bottom:20px;right:20px;}
            .floating-btn{width:50px;height:50px;font-size:1.2rem;}
        }
    </style>
</head>
<body>

<!-- ====================== HERO ====================== -->
<section class="testimonials-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Testimonials</li>
            </ol>
        </nav>
        <h1 class="banner-title">Client Testimonials</h1>
        <p class="banner-subtitle">What our satisfied clients say about working with us</p>
    </div>
</section>

<!-- ====================== MAIN + ASIDE ====================== -->
<main class="testimonials-section bg-light">
    <div class="container">
        <div class="row g-5">

            <!-- ==== MAIN: Testimonials Grid ==== -->
            <div class="col-lg-8">

                <section class="mb-5">
                    <h2 class="section-title">Hear From Our Clients</h2>
                    <p class="text-center mb-5 lead text-muted">
                        Don't just take our word for it. Here's what our clients have to say about their 
                        experience with Rakhi Construction & Consultancy.
                    </p>

                    <?php if (empty($testimonials)): ?>
                        <div class="text-center py-5">
                            <p class="text-muted">Testimonials will be available soon.</p>
                        </div>
                    <?php else: ?>
                        <div class="testimonials-grid">
                            <?php foreach ($testimonials as $t): ?>
                                <div class="testimonial-card">
                                    <div class="testimonial-content">
                                        <div class="testimonial-icon">
                                            <i class="fas fa-quote-left"></i>
                                        </div>
                                        <blockquote>
                                            <p>"<?= sanitizeOutput($t['text']) ?>"</p>
                                        </blockquote>
                                    </div>
                                    <div class="testimonial-footer">
                                        <div class="client-info">
                                            <h4 class="client-name"><?= sanitizeOutput($t['client_name']) ?></h4>

                                            <?php if ($t['project_title']): ?>
                                                <p class="project-info">
                                                    <i class="fas fa-briefcase"></i>
                                                    <?= sanitizeOutput($t['project_title']) ?>
                                                    <?php if ($t['project_location']): ?>
                                                        <span class="text-muted">| <?= sanitizeOutput($t['project_location']) ?></span>
                                                    <?php endif; ?>
                                                </p>
                                            <?php endif; ?>

                                            <p class="testimonial-date">
                                                <i class="fas fa-calendar-alt"></i>
                                                <?= date('F Y', strtotime($t['created_at'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Trust Indicators -->
                <section class="mb-5">
                    <h2 class="section-title">Why Clients Trust Us</h2>
                    <div class="row g-4">
                        <div class="col-md-6 col-lg-3">
                            <div class="trust-card">
                                <div class="trust-icon"><i class="fas fa-check-circle"></i></div>
                                <h4 class="h5 fw-bold">Quality Assurance</h4>
                                <p class="small">We maintain the highest standards in every project, ensuring durability and excellence in craftsmanship.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="trust-card">
                                <div class="trust-icon"><i class="fas fa-clock"></i></div>
                                <h4 class="h5 fw-bold">On-Time Delivery</h4>
                                <p class="small">Efficient project management ensures timely completion without compromising quality or safety.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="trust-card">
                                <div class="trust-icon"><i class="fas fa-dollar-sign"></i></div>
                                <h4 class="h5 fw-bold">Transparent Pricing</h4>
                                <p class="small">No hidden costs. We provide detailed quotes and maintain transparency throughout.</p>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-3">
                            <div class="trust-card">
                                <div class="trust-icon"><i class="fas fa-headset"></i></div>
                                <h4 class="h5 fw-bold">Excellent Support</h4>
                                <p class="small">Our relationship continues post-completion with ongoing support and warranty.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Client Statistics -->
                <section>
                    <h2 class="section-title">Our Client Satisfaction Record</h2>
                    <div class="stats-container">
                        <div class="stat-item">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Client Satisfaction Rate</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">450+</div>
                            <div class="stat-label">Happy Clients</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">85%</div>
                            <div class="stat-label">Repeat & Referral Business</div>
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
                        <form action="<?php echo SITE_URL; ?>/packages.php" method="get" class="search-box">
                            <input type="text" name="search" placeholder="Search packages..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- CATEGORIES -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Categories</h3>
                        <ul class="category-list">
                            <li><a href="<?php echo SITE_URL; ?>/packages.php" class="<?= empty($_GET['category']) ? 'active' : '' ?>">
                                <span>All Packages</span>
                                <span class="badge bg-dark text-white"><?= $total_packages ?></span>
                            </a></li>
                            <?php foreach ($categories as $c): ?>
                                <li><a href="<?php echo SITE_URL; ?>/packages.php?category=<?= urlencode($c['cat']) ?>"
                                       class="<?= ($_GET['category'] ?? '') === $c['cat'] ? 'active' : '' ?>">
                                    <span><?= ucfirst(sanitizeOutput($c['cat'])) ?></span>
                                    <span class="badge bg-dark text-white"><?= $c['cnt'] ?></span>
                                </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- POPULAR -->
                    <!-- <div class="sidebar">
                        <h3 class="sidebar-title">Popular Packages</h3>
                        <?php foreach ($popular_packages as $p): ?>
                            <div class="popular-package">
                                <div class="popular-package-image">
                                    <img src="https://via.placeholder.com/60" alt="">
                                </div>
                                <div>
                                    <div class="popular-package-title">
                                        <a href="<?php echo SITE_URL; ?>/select-plan.php?plan=<?= urlencode($p['title']) ?>">
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
                    </div> -->

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
                <h2 class="display-5 fw-bold mb-4">Become Our Next Success Story</h2>
                <p class="lead mb-4">Join hundreds of satisfied clients who trusted us with their construction dreams.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary btn-lg">
                        Start Your Project
                    </a>
                    <a href="<?php echo SITE_URL; ?>/projects.php" class="btn btn-outline-light btn-lg">
                        View Our Projects
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>