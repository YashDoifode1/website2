<?php
/**
 * Service Info Page – Grand Jyothi Construction
 * Modern design aligned with services / projects / blog-detail
 * FIXED: SITE_URL, image paths, Feather icons, mobile UX, category logic
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config.php'; // For SITE_URL

/* ---------- Validate slug ---------- */
$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    header('Location: ' . SITE_URL . '/services.php');
    exit;
}

/* ---------- Current Service ---------- */
$sql = "SELECT title, description, icon, SUBSTRING_INDEX(title, ' ', 1) AS category, 
               author, cover_image, icon_image, created_at, slug
        FROM services WHERE slug = ? LIMIT 1";
$stmt = executeQuery($sql, [$slug]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header('Location: ' . SITE_URL . '/services.php');
    exit;
}

/* ---------- Prev / Next (by created_at) ---------- */
$prev = executeQuery(
    "SELECT slug, title FROM services WHERE created_at < ? ORDER BY created_at DESC LIMIT 1",
    [$service['created_at']]
)->fetch(PDO::FETCH_ASSOC);

$next = executeQuery(
    "SELECT slug, title FROM services WHERE created_at > ? ORDER BY created_at ASC LIMIT 1",
    [$service['created_at']]
)->fetch(PDO::FETCH_ASSOC);

