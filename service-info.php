<?php
/**
 * Service Info Page – Grand Jyothi Construction
 * Modern design 100% aligned with blog‑detail / project‑info / projects / services
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

/* ---------- Validate slug ---------- */
$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    header('Location: services.php');
    exit;
}

/* ---------- Current Service ---------- */
$sql = "SELECT title, description, icon, category, author, cover_image, created_at
        FROM services WHERE slug = ? LIMIT 1";
$stmt = executeQuery($sql, [$slug]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$service) {
    header('Location: services.php');
    exit;
}

/* ---------- Prev / Next (by created_at) ---------- */
$prev = executeQuery(
    "SELECT slug, title FROM services WHERE created_at < ? ORDER BY created_at DESC LIMIT 1",
    [$service['created_at']]
)->fetch();

$next = executeQuery(
    "SELECT slug, title FROM services WHERE created_at > ? ORDER BY created_at ASC LIMIT 1",
    [$service['created_at']]
)->fetch();

/* ---------- Sidebar data ---------- */
$categories = executeQuery(
    "SELECT category, COUNT(*) AS count
     FROM services
     WHERE category IS NOT NULL AND category <> ''
     GROUP BY category
     ORDER BY category"
)->fetchAll();

$total_services = executeQuery("SELECT COUNT(*) FROM services")->fetchColumn();

$popular_services = executeQuery(
    "SELECT title, slug, cover_image
     FROM services
     ORDER BY created_at DESC LIMIT 3"
)->fetchAll();

