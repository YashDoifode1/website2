<?php
/**
 * packages.php – Grand Jyothi Construction
 * Full page with STICKY SIDEBAR + Smart Comparison Table
 * Uses: packages + package_sections tables
 * FIXED: SITE_URL, paths, consistency
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config.php'; // For SITE_URL

$page_title = 'Construction Packages | Grand Jyothi Construction';

/* ---------- 1. Fetch Active Packages ---------- */
$sql = "SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC, id ASC";
$stmt = executeQuery($sql);
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- 2. Fetch Sections Grouped by Package ---------- */
$sectionsRaw = executeQuery(
    "SELECT s.*, p.title AS package_title 
     FROM package_sections s 
     JOIN packages p ON s.package_id = p.id 
     WHERE s.is_active = 1 AND p.is_active = 1
     ORDER BY p.display_order, s.display_order"
)->fetchAll(PDO::FETCH_ASSOC);

$packageSections = [];
foreach ($sectionsRaw as $s) {
    $packageSections[$s['package_id']][] = $s;
}

/* ---------- 3. Smart Comparison Features (Auto-extracted from real data) ---------- */
$comparisonFeatures = [
    'Price per Sqft'           => fn($p) => '₹' . number_format((float)$p['price_per_sqft']) . '/sqft',
    'Steel Brand'              => 'Structure',
    'Cement Brand'             => 'Structure',
    'Ceiling Height'           => 'Structure',
    'Flooring (Bedrooms/Living)' => 'Flooring',
    'Main Door (Owner)'        => 'Door Frames & Doors',
    'Pooja Door'               => 'Door Frames & Doors',
    'Internal Doors'           => 'Door Frames & Doors',
    'Windows'                  => 'Windows',
    'Interior Painting'        => 'Painting',
    'Exterior Painting'        => 'Painting',
    'Electrical Wires'         => 'Electrical',
    'Switches Brand'           => 'Electrical',
    'Kitchen Countertop'       => 'Kitchen',
    'Kitchen Tiles Height'     => 'Kitchen',
    'Bathroom Fittings Cost'   => 'Bathroom',
    'Bathroom Tiles'           => 'Bathroom',
    'Overhead Tank'            => 'MISCELLANEOUS|Miscellaneous',
    'Underground Sump'         => 'MISCELLANEOUS|Miscellaneous',
    'Staircase Railing'        => 'MISCELLANEOUS|Miscellaneous',
    'Compound Wall'            => 'MISCELLANEOUS|Miscellaneous',
    'Lift Provision'           => 'MISCELLANEOUS|Miscellaneous',
];

/* ---------- 4. Build Comparison Data ---------- */
$comparisonData = [];
foreach ($comparisonFeatures as $label => $source) {
    if ($label === 'Price per Sqft') {
        $comparisonData[$label] = [];
        foreach ($packages as $p) {
            $comparisonData[$label][$p['id']] = $comparisonFeatures[$label]($p);
        }
        continue;
    }

    $sectionTitles = is_string($source) ? explode('|', $source) : [$source];
    $comparisonData[$label] = [];

    foreach ($packages as $p) {
        $value = '–';
        if (!empty($packageSections[$p['id']])) {
            foreach ($packageSections[$p['id']] as $sec) {
                if (in_array($sec['title'], $sectionTitles, true)) {
                    $lines = array_filter(array_map('trim', explode("\n", $sec['content'])));
                    $relevant = [];

                    foreach ($lines as $line) {
                        $line = trim(strip_tags($line));
                        if ($line === '') continue;

                        $keywords = ['steel','cement','ceiling','height','flooring','door','pooja','internal','windows','painting','wires','switches','kitchen','bathroom','tank','sump','railing','compound','lift'];
                        foreach ($keywords as $kw) {
                            if (stripos($line, $kw) !== false) {
                                $relevant[] = $line;
                                break;
                            }
                        }
                    }

                    if (!empty($relevant)) {
                        $value = implode('<br>', array_slice($relevant, 0, 3));
                        break;
                    }
                }
            }
        }
        $comparisonData[$label][$p['id']] = $value;
    }
}

/* ---------- 5. Sidebar Data ---------- */
$categories = executeQuery(
    "SELECT SUBSTRING_INDEX(title, ' ', 1) AS category, COUNT(*) AS count
     FROM packages WHERE is_active = 1 GROUP BY category ORDER BY category"
)->fetchAll(PDO::FETCH_ASSOC);

