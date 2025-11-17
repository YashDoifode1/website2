<?php
/**
 * services.php – Our Construction Services
 * Sidebar on LEFT | Main content on RIGHT | 100% aligned with theme
 * FIXED: Correct image paths + Feather Icons + SITE_URL syntax
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config.php';

$page_title = 'Our Services | Grand Jyothi Construction';

// ---------- 1. Filters ----------
$category_filter = trim($_GET['category'] ?? '');
$search_term     = trim($_GET['search'] ?? '');
$page            = max(1, (int)($_GET['page'] ?? 1));
$per_page        = 6;
$offset          = ($page - 1) * $per_page;

// ---------- 2. Build query for services ----------
$sql = "SELECT id, title, description, icon, slug, cover_image, icon_image
        FROM services 
        WHERE 1=1";
$params = [];

if ($search_term !== '') {
    $sql .= " AND (title LIKE ? OR description LIKE ?)";
    $pattern = "%$search_term%";
    $params[] = $pattern;
    $params[] = $pattern;
}

if ($category_filter !== '') {
    $sql .= " AND title LIKE ?";
    $params[] = $category_filter . '%';
}

// Count total services
$count_sql = "SELECT COUNT(*) FROM services WHERE 1=1";
$count_params = [];

if ($search_term !== '') {
    $count_sql .= " AND (title LIKE ? OR description LIKE ?)";
    $count_params[] = $pattern;
    $count_params[] = $pattern;
}

if ($category_filter !== '') {
    $count_sql .= " AND title LIKE ?";
    $count_params[] = $category_filter . '%';
}

$total_services = (int)executeQuery($count_sql, $count_params)->fetchColumn();
$total_pages = max(1, (int)ceil($total_services / $per_page));

// Final services query
$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$services = executeQuery($sql, $params)->fetchAll();

// ---------- 3. Auto Categories ----------
$categories = executeQuery("
    SELECT SUBSTRING_INDEX(title, ' ', 1) AS category, COUNT(*) AS count
    FROM services
    GROUP BY category
    ORDER BY category
")->fetchAll();

// ---------- 4. Popular services ----------
$popular_services = executeQuery("
    SELECT id, title, cover_image 
    FROM services 
    ORDER BY created_at DESC 
    LIMIT 3
")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitizeOutput($page_title) ?></title>

    <!-- Bootstrap + Font Awesome + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <!-- FEATHER ICONS -->
    <script src="https://unpkg.com/feather-icons"></script>

    <style>
        :root{
            --primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;
            --light-gray:#f8f9fa;--medium-gray:#e9ecef;
        }
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);line-height:1.6;}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}

        /* ==== HERO ==== */
        .services-banner{
            height:500px;background:linear-gradient(rgba(26,26,26,.7),rgba(26,26,26,.7)),
            url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
            center/cover no-repeat;display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;
        }
        .services-banner::before{content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.15) 0%,transparent 70%);}
        .banner-title{font-size:3rem;margin:0;line-height:1.2;}
        .banner-subtitle{font-size:1.2rem;opacity:.9;margin-bottom:1.5rem;}

        /* ==== BREADCRUMB ==== */
        .breadcrumb{background:transparent;padding:0;margin-bottom:1rem;}
        .breadcrumb-item a{color:rgba(255,255,255,.8);text-decoration:none;}
        .breadcrumb-item.active{color:var(--primary-yellow);}

        /* ==== SEARCH ==== */
        .hero-search{max-width:500px;margin:0 auto;position:relative;}
        .hero-search input{width:100%;padding:14px 50px 14px 20px;border-radius:50px;border:none;font-size:1rem;}
        .hero-search button{position:absolute;right:8px;top:8px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);width:40px;height:40px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;font-size:1.1rem;}

        /* ==== SECTION ==== */
        .services-section{padding:80px 0;background:var(--light-gray);}
        .section-title{font-size:1.8rem;margin-bottom:30px;padding-bottom:10px;
            border-bottom:3px solid var(--primary-yellow);display:inline-block;position:relative;}
        .section-title::after{content:'';position:absolute;bottom:-12px;left:0;
            width:60px;height:4px;background:var(--primary-yellow);border-radius:2px;}

        /* ==== GRID ==== */
        .services-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
            gap:30px;
            align-items:stretch;
        }
        .service-card{
            background:var(--white);border-radius:12px;overflow:hidden;
            box-shadow:0 6px 20px rgba(0,0,0,.06);transition:all .3s;
            display:flex;flex-direction:column;height:100%;
            text-decoration:none;color:inherit;
        }
        .service-card:hover{transform:translateY(-8px);box-shadow:0 15px 30px rgba(0,0,0,.12);}
        .service-cover{height:140px;background-size:cover;background-position:center;background-color:#eee;}
        .service-icon{color:var(--primary-yellow);font-size:2.4rem;margin:20px auto 15px;display:flex;justify-content:center;}
        .service-icon img{width:48px;height:48px;object-fit:contain;border-radius:6px;}
        .service-title{font-size:1.3rem;margin:0 20px 12px;text-align:center;color:var(--charcoal);font-weight:600;}
        .service-desc{color:#555;font-size:.94rem;line-height:1.5;padding:0 20px 25px;text-align:center;flex:1;}

        /* ==== SIDEBAR (LEFT) ==== */
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

        /* ==== PAGINATION ==== */
        .pagination{justify-content:center;margin-top:50px;}
        .page-link{color:var(--charcoal);border:1px solid #ddd;padding:10px 16px;border-radius:6px;}
        .page-link:hover{background:var(--light-gray);border-color:#ddd;}
        .page-item.active .page-link{background:var(--primary-yellow);border-color:var(--primary-yellow);color:var(--charcoal);}

        /* ==== NO SERVICES ==== */
        .no-services{text-align:center;padding:60px 0;}
        .no-services-icon{font-size:4rem;color:#ddd;margin-bottom:20px;}

        /* ==== CTA ==== */
        .cta-section{background:linear-gradient(135deg,var(--charcoal) 0%,#2d2d2d 100%);
            color:var(--white);padding:80px 0;text-align:center;}
        .cta-section h2{color:var(--white);margin-bottom:1.5rem;}

        /* ==== PROCESS ==== */
        .process-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(260px,1fr));
            gap:25px;
        }
        .process-card{
            background:var(--white);border-radius:12px;padding:30px;
            box-shadow:0 6px 20px rgba(0,0,0,.05);text-align:center;
            transition:all .3s;height:100%;
        }
        .process-card:hover{
            transform:translateY(-8px);box-shadow:0 15px 30px rgba(0,0,0,.12);
        }
        .process-number{
            width:50px;height:50px;background:var(--primary-yellow);
            color:var(--charcoal);border-radius:50%;display:flex;align-items:center;
            justify-content:center;font-size:1.5rem;font-weight:700;margin:0 auto 15px;
        }

        /* ==== SERVICE AREAS ==== */
        .areas-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(250px,1fr));
            gap:25px;
        }
        .area-card{
            background:var(--light-gray);border-radius:12px;padding:25px;
            text-align:center;transition:all .3s;
        }
        .area-card:hover{
            background:var(--primary-yellow);color:var(--charcoal);
            transform:translateY(-5px);
        }
        .area-icon{
            color:var(--primary-yellow);font-size:1.8rem;margin-bottom:15px;
            transition:color .3s;
        }
        .area-card:hover .area-icon{color:var(--charcoal);}

        /* ==== RESPONSIVE ==== */
        @media (max-width:992px){
            .services-banner{height:400px;padding:40px 0;}
            .banner-title{font-size:2.4rem;}
            .services-section .row{flex-direction:column;}
            .services-grid{grid-template-columns:1fr;}
        }
        @media (max-width:576px){
            .banner-title{font-size:2rem;}
        }
    </style>
</head>
<body>

<!-- ====================== HERO ====================== -->
<section class="services-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
        </nav>
        <h1 class="banner-title">Our Construction Services</h1>
        <p class="banner-subtitle">Comprehensive solutions from concept to completion</p>
        <form action="" method="get" class="hero-search">
            <input type="text" name="search" placeholder="Search services..." value="<?= sanitizeOutput($search_term) ?>">
            <?php if ($category_filter): ?>
                <input type="hidden" name="category" value="<?= sanitizeOutput($category_filter) ?>">
            <?php endif; ?>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</section>

<!-- ====================== MAIN + SIDEBAR (LEFT) ====================== -->
<main class="services-section">
    <div class="container">
        <div class="row g-5">

            <!-- ==== SIDEBAR (LEFT) ==== -->
            <aside class="col-lg-3">
                <div class="sticky-top" style="top:90px;">

                    <!-- SEARCH -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Search Services</h3>
                        <form action="" method="get" class="search-box">
                            <input type="text" name="search" placeholder="Search services..." value="<?= sanitizeOutput($search_term) ?>">
                            <?php if ($category_filter): ?>
                                <input type="hidden" name="category" value="<?= sanitizeOutput($category_filter) ?>">
                            <?php endif; ?>
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- CATEGORIES -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Categories</h3>
                        <ul class="category-list">
                            <li>
                                <a href="<?= SITE_URL ?>/services.php" class="<?= empty($category_filter) && empty($search_term) ? 'active' : '' ?>">
                                    <span>All Services</span>
                                    <span class="category-count"><?= $total_services ?></span>
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="?category=<?= urlencode($cat['category']) ?>&search=<?= urlencode($search_term) ?>"
                                       class="<?= $category_filter === $cat['category'] ? 'active' : '' ?>">
                                        <span><?= sanitizeOutput(ucfirst($cat['category'])) ?></span>
                                        <span class="category-count"><?= $cat['count'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- POPULAR SERVICES -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Popular Services</h3>
                        <?php foreach ($popular_services as $p): ?>
                            <div class="popular-service">
                                <?php if (!empty($p['cover_image'])): ?>
                                    <div class="popular-service-image">
                                        <img src="<?= SITE_URL ?><?= sanitizeOutput($p['cover_image']) ?>" 
                                             alt="<?= sanitizeOutput($p['title']) ?>" loading="lazy">
                                    </div>
                                <?php endif; ?>
                                <div class="popular-service-content">
                                    <div class="popular-service-title">
                                        <a href="<?= SITE_URL ?>/service-info.php?id=<?= (int)$p['id'] ?>">
                                            <?= sanitizeOutput($p['title']) ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </aside>

            <!-- ==== MAIN: Services Grid (RIGHT) ==== -->
            <div class="col-lg-9">

                <div class="services-grid" id="servicesGrid">
                    <?php if (empty($services)): ?>
                        <div class="no-services">
                            <div class="no-services-icon"><i class="fas fa-tools"></i></div>
                            <h3>No Services Found</h3>
                            <p>Try adjusting your search or filter criteria.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($services as $s):
                            $icon  = !empty($s['icon']) ? sanitizeOutput($s['icon']) : 'tool';
                            $title = sanitizeOutput($s['title']);
                            $desc  = sanitizeOutput(substr(strip_tags($s['description']), 0, 120)) . '...';
                            $slug  = !empty($s['slug']) 
                                ? sanitizeOutput($s['slug']) 
                                : strtolower(preg_replace('/[^a-z0-9]+/', '-', $s['title']));
                            
                            $cover = !empty($s['cover_image']) 
                                ? SITE_URL . sanitizeOutput($s['cover_image']) 
                                : 'https://via.placeholder.com/300x140/eee/ccc?text=Service';
                        ?>
                            <a href="<?= SITE_URL ?>/service-info.php?slug=<?= urlencode($slug) ?>" class="service-card">
                                <div class="service-cover" style="background-image:url('<?= $cover ?>');"></div>
                                
                                <div class="service-icon">
                                    <?php if (!empty($s['icon_image'])): ?>
                                        <img src="<?= SITE_URL ?><?= sanitizeOutput($s['icon_image']) ?>" alt="icon" loading="lazy">
                                    <?php else: ?>
                                        <i data-feather="<?= $icon ?>"></i>
                                    <?php endif; ?>
                                </div>

                                <h3 class="service-title"><?= $title ?></h3>
                                <p class="service-desc"><?= $desc ?></p>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Our Process -->
                <div class="mt-5">
                    <h2 class="section-title text-center">Our Proven Process</h2>
                    <div class="process-grid">
                        <?php
                        $steps = [
                            ['Consultation','We begin with a detailed consultation to understand your requirements, budget, and timeline.'],
                            ['Planning & Design','Our architects and engineers create detailed plans and designs that align with your vision.'],
                            ['Execution','Skilled team begins construction using quality materials and modern techniques.'],
                            ['Quality Check','Regular inspections ensure the highest standards are maintained throughout.'],
                            ['Handover','Final walkthrough, documentation, and handover with complete satisfaction.'],
                            ['After-Sales Support','Ongoing support and warranty services to ensure long-term satisfaction.']
                        ];
                        foreach ($steps as $i => $step):
                        ?>
                            <div class="process-card">
                                <div class="process-number"><?= $i + 1 ?></div>
                                <h4 class="h5 fw-bold"><?= sanitizeOutput($step[0]) ?></h4>
                                <p class="small"><?= sanitizeOutput($step[1]) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Service Areas -->
                <div class="mt-5">
                    <h2 class="section-title text-center">Our Service Areas</h2>
                    <p class="text-center mb-5 lead text-muted">Serving clients across Nagpur and surrounding regions</p>
                    <div class="areas-grid">
                        <?php
                        $areas = [
                            ['Nagpur City','Core city and surrounding areas'],
                            ['Dharampeth','Premium residential & commercial zone'],
                            ['Sadar','Central business district'],
                            ['Ramdaspeth','High-end residential area'],
                            ['Civil Lines','Heritage and government zone'],
                            ['Sitabuldi','Commercial & market hub'],
                            ['Wardha Road','Industrial & logistics corridor'],
                            ['Kamptee','Suburban residential growth'],
                            ['Hingna','Industrial & manufacturing zone'],
                            ['Koradi','Emerging residential area'],
                            ['Manish Nagar','Modern residential locality'],
                            ['And Surrounding Areas','We serve all nearby regions']
                        ];
                        foreach ($areas as $a):
                        ?>
                            <div class="area-card">
                                <div class="area-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <h4><?= sanitizeOutput($a[0]) ?></h4>
                                <p><?= sanitizeOutput($a[1]) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Services pagination">
                    <ul class="pagination">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search_term) ?>&category=<?= urlencode($category_filter) ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search_term) ?>&category=<?= urlencode($category_filter) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search_term) ?>&category=<?= urlencode($category_filter) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<!-- ====================== CTA ====================== -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-4">Ready to Build Your Dream?</h2>
                <p class="lead mb-4">Let's discuss your vision and create something extraordinary together</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary btn-lg">
                        Get Free Consultation
                    </a>
                    <a href="<?= SITE_URL ?>/projects.php" class="btn btn-outline-light btn-lg">
                        View Projects
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
    feather.replace();
</script>

</body>
</html>