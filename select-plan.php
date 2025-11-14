<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Select Your Plan | Grand Jyothi Construction';
$success_message = $error_message = '';
$selected_plan = $_GET['plan'] ?? '';
$package = null;

// Fetch package
if ($selected_plan) {
    $stmt = executeQuery("SELECT * FROM packages WHERE title = ? AND is_active = 1", [$selected_plan]);
    $package = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($package) {
        $page_title = sanitizeOutput($package['title']) . ' - Select Plan';
    } else {
        $error_message = 'Package not found.';
    }
}

// Form handling (same as before)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... [your existing validation & insert logic] ...
}

// Sidebar data
$categories = executeQuery("SELECT SUBSTRING_INDEX(title,' ',1) AS cat, COUNT(*) AS cnt FROM packages WHERE is_active=1 GROUP BY cat")->fetchAll();
$total_packages = executeQuery("SELECT COUNT(*) FROM packages WHERE is_active=1")->fetchColumn();
$popular_packages = executeQuery("SELECT title, price_per_sqft FROM packages WHERE is_active=1 ORDER BY display_order LIMIT 3")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root{--primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;--light-gray:#f8f9fa;}
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}
        .btn-primary{background:var(--primary-yellow);border-color:var(--primary-yellow);color:var(--charcoal);font-weight:600;padding:10px 25px;border-radius:8px;}
        .btn-primary:hover{background:#e89a1f;border-color:#e89a1f;}
        .form-control{padding:12px 15px;border-radius:5px;border:1px solid #ddd;}
        .form-control:focus{border-color:var(--primary-yellow);box-shadow:0 0 0 .25rem rgba(249,168,38,.25);}
        .sidebar-card{background:var(--light-gray);border-radius:10px;padding:25px;margin-bottom:25px;box-shadow:0 3px 10px rgba(0,0,0,.05);}
        .sidebar-title{font-size:1.2rem;margin-bottom:20px;padding-bottom:10px;border-bottom:2px solid var(--primary-yellow);display:inline-block;}
        .search-box{position:relative;}
        .search-box input{width:100%;padding:12px 40px 12px 15px;border-radius:50px;}
        .search-box button{position:absolute;right:8px;top:8px;background:var(--primary-yellow);border:none;color:var(--charcoal);width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;}
        .category-list{list-style:none;padding:0;}
        .category-list a{display:flex;justify-content:space-between;align-items:center;padding:10px 0;color:var(--charcoal);text-decoration:none;border-bottom:1px solid #eee;}
        .category-list a:hover,.category-list a.active{color:var(--primary-yellow);font-weight:600;}
        .popular-package{display:flex;gap:12px;margin-bottom:15px;padding-bottom:15px;border-bottom:1px solid #eee;}
        .popular-package:last-child{margin-bottom:0;padding-bottom:0;border:none;}
        .popular-package-image{width:60px;height:60px;border-radius:8px;overflow:hidden;flex-shrink:0;}
        .popular-package-image img{width:100%;height:100%;object-fit:cover;}
        .popular-package-title a{color:var(--charcoal);font-weight:500;text-decoration:none;}
        .popular-package-title a:hover{color:var(--primary-yellow);}
        .package-card{background:var(--white);border-radius:10px;padding:30px;box-shadow:0 5px 15px rgba(0,0,0,.05);}
        .package-card:hover{transform:translateY(-5px);box-shadow:0 15px 30px rgba(0,0,0,.1);}
        .feature-list{list-style:none;padding:0;}
        .feature-list li{display:flex;align-items:flex-start;gap:10px;padding:8px 0;color:#555;}
        .feature-list i{color:var(--primary-yellow);margin-top:3px;}
        @media (max-width:992px){
            .sticky-top{position:static !important;}
        }
    </style>
</head>
<body>

<!-- HERO -->
<section class="hero-section" style="background:linear-gradient(rgba(26,26,26,.7),rgba(26,26,26,.7)),url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5') center/cover;height:450px;display:flex;align-items:flex-end;padding:60px 0;color:white;position:relative;">
    <div class="container position-relative z-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/" style="color:rgba(255,255,255,.8)">Home</a></li>
                <li class="breadcrumb-item"><a href="/constructioninnagpur/packages.php" style="color:rgba(255,255,255,.8)">Packages</a></li>
                <li class="breadcrumb-item active text-warning"><?= $package ? sanitizeOutput($package['title']) : 'Select Plan' ?></li>
            </ol>
        </nav>
        <h1 class="display-4 fw-bold"><?= $package ? sanitizeOutput($package['title']) : 'Select Plan' ?></h1>
        <p class="lead">Get a free quote in 24 hours</p>
    </div>
</section>

<!-- MAIN + ASIDE -->
<main class="container my-5">
    <div class="row g-5">

        <!-- MAIN CONTENT -->
        <div class="col-lg-8">

            <?php if (!$package): ?>
                <div class="alert alert-warning text-center">
                    No package selected. <a href="/constructioninnagpur/packages.php" class="alert-link">Choose a package</a> first.
                </div>
            <?php else: ?>

                <?php if ($success_message): ?>
                    <div class="alert alert-success text-center"><?= $success_message ?></div>
                <?php elseif ($error_message): ?>
                    <div class="alert alert-danger text-center"><?= $error_message ?></div>
                <?php endif; ?>

                <!-- Enquiry Form -->
                <div class="card border-0 shadow-sm p-4">
                    <h3 class="mb-4">Request Quote</h3>
                    <form method="POST" id="enquiryForm">
                        <?= getCsrfTokenField() ?>
                        <input type="hidden" name="selected_plan" value="<?= sanitizeOutput($package['title']) ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Phone *</label>
                                <input type="tel" name="phone" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message (Optional)</label>
                                <textarea name="message" class="form-control" rows="3" placeholder="Plot size, timeline..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">Submit Enquiry</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Package Summary -->
                <div class="package-card mt-4">
                    <h4><?= sanitizeOutput($package['title']) ?></h4>
                    <div class="h3 text-warning">
                        <?php if ($package['price_per_sqft'] > 0): ?>
                            ₹<?= number_format((float)$package['price_per_sqft']) ?>/sq.ft
                        <?php else: ?> Custom Quote <?php endif; ?>
                    </div>
                    <p class="mt-3"><?= nl2br(sanitizeOutput($package['description'])) ?></p>
                    <?php if ($package['features']): ?>
                        <ul class="feature-list">
                            <?php foreach (array_filter(array_map('trim', explode('|', $package['features']))) as $f): ?>
                                <li><i class="fas fa-check"></i> <?= sanitizeOutput($f) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

            <?php endif; ?>

        </div>

        <!-- SIDEBAR (ASIDE) -->
        <aside class="col-lg-4">
            <div class="sticky-top" style="top: 2rem;">

                <!-- SEARCH -->
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Search Packages</h3>
                    <form action="/constructioninnagpur/packages.php" method="get" class="search-box">
                        <input type="text" name="search" placeholder="Type to search..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- CATEGORIES -->
                <div class="sidebar-card">
                    <h3 class="sidebar-title">Categories</h3>
                    <ul class="category-list">
                        <li><a href="/constructioninnagpur/packages.php" class="<?= empty($_GET['category']) ? 'active' : '' ?>">
                            <span>All Packages</span>
                            <span class="badge bg-dark text-white"><?= $total_packages ?></span>
                        </a></li>
                        <?php foreach ($categories as $c): ?>
                            <li><a href="/constructioninnagpur/packages.php?category=<?= urlencode($c['cat']) ?>" class="<?= ($_GET['category'] ?? '') === $c['cat'] ? 'active' : '' ?>">
                                <span><?= ucfirst(sanitizeOutput($c['cat'])) ?></span>
                                <span class="badge bg-dark text-white"><?= $c['cnt'] ?></span>
                            </a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- POPULAR -->
                <div class="sidebar-card">
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
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>