$total_packages = count($packages);
$popular_packages = array_slice($packages, 0, 3);

require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitizeOutput($page_title) ?></title>

    <!-- Bootstrap + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;
            --light-gray:#f8f9fa;--medium-gray:#e9ecef;--success:#28a745;
        }
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);line-height:1.6;}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}

        /* Smooth Scroll */
        html{scroll-behavior:smooth;}

        /* Buttons */
        .btn-primary{background:var(--primary-yellow);border:none;color:var(--charcoal);font-weight:600;padding:10px 28px;border-radius:8px;transition:.3s;}
        .btn-primary:hover{background:#e89a1f;color:var(--charcoal);}
        .btn-comparison{background:var(--charcoal);color:#fff;border:2px solid var(--charcoal);padding:14px 40px;font-weight:600;border-radius:8px;}
        .btn-comparison:hover{background:var(--primary-yellow);color:var(--charcoal);border-color:var(--primary-yellow);}

        /* Hero */
        .hero-banner{height:500px;background:linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)),
            url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') center/cover;
            display:flex;align-items:flex-end;padding-bottom:60px;color:#fff;position:relative;}
        .hero-banner::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(249,168,38,.15),transparent);}
        .banner-title{font-size:3.5rem;margin:0;line-height:1.2;}
        .banner-subtitle{font-size:1.25rem;opacity:.9;}

        /* Breadcrumb */
        .breadcrumb{background:transparent;padding:0;margin-bottom:20px;}
        .breadcrumb-item a{color:rgba(255,255,255,.8);text-decoration:none;}
        .breadcrumb-item.active{color:var(--primary-yellow);}

        /* Section */
        .packages-section{padding:80px 0;}
        .section-title{font-size:1.9rem;margin-bottom:35px;padding-bottom:12px;
            border-bottom:3px solid var(--primary-yellow);display:inline-block;}

        /* Package Card */
        .package-card{background:#fff;border-radius:12px;overflow:hidden;
            box-shadow:0 6px 20px rgba(0,0,0,.08);transition:.3s;height:100%;position:relative;}
        .package-card:hover{transform:translateY(-10px);box-shadow:0 20px 40px rgba(0,0,0,.15);}
        .package-header{background:var(--charcoal);color:#fff;padding:28px;text-align:center;}
        .package-popular{position:absolute;top:-12px;left:50%;transform:translateX(-50%);
            background:var(--primary-yellow);color:var(--charcoal);padding:6px 22px;
            border-radius:25px;font-size:.85rem;font-weight:600;z-index:10;}
        .package-price{font-size:2.5rem;color:var(--primary-yellow);font-weight:700;margin:15px 0;}
        .package-desc{font-size:.98rem;color:#555;margin-bottom:25px;text-align:center;}

        /* Accordion */
        .accordion-button{font-weight:600;background:#fff;color:var(--charcoal);font-size:1rem;}
        .accordion-button:not(.collapsed){background:var(--primary-yellow);color:var(--charcoal);}
        .accordion-body{font-size:.94rem;color:#444;line-height:1.6;padding:18px;}
        .package-items-list{list-style:none;padding:0;margin:0;}
        .package-items-list li{padding:6px 0;display:flex;align-items:flex-start;}
        .package-items-list li::before{content:"•";color:var(--primary-yellow);font-weight:bold;margin-right:10px;}

        /* Sidebar */
        .sidebar-sticky {
            position: sticky;
            top: 20px;
            max-height: calc(100vh - 40px);
            overflow-y: auto;
            padding-right: 10px;
        }
        .sidebar-sticky::-webkit-scrollbar{width:6px;}
        .sidebar-sticky::-webkit-scrollbar-track{background:#f1f1f1;border-radius:10px;}
        .sidebar-sticky::-webkit-scrollbar-thumb{background:var(--primary-yellow);border-radius:10px;}
        .sidebar-sticky::-webkit-scrollbar-thumb:hover{background:#e89a1f;}

        .sidebar{background:var(--light-gray);border-radius:12px;padding:28px;margin-bottom:24px;}
        .sidebar-title{font-size:1.25rem;margin-bottom:20px;padding-bottom:10px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;}
        .search-box{position:relative;margin-bottom:25px;}
        .search-box input{width:100%;padding:12px 45px 12px 15px;border:1px solid #ddd;border-radius:8px;}
        .search-box button{position:absolute;right:5px;top:5px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);padding:8px 16px;border-radius:6px;font-weight:600;}
        .category-list{list-style:none;padding:0;}
        .category-list li{padding:10px 0;border-bottom:1px solid #eee;}
        .category-list li:last-child{border:none;}
        .category-list a{display:flex;justify-content:space-between;align-items:center;
            color:var(--charcoal);text-decoration:none;transition:.3s;}
        .category-list a:hover,.category-list a.active{color:var(--primary-yellow);font-weight:600;}
        .category-count{background:var(--charcoal);color:#fff;padding:4px 9px;
            border-radius:12px;font-size:.8rem;}
        .popular-package{display:flex;margin-bottom:16px;padding-bottom:16px;
            border-bottom:1px solid #eee;align-items:center;}
        .popular-package:last-child{margin-bottom:0;padding-bottom:0;border:none;}
        .popular-package-image{width:70px;height:70px;border-radius:8px;overflow:hidden;
            margin-right:15px;flex-shrink:0;}
        .popular-package-image img{width:100%;height:100%;object-fit:cover;}
        .popular-package-title a{color:var(--charcoal);font-weight:500;text-decoration:none;}
        .popular-package-title a:hover{color:var(--primary-yellow);}

        /* Comparison Table */
        .comparison-section{background:#fafafa;padding:90px 0;display:none;}
        .comparison-table{width:100%;border-collapse:collapse;font-size:.95rem;}
        .comparison-table th{background:var(--charcoal);color:#fff;padding:16px 12px;vertical-align:middle;text-align:center;font-weight:600;}
        .comparison-table td{padding:14px 12px;border-bottom:1px solid #eee;vertical-align:middle;text-align:center;}
        .comparison-table tr:nth-child(even){background:#fdfdfd;}
        .comparison-table .feature-name{position:sticky;left:0;background:#fff;font-weight:600;
            text-align:left;width:280px;z-index:10;box-shadow:2px 0 5px rgba(0,0,0,.05);}
        .highlight-best{background:#fffbe6 !important;font-weight:600;}
        .table-responsive{border-radius:12px;overflow:hidden;box-shadow:0 6px 25px rgba(0,0,0,.1);}

        /* Share & Nav */
        .social-share{display:flex;gap:12px;margin:40px 0;}
        .social-icon{width:42px;height:42px;border-radius:50%;background:var(--light-gray);
            color:var(--charcoal);display:flex;align-items:center;justify-content:center;font-size:1.1rem;transition:.3s;}
        .social-icon:hover{transform:translateY(-4px);}
        .whatsapp:hover{background:#25d366;color:#fff;}
        .facebook:hover{background:#3b5998;color:#fff;}
        .linkedin:hover{background:#0077b5;color:#fff;}

        .package-navigation{display:flex;justify-content:space-between;padding:45px 0;
            border-top:1px solid #eee;border-bottom:1px solid #eee;margin:45px 0;}
        .nav-package{max-width:45%;}
        .nav-package a{display:flex;align-items:center;text-decoration:none;color:var(--charcoal);transition:.3s;}
        .nav-package a:hover{color:var(--primary-yellow);}
        .nav-icon{font-size:1.6rem;margin:0 16px;color:var(--primary-yellow);}

        /* CTA */
        .cta-section{background:linear-gradient(135deg,var(--charcoal),#2d2d2d);color:#fff;padding:90px 0;text-align:center;}
        .cta-section h2{color:#fff;margin-bottom:1.5rem;}

        /* Responsive */
        @media (max-width:992px){
            .comparison-table{font-size:.9rem;}
            .feature-name{width:220px;}
        }
        @media (max-width:768px){
            .hero-banner{height:400px;padding-bottom:40px;}
            .banner-title{font-size:2.5rem;}
            .package-navigation{flex-direction:column;gap:20px;}
            .nav-package{max-width:100%;}
            .comparison-table{font-size:.82rem;}
            .feature-name{position:relative;width:auto;background:#fff !important;}
        }
    </style>
</head>
<body>

<!-- ====================== HERO ====================== -->
<section class="hero-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Packages</li>
            </ol>
        </nav>
        <h1 class="banner-title">Construction Packages</h1>
        <p class="banner-subtitle">Premium quality • Transparent pricing • 100% satisfaction guaranteed</p>
    </div>
</section>

<!-- ====================== MAIN CONTENT ====================== -->
<section class="packages-section">
    <div class="container">
        <div class="row">

            <!-- LEFT: Packages -->
            <div class="col-lg-8">

                <?php if (empty($packages)): ?>
                    <div class="text-center py-5">
                        <p class="lead text-muted">No packages available at the moment.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($packages as $p): ?>
                            <div class="col-md-6">
                                <div class="package-card" id="package-<?= $p['id'] ?>">
                                    <?php if ($p['display_order'] == 3): // Diamond is most popular ?>
                                        <div class="package-popular">MOST POPULAR</div>
                                    <?php endif; ?>
                                    <div class="package-header">
                                        <h3 class="mb-0"><?= sanitizeOutput($p['title']) ?></h3>
                                    </div>
                                    <div class="package-body p-4">
                                        <div class="package-price">
                                            ₹<?= number_format((float)$p['price_per_sqft']) ?><small class="text-muted">/sqft</small>
                                        </div>
                                        <p class="package-desc"><?= sanitizeOutput($p['description']) ?></p>

                                        <?php if (!empty($packageSections[$p['id']])): ?>
                                            <div class="accordion" id="accordion<?= $p['id'] ?>">
                                                <?php foreach ($packageSections[$p['id']] as $i => $s):
                                                    $collapseId = "collapse{$p['id']}_{$i}";
                                                    $contentLines = array_filter(array_map('trim', explode("\n", $s['content'])));
                                                    $formatted = '<ul class="package-items-list">';
                                                    foreach ($contentLines as $line) {
                                                        if ($line !== '') {
                                                            $formatted .= '<li>' . sanitizeOutput($line) . '</li>';
                                                        }
                                                    }
                                                    $formatted .= '</ul>';
                                                ?>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header">
                                                            <button class="accordion-button <?= $i>0?'collapsed':'' ?>"
                                                                    type="button" data-bs-toggle="collapse"
                                                                    data-bs-target="#<?= $collapseId ?>"
                                                                    aria-expanded="<?= $i===0?'true':'false' ?>">
                                                                <?= sanitizeOutput($s['title']) ?>
                                                            </button>
                                                        </h2>
                                                        <div id="<?= $collapseId ?>" class="accordion-collapse collapse <?= $i===0?'show':'' ?>"
                                                             data-bs-parent="#accordion<?= $p['id'] ?>">
                                                            <div class="accordion-body">
                                                                <?= $formatted ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="text-center mt-4">
                                            <a href="<?= SITE_URL ?>/select-plan.php?plan=<?= urlencode($p['title']) ?>"
                                               class="btn btn-primary w-100">Select This Package</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Show Comparison Button -->
                <?php if (count($packages) > 1): ?>
                    <div class="text-center my-5">
                        <button id="showComparisonBtn" class="btn btn-comparison btn-lg">
                            <i class="fas fa-table me-2"></i> Compare All Packages
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Share -->
                <div class="social-share">
                    <span class="fw-bold me-2">Share:</span>
                    <a href="https://wa.me/?text=Check%20our%20packages:%20<?= urlencode(currentUrl()) ?>" target="_blank" class="social-icon whatsapp"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(currentUrl()) ?>" target="_blank" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(currentUrl()) ?>" target="_blank" class="social-icon linkedin"><i class="fab fa-linkedin-in"></i></a>
                </div>

                <!-- Prev/Next -->
                <?php
                $prev = $next = null;
                $ids = array_column($packages, 'id');
                $currentIdx = 0;
                if (isset($ids[$currentIdx - 1])) {
                    $prev = executeQuery("SELECT title, id FROM packages WHERE id = ?", [$ids[$currentIdx - 1]])->fetch();
                }
                if (isset($ids[$currentIdx + 1])) {
                    $next = executeQuery("SELECT title, id FROM packages WHERE id = ?", [$ids[$currentIdx + 1]])->fetch();
                }
                ?>
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

            <!-- RIGHT: STICKY SIDEBAR -->
            <div class="col-lg-4">
                <div class="sidebar-sticky">

                    <!-- Search -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Search Packages</h3>
                        <form action="<?= SITE_URL ?>/packages.php" method="get" class="search-box">
                            <input type="text" name="search" placeholder="Search..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Categories</h3>
                        <ul class="category-list">
                            <li><a href="<?= SITE_URL ?>/packages.php" class="<?= empty($_GET['category']) ? 'active' : '' ?>">
                                <span>All Packages</span>
                                <span class="categorywend-count"><?= $total_packages ?></span>
                            </a></li>
                            <?php foreach ($categories as $c): ?>
                                <li>
                                    <a href="<?= SITE_URL ?>/packages.php?category=<?= urlencode($c['category']) ?>"
                                       class="<?= ($_GET['category'] ?? '') === $c['category'] ? 'active' : '' ?>">
                                        <span><?= ucfirst(sanitizeOutput($c['category'])) ?></span>
                                        <span class="category-count"><?= $c['count'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Popular Packages (Uncomment when images are added) -->
                    <!--
                    <div class="sidebar">
                        <h3 class="sidebar-title">Popular Packages</h3>
                        <?php foreach ($popular_packages as $p): ?>
                            <div class="popular-package">
                                <div class="popular-package-image">
                                    <img src="<?= $p['cover_image'] ?? 'https://via.placeholder.com/70' ?>" alt="<?= sanitizeOutput($p['title']) ?>" loading="lazy">
                                </div>
                                <div>
                                    <div class="popular-package-title">
                                        <a href="#package-<?= $p['id'] ?>"><?= sanitizeOutput($p['title']) ?></a>
                                    </div>
                                    <div class="text-muted small">
                                        ₹<?= number_format((float)$p['price_per_sqft']) ?>/sqft
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    -->

                </div>
            </div>
        </div>

        <!-- ====================== COMPARISON TABLE ====================== -->
        <div class="comparison-section" id="comparisonSection">
            <div class="container">
                <h2 class="section-title text-center mb-5">Package Comparison</h2>
                <div class="table-responsive">
                    <table class="comparison-table">
                        <thead>
                            <tr>
                                <th class="feature-name">Feature</th>
                                <?php foreach ($packages as $p): ?>
                                    <th>
                                        <strong><?= sanitizeOutput($p['title']) ?></strong><br>
                                        <span style="color:var(--primary-yellow);font-size:1.4rem;font-weight:700;">
                                            ₹<?= number_format((float)$p['price_per_sqft']) ?>/sqft
                                        </span>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($comparisonData as $feature => $values): ?>
                                <tr>
                                    <td class="feature-name"><strong><?= $feature ?></strong></td>
                                    <?php foreach ($packages as $p): 
                                        $val = $values[$p['id']] ?? '–';
                                        $isBest = in_array($p['display_order'], [5]) || 
                                                 (strpos($val, 'TATA') !== false) ||
                                                 (strpos($val, 'Italian marble') !== false) ||
                                                 (strpos($val, 'Burma Teak') !== false) ||
                                                 (strpos($val, 'Jaguar') !== false && $p['display_order'] >= 4);
                                    ?>
                                        <td <?= $isBest ? 'class="highlight-best"' : '' ?>>
                                            <?= $val === '–' ? '<span class="text-muted">–</span>' : $val ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-4">
                    <button id="hideComparisonBtn" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i> Hide Comparison
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ====================== CTA ====================== -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">Ready to Build Your Dream Home?</h2>
        <p class="lead mb-4">Get a free consultation and detailed quote today.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary btn-lg">Get Free Estimate</a>
            <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline-light btn-lg">Schedule Call</a>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const showBtn = document.getElementById('showComparisonBtn');
    const hideBtn = document.getElementById('hideComparisonBtn');
    const section = document.getElementById('comparisonSection');

    if (showBtn && section) {
        showBtn.addEventListener('click', () => {
            section.style.display = 'block';
            setTimeout(() => section.scrollIntoView({ behavior: 'smooth', block: 'start' }), 100);
            showBtn.style.display = 'none';
        });
    }

    if (hideBtn && section) {
        hideBtn.addEventListener('click', () => {
            section.style.display = 'none';
            showBtn.style.display = 'inline-block';
        });
    }
});
</script>

</body>
</html>