/* ---------- Sidebar: Categories (first word of title) ---------- */
$categories = executeQuery("
    SELECT SUBSTRING_INDEX(title, ' ', 1) AS category, COUNT(*) AS count
    FROM services
    GROUP BY category
    ORDER BY category
")->fetchAll(PDO::FETCH_ASSOC);

$total_services = (int)executeQuery("SELECT COUNT(*) FROM services")->fetchColumn();

/* ---------- Popular Services ---------- */
$popular_services = executeQuery("
    SELECT title, slug, cover_image, icon_image
    FROM services
    ORDER BY created_at DESC LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        :root{
            --primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;
            --light-gray:#f8f9fa;--medium-gray:#e9ecef;
        }
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);line-height:1.6;}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}

        /* ==== BUTTONS ==== */
        .btn-primary{background:var(--primary-yellow);border-color:var(--primary-yellow);
            color:var(--charcoal);font-weight:600;padding:10px 25px;border-radius:8px;transition:.3s;}
        .btn-primary:hover{background:#e89a1f;border-color:#e89a1f;color:var(--charcoal);}
        .btn-outline-dark{border:2px solid #ddd;color:var(--charcoal);padding:10px 25px;border-radius:8px;}
        .btn-outline-dark:hover{background:#f1f1f1;}

        /* ==== HERO ==== */
        .service-banner{
            height:500px;background:linear-gradient(rgba(26,26,26,.7),rgba(26,26,26,.7)),
            url('<?= !empty($service['cover_image']) ? SITE_URL . sanitizeOutput($service['cover_image']) : 'https://via.placeholder.com/1600x900/1A1A1A/F9A826?text=Service' ?>')
            center/cover no-repeat;display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;
        }
        .service-banner::before{content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.15) 0%,transparent 70%);}
        .service-title{font-size:3rem;margin-bottom:20px;line-height:1.2;}
        .service-meta{display:flex;flex-wrap:wrap;gap:15px;align-items:center;font-size:.95rem;}
        .badge-category,.badge-author{background:var(--primary-yellow);color:var(--charcoal);
            padding:5px 15px;border-radius:20px;font-weight:600;}
        .meta-date{color:rgba(255,255,255,.9);}

        /* ==== CONTENT ==== */
        .service-content-section{padding:80px 0;background:var(--light-gray);}
        .service-content{font-size:1.1rem;line-height:1.8;color:#444;}
        .service-content h2{font-size:1.8rem;margin:40px 0 20px;padding-bottom:10px;
            border-bottom:3px solid var(--primary-yellow);display:inline-block;}
        .service-content p{margin-bottom:1.5rem;}
        .service-content ul{list-style:disc;margin-left:20px;margin-bottom:1.5rem;}
        .service-content ul li{margin-bottom:8px;}

        /* ==== ICON DISPLAY ==== */
        .service-icon-display{
            width:80px;height:80px;background:var(--primary-yellow);color:var(--charcoal);
            border-radius:16px;display:flex;align-items:center;justify-content:center;
            margin:0 auto 25px;font-size:2.5rem;box-shadow:0 6px 15px rgba(249,168,38,.3);
        }
        .service-icon-display img{width:50px;height:50px;object-fit:contain;}

        /* ==== SIDEBAR ==== */
        .sidebar{background:var(--white);border-radius:12px;padding:28px;
            box-shadow:0 6px 20px rgba(0,0,0,.05);margin-bottom:30px;}
        .sidebar-title{font-size:1.2rem;margin-bottom:18px;padding-bottom:8px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;}
        .search-box{position:relative;margin-bottom:25px;}
        .search-box input{width:100%;padding:12px 45px 12px 16px;border-radius:50px;border:1px solid #ddd;font-size:.95rem;}
        .search-box button{position:absolute;right:6px;top:6px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);width:36px;height:36px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;font-size:1rem;}
        .category-list{list-style:none;padding:0;margin:0;}
        .category-list a{display:flex;justify-content:space-between;align-items:center;
            padding:10px 0;color:var(--charcoal);text-decoration:none;border-bottom:1px solid #eee;font-size:.95rem;}
        .category-list a:hover,.category-list a.active{color:var(--primary-yellow);font-weight:600;}
        .category-count{background:var(--charcoal);color:var(--white);padding:2px 8px;
            border-radius:10px;font-size:.75rem;}
        .popular-service{display:flex;gap:12px;margin-bottom:18px;padding-bottom:18px;
            border-bottom:1px solid #eee;}
        .popular-service:last-child{margin-bottom:0;padding-bottom:0;border:none;}
        .popular-service-image{width:70px;height:70px;border-radius:8px;overflow:hidden;flex-shrink:0;background:#eee;}
        .popular-service-image img{width:100%;height:100%;object-fit:cover;}
        .popular-service-title a{color:var(--charcoal);font-weight:500;text-decoration:none;font-size:.95rem;line-height:1.3;}
        .popular-service-title a:hover{color:var(--primary-yellow);}

        /* ==== SHARE ==== */
        .social-share{display:flex;align-items:center;gap:12px;margin:40px 0;flex-wrap:wrap;}
        .social-icon{width:42px;height:42px;border-radius:50%;background:var(--light-gray);
            color:var(--charcoal);display:flex;align-items:center;justify-content:center;font-size:1.1rem;transition:.3s;}
        .social-icon:hover{transform:translateY(-4px);box-shadow:0 8px 15px rgba(0,0,0,.1);}
        .whatsapp:hover{background:#25d366;color:#fff;}
        .facebook:hover{background:#3b5998;color:#fff;}
        .linkedin:hover{background:#0077b5;color:#fff;}

        /* ==== NAVIGATION ==== */
        .service-navigation{display:flex;justify-content:space-between;padding:40px 0;
            border-top:1px solid #ddd;border-bottom:1px solid #ddd;margin:50px 0;}
        .nav-service{max-width:45%;}
        .nav-service a{display:flex;align-items:center;text-decoration:none;color:var(--charcoal);transition:.3s;}
        .nav-service a:hover{color:var(--primary-yellow);}
        .nav-icon{font-size:1.5rem;margin:0 15px;color:var(--primary-yellow);}

        /* ==== CTA ==== */
        .cta-section{background:linear-gradient(135deg,var(--charcoal) 0%,#2d2d2d 100%);
            color:var(--white);padding:80px 0;text-align:center;}
        .cta-section h2{color:var(--white);margin-bottom:1.5rem;font-size:2.2rem;}
        .btn-outline-light{border:2px solid rgba(255,255,255,.4);color:var(--white);
            padding:12px 32px;border-radius:50px;font-weight:600;transition:.3s;}
        .btn-outline-light:hover{background:rgba(255,255,255,.1);border-color:var(--white);}

        /* ==== RESPONSIVE ==== */
        @media (max-width:992px){
            .service-banner{height:400px;padding:40px 0;}
            .service-title{font-size:2.4rem;}
            .service-navigation{flex-direction:column;gap:25px;}
            .nav-service{max-width:100%;}
            .nav-service.prev a{flex-direction:row;}
            .nav-service.next a{flex-direction:row-reverse;text-align:right;}
        }
        @media (max-width:576px){
            .service-title{font-size:2rem;}
            .service-meta{flex-direction:column;align-items:flex-start;gap:10px;}
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
                    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/services.php">Services</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= sanitizeOutput($service['title']) ?></li>
                </ol>
            </nav>
            <h1 class="service-title"><?= sanitizeOutput($service['title']) ?></h1>
            <div class="service-meta">
                <?php if (!empty($service['category'])): ?>
                    <div class="badge-category"><?= ucfirst(sanitizeOutput($service['category'])) ?></div>
                <?php endif; ?>
                <?php if (!empty($service['author'])): ?>
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
        <div class="row g-5">

            <!-- ==== LEFT COLUMN ==== -->
            <div class="col-lg-8">

                <!-- Icon Display -->
                <div class="text-center mb-5">
                    <div class="service-icon-display">
                        <?php if (!empty($service['icon_image'])): ?>
                            <img src="<?= SITE_URL ?><?= sanitizeOutput($service['icon_image']) ?>" alt="<?= sanitizeOutput($service['title']) ?> icon" loading="lazy">
                        <?php else: ?>
                            <i data-feather="<?= sanitizeOutput($service['icon'] ?? 'tool') ?>"></i>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Service Content -->
                <div class="service-content mb-5">
                    <?= nl2br(htmlspecialchars($service['description'])) ?>
                </div>

                <!-- Share -->
                <div class="social-share">
                    <span class="fw-bold me-2">Share:</span>
                    <a href="https://wa.me/?text=Check%20out%20this%20service:%20<?= urlencode(currentUrl()) ?>"
                       target="_blank" class="social-icon whatsapp" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(currentUrl()) ?>"
                       target="_blank" class="social-icon facebook" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(currentUrl()) ?>&title=<?= urlencode($service['title']) ?>"
                       target="_blank" class="social-icon linkedin" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>

                <!-- Prev / Next -->
                <div class="service-navigation">
                    <?php if ($prev): ?>
                        <div class="nav-service prev">
                            <a href="<?= SITE_URL ?>/service-info.php?slug=<?= urlencode($prev['slug']) ?>">
                                <div class="nav-icon"><i class="fas fa-arrow-left"></i></div>
                                <div>
                                    <div class="text-muted small">Previous</div>
                                    <div class="nav-service-title"><?= sanitizeOutput($prev['title']) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php else: ?><div></div><?php endif; ?>

                    <?php if ($next): ?>
                        <div class="nav-service next">
                            <a href="<?= SITE_URL ?>/service-info.php?slug=<?= urlencode($next['slug']) ?>">
                                <div>
                                    <div class="text-muted small">Next</div>
                                    <div class="nav-service-title"><?= sanitizeOutput($next['title']) ?></div>
                                </div>
                                <div class="nav-icon"><i class="fas fa-arrow-right"></i></div>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- CTA Buttons -->
                <div class="text-center mt-5">
                    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary me-3">
                        Get Quote
                    </a>
                    <a href="<?= SITE_URL ?>/services.php" class="btn btn-outline-dark">
                        All Services
                    </a>
                </div>

            </div>

            <!-- ==== RIGHT SIDEBAR ==== -->
            <div class="col-lg-4">

                <!-- Search -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Search Services</h3>
                    <form action="<?= SITE_URL ?>/services.php" method="get" class="search-box">
                        <input type="text" name="search" placeholder="Search services..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Categories -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Service Categories</h3>
                    <ul class="category-list">
                        <li>
                            <a href="<?= SITE_URL ?>/services.php" class="<?= empty($_GET['category']) ? 'active' : '' ?>">
                                <span>All Services</span>
                                <span class="category-count"><?= $total_services ?></span>
                            </a>
                        </li>
                        <?php foreach ($categories as $c): ?>
                            <li>
                                <a href="<?= SITE_URL ?>/services.php?category=<?= urlencode($c['category']) ?>"
                                   class="<?= ($_GET['category'] ?? '') === $c['category'] ? 'active' : '' ?>">
                                    <span><?= ucfirst(sanitizeOutput($c['category'])) ?></span>
                                    <span class="category-count"><?= $c['count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Popular Services -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Popular Services</h3>
                    <?php foreach ($popular_services as $p): ?>
                        <?php
                        $thumb = !empty($p['cover_image'])
                            ? SITE_URL . sanitizeOutput($p['cover_image'])
                            : 'https://via.placeholder.com/70/eee/ccc?text=Service';
                        ?>
                        <div class="popular-service">
                            <div class="popular-service-image">
                                <img src="<?= $thumb ?>" alt="<?= sanitizeOutput($p['title']) ?>" loading="lazy">
                            </div>
                            <div>
                                <div class="popular-service-title">
                                    <a href="<?= SITE_URL ?>/service-info.php?slug=<?= urlencode($p['slug']) ?>">
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
                    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary btn-lg">
                        Get Free Consultation
                    </a>
                    <a href="<?= SITE_URL ?>/services.php" class="btn btn-outline-light btn-lg">
                        Explore All Services
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
/* Helper: Current URL */
function currentUrl(): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
require_once __DIR__ . '/includes/footer.php';
?>

<!-- Initialize Feather Icons -->
<script>
    feather.replace();
</script>

</body>
</html>