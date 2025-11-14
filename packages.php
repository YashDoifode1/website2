<?php
/**
 * Packages Page – Grand Jyothi Construction
 * Fully compatible with tables: packages + package_sections
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Construction Packages | Grand Jyothi Construction';

/* ---------- 1. Fetch Active Packages ---------- */
$sql = "SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC, id ASC";
$stmt = executeQuery($sql);
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- 2. Fetch Active Sections (grouped by package_id) ---------- */
$sectionsRaw = executeQuery(
    "SELECT * FROM package_sections 
     WHERE is_active = 1 
     ORDER BY package_id, display_order ASC, id ASC"
)->fetchAll(PDO::FETCH_ASSOC);

$packageSections = [];
foreach ($sectionsRaw as $s) {
    $packageSections[$s['package_id']][] = $s;
}

/* ---------- 3. Build Unique Features for Comparison Table ---------- */
$allFeatures = [];
foreach ($packages as $p) {
    if (!empty($p['features'])) {
        $list = array_map('trim', explode('|', $p['features']));
        $allFeatures = array_merge($allFeatures, $list);
    }
}
$uniqueFeatures = array_unique(array_filter($allFeatures));

/* ---------- 4. Sidebar Data ---------- */
$categories = executeQuery(
    "SELECT SUBSTRING_INDEX(title, ' ', 1) AS category, COUNT(*) AS count
     FROM packages
     WHERE is_active = 1
     GROUP BY category
     ORDER BY category"
)->fetchAll();

$total_packages = count($packages);
$popular_packages = array_slice($packages, 0, 3);