$page_title = sanitizeOutput($service['title']) . ' | Grand Jyothi Construction';
require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>

    <!-- Bootstrap + Icons + Google Fonts -->
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
            color:var(--charcoal);font-weight:600;padding:10px 25px;border-radius:8px;}
        .btn-primary:hover{background:#e89a1f;border-color:#e89a1f;color:var(--charcoal);}

        /* ==== HERO ==== */
        .service-banner{height:500px;background:linear-gradient(rgba(26,26,26,.6),rgba(26,26,26,.6)),
            url('<?= sanitizeOutput($service['cover_image'] ?? 'https://via.placeholder.com/1600x900/1A1A1A/F9A826?text=Service') ?>')
            center/cover no-repeat;display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;}
        .service-banner::before{content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.1) 0%,transparent 70%);}
        .service-title{font-size:3rem;margin-bottom:20px;line-height:1.2;}
        .service-meta{display:flex;flex-wrap:wrap;gap:15px;align-items:center;}
        .badge-category,.badge-author{background:var(--primary-yellow);color:var(--charcoal);
            padding:5px 15px;border-radius:20px;font-size:.9rem;font-weight:600;}

        /* ==== CONTENT ==== */
        .service-content-section{padding:80px 0;}
        .service-content{font-size:1.1rem;line-height:1.8;}
        .service-content h2{font-size:1.8rem;margin:40px 0 20px;padding-bottom:10px;
            border-bottom:2px solid var(--primary-yellow);}

        /* ==== SIDEBAR ==== */
        .sidebar{background:var(--light-gray);border-radius:10px;padding:30px;margin-bottom:30px;}
        .sidebar-title{font-size:1.2rem;margin-bottom:20px;padding-bottom:10px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;}
        .search-box{position:relative;margin-bottom:30px;}
        .search-box input{width:100%;padding:12px 15px;border-radius:5px;border:1px solid #ddd;}
        .search-box button{position:absolute;right:5px;top:5px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);padding:7px 15px;border-radius:5px;font-weight:600;}
        .category-list{list-style:none;padding:0;}
        .category-list li{padding:10px 0;border-bottom:1px solid #eee;}
        .category-list li:last-child{border:none;}
        .category-list a{display:flex;justify-content:space-between;align-items:center;
            color:var(--charcoal);text-decoration:none;transition:.3s;}
        .category-list a:hover,.category-list a.active{color:var(--primary-yellow);font-weight:600;}
        .category-count{background:var(--charcoal);color:var(--white);padding:3px 8px;
            border-radius:10px;font-size:.8rem;}
        .popular-service{display:flex;margin-bottom:15px;padding-bottom:15px;
            border-bottom:1px solid #eee;}
        .popular-service:last-child{margin-bottom:0;padding-bottom:0;border:none;}
        .popular-service-image{width:70px;height:70px;border-radius:5px;overflow:hidden;
            margin-right:15px;flex-shrink:0;}
        .popular-service-image img{width:100%;height:100%;object-fit:cover;}
        .popular-service-title{font-size:.9rem;margin-bottom:5px;}
        .popular-service-title a{color:var(--charcoal);text-decoration:none;transition:.3s;}
        .popular-service-title a:hover{color:var(--primary-yellow);}

        /* ==== SHARE ==== */
        .social-share{display:flex;align-items:center;gap:10px;margin:40px 0;}
        .social-icon{width:40px;height:40px;border-radius:50%;background:var(--light-gray);
            color:var(--charcoal);display:flex;align-items:center;justify-content:center;transition:.3s;}
        .social-icon:hover{transform:translateY(-3px);}
        .whatsapp:hover{background:#25d366;color:#fff;}
        .facebook:hover{background:#3b5998;color:#fff;}
        .linkedin:hover{background:#0077b5;color:#fff;}

        /* ==== NAVIGATION ==== */
        .service-navigation{display:flex;justify-content:space-between;padding:40px 0;
            border-top:1px solid #eee;border-bottom:1px solid #eee;margin:40px 0;}
        .nav-service{max-width:45%;}
        .nav-service a{display:flex;align-items:center;text-decoration:none;color:var(--charcoal);transition:.3s;}
        .nav-service a:hover{color:var(--primary-yellow);}
        .nav-icon{font-size:1.5rem;margin:0 15px;}

        /* ==== CTA ==== */
        .cta-section{background:linear-gradient(135deg,var(--charcoal) 0%,#2d2d2d 100%);
            color:var(--white);padding:80px 0;text-align:center;}
        .cta-section h2{color:var(--white);margin-bottom:1.5rem;}
        .btn-outline-light{border:2px solid rgba(255,255,255,.3);color:var(--white);
            padding:12px 30px;border-radius:30px;}
        .btn-outline-light:hover{background:rgba(255,255,255,.1);border-color:var(--white);}

        /* ==== RESPONSIVE ==== */
        @media (max-width:768px){
            .service-banner{height:400px;padding:40px 0;}
            .service-title{font-size:2.2rem;}
            .service-navigation{flex-direction:column;}
            .nav-service{max-width:100%;margin-bottom:20px;}
            .nav-service.next a{flex-direction:row;}
        }
    </style>
</head>
<body>

<!-- ====================== HERO ====================== -->
<section class="service-banner">
    <div class="container">
        <div class="position-relative z-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/constructioninnagpur/">Home</a></li>
                    <li class="breadcrumb-item"><a href="services.php">Services</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= sanitizeOutput($service['title']) ?></li>
                </ol>
            </nav>
            <h1 class="service-title"><?= sanitizeOutput($service['title']) ?></h1>
            <div class="service-meta">
                <?php if ($service['category']): ?>
                    <div class="badge-category"><?= ucfirst(sanitizeOutput($service['category'])) ?></div>
                <?php endif; ?>
                <?php if ($service['author']): ?>
                    <div class="badge-author"><i class="fas fa-user me-1"></i> <?= sanitizeOutput($service['author']) ?></div>
                <?php endif; ?>
                <div class="meta-date"><i class="far fa-calendar-alt me-1"></i> <?= date('F j, Y', strtotime($service['created_at'])) ?></div>
            </div>
        </div>
    </div>
</section>

<!-- ====================== MAIN CONTENT ====================== -->
<section class="service-content-section">
    <div class="container">
        <div class="row">

            <!-- ==== LEFT COLUMN (Content + Share + Nav) ==== -->
            <div class="col-lg-8">

                <!-- Service Content -->
                <div class="service-content mb-5">
                    <?= nl2br(sanitizeOutput($service['description'])) ?>
                </div>

                <!-- ==== SHARE ==== -->
                <div class="social-share">
                    <span class="fw-bold me-2">Share:</span>
                    <a href="https://wa.me/?text=Check%20this%20service:%20<?= urlencode(currentUrl()) ?>"
                       target="_blank" class="social-icon whatsapp"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(currentUrl()) ?>"
                       target="_blank" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(currentUrl()) ?>"
                      
                       target="_blank" class="social-icon linkedin"><i class="fab fa-linkedin-in"></i></a>
                </div>

                <!-- ==== PREV / NEXT ==== -->
                <div class="service-navigation">
                    <?php if ($prev): ?>
                        <div class="nav-service prev">
                            <a href="service-info.php?slug=<?= $prev['slug'] ?>">
                                <div class="nav-icon"><i class="fas fa-arrow-left"></i></div>
                                <div>
                                    <div class="text-muted small">Previous Service</div>
                                    <div class="nav-service-title"><?= sanitizeOutput($prev['title']) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php else: ?><div></div><?php endif; ?>

                    <?php if ($next): ?>
                        <div class="nav-service next">
                            <a href="service-info.php?slug=<?= $next['slug'] ?>">
                                <div class="nav-icon"><i class="fas fa-arrow-right"></i></div>
                                <div>
                                    <div class="text-muted small">Next Service</div>
                                    <div class="nav-service-title"><?= sanitizeOutput($next['title']) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- CTA Buttons -->
                <div class="text-center mt-5">
                    <a href="/constructioninnagpur/contact.php" class="btn btn-primary me-3">
                        Get Quote
                    </a>
                    <a href="services.php" class="btn btn-outline-dark">
                        All Services
                    </a>
                </div>

            </div>

            <!-- ==== RIGHT SIDEBAR ==== -->
            <div class="col-lg-4">

                <!-- 1. SEARCH -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Search Services</h3>
                    <form action="services.php" method="get" class="search-box">
                        <input type="text" name="search" placeholder="Search services..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- 2. CATEGORIES -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Service Categories</h3>
                    <ul class="category-list">
                        <li>
                            <a href="services.php" class="<?= empty($_GET['category']) ? 'active' : '' ?>">
                                <span>All Services</span>
                                <span class="category-count"><?= $total_services ?></span>
                            </a>
                        </li>
                        <?php foreach ($categories as $c): ?>
                            <li>
                                <a href="services.php?category=<?= urlencode($c['category']) ?>"
                                   class="<?= ($_GET['category'] ?? '') === $c['category'] ? 'active' : '' ?>">
                                    <span><?= ucfirst(sanitizeOutput($c['category'])) ?></span>
                                    <span class="category-count"><?= $c['count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- 3. POPULAR SERVICES -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Popular Services</h3>
                    <?php foreach ($popular_services as $p):
                        $thumb = $p['cover_image']
                            ? sanitizeOutput($p['cover_image'])
                            : "https://via.placeholder.com/70";
                    ?>
                        <div class="popular-service">
                            <div class="popular-service-image">
                                <img src="<?= $thumb ?>" alt="<?= sanitizeOutput($p['title']) ?>">
                            </div>
                            <div>
                                <div class="popular-service-title">
                                    <a href="service-info.php?slug=<?= $p['slug'] ?>">
                                        <?= sanitizeOutput($p['title']) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ====================== CTA ====================== -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-4">Ready to Start Your Project?</h2>
                <p class="lead mb-4">Let’s bring your vision to life with expert construction services</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="/constructioninnagpur/contact.php" class="btn btn-primary btn-lg">
                        Get Free Consultation
                    </a>
                    <a href="/constructioninnagpur/services.php" class="btn btn-outline-light btn-lg">
                        Explore All Services
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
/* Helper for share URLs */
function currentUrl(): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
require_once __DIR__ . '/includes/footer.php';
?>

</body>
</html>