/* ---------- 5. Prev / Next Navigation (by display_order) ---------- */
$prev = $next = null;
if (!empty($packages)) {
    $ids = array_column($packages, 'id');
    $currentPos = 0; // We show all, but let’s assume first is current for demo
    $prevId = $ids[$currentPos - 1] ?? null;
    $nextId = $ids[$currentPos + 1] ?? null;

    if ($prevId) {
        $prev = executeQuery("SELECT title, id FROM packages WHERE id = ?", [$prevId])->fetch();
    }
    if ($nextId) {
        $next = executeQuery("SELECT title, id FROM packages WHERE id = ?", [$nextId])->fetch();
    }
}

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
        .btn-outline-light{border:2px solid rgba(255,255,255,.3);color:var(--white);
            padding:12px 30px;border-radius:30px;}
        .btn-outline-light:hover{background:rgba(255,255,255,.1);border-color:var(--white);}

        /* ==== HERO ==== */
        .packages-banner{height:500px;background:linear-gradient(rgba(26,26,26,.6),rgba(26,26,26,.6)),
            url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
            center/cover no-repeat;display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;}
        .packages-banner::before{content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.1) 0%,transparent 70%);}
        .banner-title{font-size:3rem;margin-bottom:20px;line-height:1.2;}
        .banner-subtitle{font-size:1.2rem;opacity:.9;}

        /* ==== BREADCRUMB ==== */
        .breadcrumb{background:transparent;padding:0;margin-bottom:20px;}
        .breadcrumb-item a{color:rgba(255,255,255,.8);text-decoration:none;}
        .breadcrumb-item.active{color:var(--primary-yellow);}

        /* ==== CONTENT ==== */
        .packages-section{padding:80px 0;}
        .section-title{font-size:1.8rem;margin-bottom:30px;padding-bottom:15px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;}

        /* ==== PACKAGES GRID ==== */
        .packages-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(350px,1fr));gap:30px;}
        .package-card{background:var(--white);border-radius:10px;overflow:hidden;
            box-shadow:0 5px 15px rgba(0,0,0,.05);transition:all .3s ease;height:100%;position:relative;}
        .package-card:hover{transform:translateY(-8px);box-shadow:0 15px 30px rgba(0,0,0,.12);}
        .package-header{background:var(--charcoal);color:var(--white);padding:25px;text-align:center;}
        .package-popular{position:absolute;top:-10px;left:50%;transform:translateX(-50%);
            background:var(--primary-yellow);color:var(--charcoal);padding:5px 20px;
            border-radius:20px;font-size:.8rem;font-weight:600;z-index:10;}
        .package-body{padding:30px;}
        .package-price{font-size:2.2rem;font-weight:700;color:var(--primary-yellow);
            text-align:center;margin-bottom:15px;}
        .package-desc{color:#666;font-size:.95rem;text-align:center;margin-bottom:25px;}

        /* ==== ACCORDION ==== */
        .accordion-button{font-weight:600;background:#fff;color:var(--charcoal);}
        .accordion-button:not(.collapsed){background:var(--primary-yellow);color:var(--charcoal);}
        .accordion-body{font-size:.95rem;color:#555;line-height:1.5;padding:15px;}

        /* ==== COMPARISON ==== */
        .comparison-toggle{background:var(--white);border-radius:50px;padding:10px 20px;
            display:inline-flex;align-items:center;margin-top:20px;box-shadow:0 3px 10px rgba(0,0,0,.1);}
        .comparison-toggle span{margin-right:10px;font-weight:500;}
        .comparison-section{padding:60px 0;background:var(--white);display:none;}
        .comparison-table{width:100%;border-collapse:collapse;margin-top:20px;}
        .comparison-table th{background:var(--charcoal);color:var(--white);padding:15px;
            text-align:center;font-weight:600;}
        .comparison-table td{padding:15px;border-bottom:1px solid #eee;text-align:center;}
        .comparison-table tr:nth-child(even){background:#f9f9f9;}
        .comparison-table .feature-name{text-align:left;font-weight:500;background:#fff !important;
            position:sticky;left:0;z-index:1;}
        .check-mark{color:var(--primary-yellow);font-size:1.2rem;}
        .cross-mark{color:#ccc;font-size:1.2rem;}

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
        .popular-package{display:flex;margin-bottom:15px;padding-bottom:15px;
            border-bottom:1px solid #eee;}
        .popular-package:last-child{margin-bottom:0;padding-bottom:0;border:none;}
        .popular-package-image{width:70px;height:70px;border-radius:5px;overflow:hidden;
            margin-right:15px;flex-shrink:0;}
        .popular-package-image img{width:100%;height:100%;object-fit:cover;}
        .popular-package-title a{color:var(--charcoal);text-decoration:none;transition:.3s;}
        .popular-package-title a:hover{color:var(--primary-yellow);}

        /* ==== SHARE ==== */
        .social-share{display:flex;align-items:center;gap:10px;margin:40px 0;}
        .social-icon{width:40px;height:40px;border-radius:50%;background:var(--light-gray);
            color:var(--charcoal);display:flex;align-items:center;justify-content:center;transition:.3s;}
        .social-icon:hover{transform:translateY(-3px);}
        .whatsapp:hover{background:#25d366;color:#fff;}
        .facebook:hover{background:#3b5998;color:#fff;}
        .linkedin:hover{background:#0077b5;color:#fff;}

        /* ==== NAVIGATION ==== */
        .package-navigation{display:flex;justify-content:space-between;padding:40px 0;
            border-top:1px solid #eee;border-bottom:1px solid #eee;margin:40px 0;}
        .nav-package{max-width:45%;}
        .nav-package a{display:flex;align-items:center;text-decoration:none;color:var(--charcoal);transition:.3s;}
        .nav-package a:hover{color:var(--primary-yellow);}
        .nav-icon{font-size:1.5rem;margin:0 15px;}

        /* ==== CTA ==== */
        .cta-section{background:linear-gradient(135deg,var(--charcoal) 0%,#2d2d2d 100%);
            color:var(--white);padding:80px 0;text-align:center;}
        .cta-section h2{color:var(--white);margin-bottom:1.5rem;}

        /* ==== RESPONSIVE ==== */
        @media (max-width:768px){
            .packages-banner{height:400px;padding:40px 0;}
            .banner-title{font-size:2.2rem;}
            .packages-grid{grid-template-columns:1fr;}
            .package-navigation{flex-direction:column;}
            .nav-package{max-width:100%;margin-bottom:20px;}
        }
    </style>
</head>
<body>

<!-- ====================== HERO ====================== -->
<section class="packages-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/constructioninnagpur/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Packages</li>
            </ol>
        </nav>
        <h1 class="banner-title">Construction Packages</h1>
        <p class="banner-subtitle">Choose the perfect package for your dream home. Transparent pricing, quality materials, and expert craftsmanship.</p>
        <div class="comparison-toggle">
            <span>Compare Packages</span>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="comparisonToggle">
            </div>
        </div>
    </div>
</section>

<!-- ====================== MAIN CONTENT ====================== -->
<section class="packages-section">
    <div class="container">
        <div class="row">

            <!-- ==== LEFT: Packages + Comparison ==== -->
            <div class="col-lg-8">

                <?php if (empty($packages)): ?>
                    <div class="text-center py-5">
                        <p class="lead text-muted">No packages available at the moment. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <div class="packages-grid">
                        <?php foreach ($packages as $p): ?>
                            <div class="package-card" id="package-<?= $p['id'] ?>">
                                <?php if (!empty($p['is_popular'])): ?>
                                    <div class="package-popular">MOST POPULAR</div>
                                <?php endif; ?>
                                <div class="package-header">
                                    <h3><?= sanitizeOutput($p['title']) ?></h3>
                                </div>
                                <div class="package-body">
                                    <div class="package-price">
                                        <?php if ($p['price_per_sqft'] > 0): ?>
                                            ₹<?= number_format((float)$p['price_per_sqft']) ?>/sq.ft
                                        <?php else: ?>
                                            Custom Quote
                                        <?php endif; ?>
                                    </div>
                                    <p class="package-desc"><?= sanitizeOutput($p['description']) ?></p>

                                    <?php if (!empty($packageSections[$p['id']])): ?>
                                        <div class="accordion" id="accordion<?= $p['id'] ?>">
                                            <?php foreach ($packageSections[$p['id']] as $i => $s):
                                                $collapseId = "collapse{$p['id']}_{$i}";
                                                $headingId  = "heading{$p['id']}_{$i}";
                                            ?>
                                                <div class="accordion-item mb-2">
                                                    <h2 class="accordion-header" id="<?= $headingId ?>">
                                                        <button class="accordion-button <?= $i>0?'collapsed':'' ?>"
                                                                type="button" data-bs-toggle="collapse"
                                                                data-bs-target="#<?= $collapseId ?>"
                                                                aria-expanded="<?= $i===0?'true':'false' ?>"
                                                                aria-controls="<?= $collapseId ?>">
                                                            <?= sanitizeOutput($s['title']) ?>
                                                        </button>
                                                    </h2>
                                                    <div id="<?= $collapseId ?>" class="accordion-collapse collapse <?= $i===0?'show':'' ?>"
                                                         aria-labelledby="<?= $headingId ?>" data-bs-parent="#accordion<?= $p['id'] ?>">
                                                        <div class="accordion-body">
                                                            <?= nl2br(sanitizeOutput($s['content'])) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center">No detailed sections available.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="package-footer text-center pb-4">
                                    <a href="/constructioninnagpur/select-plan.php?plan=<?= urlencode($p['title']) ?>"
                                       class="btn btn-primary w-100">
                                        <?= $p['price_per_sqft'] > 0 ? 'Select Package' : 'Get Quote' ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Comparison Table -->
                <div class="comparison-section mt-5" id="comparisonSection">
                    <h2 class="section-title text-center">Package Comparison</h2>
                    <div class="table-responsive">
                        <table class="comparison-table">
                            <thead>
                                <tr>
                                    <th class="feature-name">Features</th>
                                    <?php foreach ($packages as $p): ?>
                                        <th><?= sanitizeOutput($p['title']) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($uniqueFeatures as $f):
                                    if (empty(trim($f))) continue;
                                ?>
                                    <tr>
                                        <td class="feature-name"><?= sanitizeOutput(trim($f)) ?></td>
                                        <?php foreach ($packages as $p):
                                            $has = in_array(trim($f), array_map('trim', explode('|', $p['features'] ?? '')));
                                        ?>
                                            <td>
                                                <?php if ($has): ?>
                                                    <i class="fas fa-check check-mark"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-times cross-mark"></i>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Share -->
                <div class="social-share">
                    <span class="fw-bold me-2">Share:</span>
                    <a href="https://wa.me/?text=Check%20our%20packages:%20<?= urlencode(currentUrl()) ?>" target="_blank" class="social-icon whatsapp"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(currentUrl()) ?>" target="_blank" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(currentUrl()) ?>" target="_blank" class="social-icon linkedin"><i class="fab fa-linkedin-in"></i></a>
                </div>

                <!-- Prev/Next -->
                <div class="package-navigation">
                    <?php if ($prev): ?>
                        <div class="nav-package prev">
                            <a href="#package-<?= $prev['id'] ?>">
                                <div class="nav-icon"><i class="fas fa-arrow-left"></i></div>
                                <div>
                                    <div class="text-muted small">Previous</div>
                                    <div class="nav-package-title"><?= sanitizeOutput($prev['title']) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php if ($next): ?>
                        <div class="nav-package next">
                            <a href="#package-<?= $next['id'] ?>">
                                <div class="nav-icon"><i class="fas fa-arrow-right"></i></div>
                                <div>
                                    <div class="text-muted small">Next</div>
                                    <div class="nav-package-title"><?= sanitizeOutput($next['title']) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- ==== RIGHT: Sidebar ==== -->
            <div class="col-lg-4">

                <!-- Search -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Search Packages</h3>
                    <form action="" method="get" class="search-box">
                        <input type="text" name="search" placeholder="Search packages..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Categories -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Categories</h3>
                    <ul class="category-list">
                        <li><a href="?" class="<?= empty($_GET['category']) ? 'active' : '' ?>">
                            <span>All Packages</span>
                            <span class="category-count"><?= $total_packages ?></span>
                        </a></li>
                        <?php foreach ($categories as $c): ?>
                            <li>
                                <a href="?category=<?= urlencode($c['category']) ?>"
                                   class="<?= ($_GET['category'] ?? '') === $c['category'] ? 'active' : '' ?>">
                                    <span><?= ucfirst(sanitizeOutput($c['category'])) ?></span>
                                    <span class="category-count"><?= $c['count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Popular -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Popular Packages</h3>
                    <?php foreach ($popular_packages as $p): ?>
                        <div class="popular-package">
                            <div class="popular-package-image">
                                <img src="<?= $p['cover_image'] ?? 'https://via.placeholder.com/70' ?>" alt="">
                            </div>
                            <div>
                                <div class="popular-package-title">
                                    <a href="#package-<?= $p['id'] ?>"><?= sanitizeOutput($p['title']) ?></a>
                                </div>
                                <div class="text-muted small">
                                    <?php if ($p['price_per_sqft'] > 0): ?>
                                        ₹<?= number_format((float)$p['price_per_sqft']) ?>/sq.ft
                                    <?php else: ?>
                                        Custom
                                    <?php endif; ?>
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
                <h2 class="display-5 fw-bold mb-4">Ready to Start Your Dream Project?</h2>
                <p class="lead mb-4">Contact us today for a free consultation and detailed quote.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="/constructioninnagpur/contact.php" class="btn btn-primary btn-lg">Get Free Estimate</a>
                    <a href="/constructioninnagpur/contact.php" class="btn btn-outline-light btn-lg">Schedule Consultation</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
function currentUrl(): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
require_once __DIR__ . '/includes/footer.php';
?>

<script>
document.getElementById('comparisonToggle').addEventListener('change', function () {
    const sec = document.getElementById('comparisonSection');
    sec.style.display = this.checked ? 'block' : 'none';
    if (this.checked) setTimeout(() => sec.scrollIntoView({behavior:'smooth'}), 100);
});
</script>

</body>
</html>