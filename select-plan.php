<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config.php';

$page_title      = 'Select Your Plan | Grand Jyothi Construction';
$success_message = '';
$error_message   = '';
$selected_plan   = trim((string)($_GET['plan'] ?? ''));
$package         = null;

/* -------------------------------------------------
 * 1. FETCH PACKAGE
 * ------------------------------------------------- */
if ($selected_plan !== '') {
    $stmt = executeQuery(
        "SELECT * FROM packages WHERE title = ? LIMIT 1",
        [$selected_plan]
    );
    $package = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    if ($package) {
        $page_title = sanitizeOutput($package['title']) . ' - Select Plan';
    } else {
        $error_message = 'Package not found.';
    }
}

/* -------------------------------------------------
 * 2. FORM SUBMISSION
 * ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid security token. Please try again.';
    } elseif (!checkRateLimit('select_plan_form', 5, 300)) {
        $remaining     = getRateLimitRemaining('select_plan_form', 300);
        $error_message = 'Too many submissions. Please try again in ' . ceil($remaining / 60) . ' minutes.';
    } else {
        $first_name   = sanitizeInput(trim($_POST['first_name'] ?? ''));
        $last_name    = sanitizeInput(trim($_POST['last_name'] ?? ''));
        $email        = sanitizeInput(trim($_POST['email'] ?? ''));
        $phone        = sanitizeInput(trim($_POST['phone'] ?? ''));
        $project_type = sanitizeInput(trim($_POST['project_type'] ?? ''));
        $budget       = sanitizeInput(trim($_POST['budget'] ?? ''));
        $message      = sanitizeInput(trim($_POST['message'] ?? ''));
        $plan         = sanitizeInput(trim($_POST['selected_plan'] ?? ''));

        $errors = [];
        if ($first_name === '' || strlen($first_name) < 2) $errors[] = 'Valid first name required.';
        if ($last_name  === '' || strlen($last_name)  < 2) $errors[] = 'Valid last name required.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
        if ($phone === '' || !preg_match('/^\+?\d{10,15}$/', $phone)) $errors[] = 'Valid phone (10-15 digits) required.';
        if ($plan  === '') $errors[] = 'Plan not selected.';

        if (!empty($errors)) {
            $error_message = implode('<br>', $errors);
        } else {
            try {
                $sql = "INSERT INTO contact_messages
                        (first_name, last_name, email, phone,
                         project_type, budget, message,
                         selected_plan, submitted_at,
                         ip_address, user_agent, created_at)
                        VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, NOW(),
                         ?, ?, NOW())";

                $params = [
                    $first_name, $last_name, $email, $phone,
                    $project_type ?: null, $budget ?: null, $message, $plan,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                ];

                executeQuery($sql, $params);

                $success_message = "Thank you for choosing <strong>" . htmlspecialchars($plan) . "</strong>!<br>We'll contact you within 24 hours.";
                logSecurityEvent("Plan enquiry submitted: $plan", 'INFO');

                // Reset
                $_POST = [];
            } catch (Throwable $e) {
                $error_message = 'Error submitting your enquiry. Please try again.';
                error_log('Plan Enquiry Error: ' . $e->getMessage());
            }
        }
    }
}

/* -------------------------------------------------
 * 3. SIDEBAR DATA
 * ------------------------------------------------- */
$categories = executeQuery("
    SELECT SUBSTRING_INDEX(title,' ',1) AS cat, COUNT(*) AS cnt
    FROM packages
    GROUP BY cat
    ORDER BY cnt DESC
")->fetchAll(PDO::FETCH_ASSOC);

$total_packages = (int)executeQuery("SELECT COUNT(*) FROM packages")->fetchColumn();

$popular_packages = executeQuery("
    SELECT title, price_per_sqft
    FROM packages
    ORDER BY created_at DESC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= sanitizeOutput($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root{
            --primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;--light-gray:#f8f9fa;
            --radius:12px;--shadow:0 8px 30px rgba(6,6,6,.06);
        }
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}

        .btn-primary{
            background:var(--primary-yellow);color:var(--charcoal);border:0;font-weight:600;
            border-radius:10px;padding:12px 24px;transition:.3s;
        }
        .btn-primary:hover{filter:brightness(.95);transform:translateY(-1px);}

        .form-control, .form-select{
            padding:12px 16px;border-radius:8px;border:1px solid #ddd;font-size:1rem;
            transition:border .3s;
        }
        .form-control:focus, .form-select:focus{
            border-color:var(--primary-yellow);box-shadow:0 0 0 .25rem rgba(249,168,38,.25);
        }

        /* HERO */
        .hero{
            background:linear-gradient(rgba(26,26,26,.7),rgba(26,26,26,.7)),
                       url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
                       center/cover no-repeat;
            color:#fff;padding:80px 0;display:flex;align-items:flex-end;position:relative;
            min-height:400px;
        }
        .hero::before{
            content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.15) 0%,transparent 70%);
        }
        .hero .breadcrumb a{color:rgba(255,255,255,.85);text-decoration:none;}
        .hero .breadcrumb-item.active{color:var(--primary-yellow);}

        .package-card{
            background:#fff;border-radius:var(--radius);padding:32px;
            box-shadow:var(--shadow);transition:.3s;margin-bottom:24px;
        }
        .package-card:hover{transform:translateY(-5px);box-shadow:0 15px 35px rgba(0,0,0,.1);}

        .sidebar-card{
            background:var(--light-gray);border-radius:var(--radius);padding:24px;
            box-shadow:var(--shadow);margin-bottom:24px;
        }
        .sticky-sidebar{position:sticky;top:100px;}
        @media (max-width:991px){.sticky-sidebar{position:static;}}

        .popular-package{
            display:flex;gap:14px;align-items:center;padding:14px 0;
            border-bottom:1px solid rgba(0,0,0,.05);
        }
        .popular-package:last-child{border-bottom:none;padding-bottom:0;}
        .popular-thumb{
            width:60px;height:60px;border-radius:10px;overflow:hidden;flex-shrink:0;
            background:#eee;display:flex;align-items:center;justify-content:center;
        }
        .popular-thumb i{font-size:1.6rem;color:#999;}

        .feature-list{list-style:none;padding:0;margin:0;}
        .feature-list li{
            display:flex;gap:12px;align-items:flex-start;padding:10px 0;color:#444;
            font-size:.95rem;
        }
        .feature-list i{color:var(--primary-yellow);font-size:1.1rem;margin-top:2px;}

        .form-label{font-weight:600;color:var(--charcoal);margin-bottom:8px;}
        .form-text{color:#6c757d;font-size:.9rem;}

        .price-highlight{
            font-size:1.8rem;font-weight:700;color:var(--primary-yellow);
        }
        .price-note{font-size:.9rem;color:#6c757d;}

        .section-title{
            font-size:1.5rem;margin-bottom:20px;padding-bottom:10px;
            border-bottom:3px solid var(--primary-yellow);display:inline-block;
        }
    </style>
</head>
<body>

<!-- HERO -->
<section class="hero">
    <div class="container position-relative" style="z-index:2;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/packages.php">Packages</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= $package ? sanitizeOutput($package['title']) : 'Select Plan' ?>
                </li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold mb-2">
            <?= $package ? sanitizeOutput($package['title']) : 'Select Your Plan' ?>
        </h1>
        <p class="lead mb-0">Get a free personalized quote in 24 hours</p>
    </div>
</section>

<main class="container my-5">
    <div class="row g-5 align-items-start">

        <!-- LEFT: FORM + PACKAGE -->
        <div class="col-lg-8">

            <!-- Alerts -->
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $error_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $success_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!$package): ?>
                <div class="alert alert-warning">
                    <strong>No package selected.</strong>
                    <a href="<?= SITE_URL ?>/packages.php" class="alert-link">Browse all packages →</a>
                </div>
            <?php else: ?>

                <!-- ENQUIRY FORM -->
                <div class="package-card">
                    <h3 class="section-title">Request Your Quote</h3>
                    <form method="post" id="enquiryForm" novalidate>
                        <?= getCsrfTokenField() ?>
                        <input type="hidden" name="selected_plan" value="<?= sanitizeOutput($package['title']) ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" required
                                       value="<?= sanitizeOutput($_POST['first_name'] ?? '') ?>" placeholder="John">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" required
                                       value="<?= sanitizeOutput($_POST['last_name'] ?? '') ?>" placeholder="Doe">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required
                                       value="<?= sanitizeOutput($_POST['email'] ?? '') ?>" placeholder="john@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" required
                                       value="<?= sanitizeOutput($_POST['phone'] ?? '') ?>" placeholder="+919876543210">
                                <div class="form-text">Include country code</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Project Type</label>
                                <select name="project_type" class="form-select">
                                    <option value="">-- Select Type --</option>
                                    <option value="Residential" <?= ($_POST['project_type'] ?? '') === 'Residential' ? 'selected' : '' ?>>Residential</option>
                                    <option value="Villa" <?= ($_POST['project_type'] ?? '') === 'Villa' ? 'selected' : '' ?>>Villa</option>
                                    <option value="Apartment" <?= ($_POST['project_type'] ?? '') === 'Apartment' ? 'selected' : '' ?>>Apartment</option>
                                    <option value="Commercial" <?= ($_POST['project_type'] ?? '') === 'Commercial' ? 'selected' : '' ?>>Commercial</option>
                                    <option value="Renovation" <?= ($_POST['project_type'] ?? '') === 'Renovation' ? 'selected' : '' ?>>Renovation</option>
                                    <option value="Interior" <?= ($_POST['project_type'] ?? '') === 'Interior' ? 'selected' : '' ?>>Interior Design</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Budget Range</label>
                                <select name="budget" class="form-select">
                                    <option value="">-- Select Budget --</option>
                                    <option value="Under 50 Lakhs" <?= ($_POST['budget'] ?? '') === 'Under 50 Lakhs' ? 'selected' : '' ?>>Under 50 Lakhs</option>
                                    <option value="50 - 75 Lakhs" <?= ($_POST['budget'] ?? '') === '50 - 75 Lakhs' ? 'selected' : '' ?>>50 - 75 Lakhs</option>
                                    <option value="75 Lakhs - 1 Cr" <?= ($_POST['budget'] ?? '') === '75 Lakhs - 1 Cr' ? 'selected' : '' ?>>75 Lakhs - 1 Cr</option>
                                    <option value="1 - 2 Cr" <?= ($_POST['budget'] ?? '') === '1 - 2 Cr' ? 'selected' : '' ?>>1 - 2 Cr</option>
                                    <option value="Above 2 Cr" <?= ($_POST['budget'] ?? '') === 'Above 2 Cr' ? 'selected' : '' ?>>Above 2 Cr</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Message (Optional)</label>
                                <textarea name="message" class="form-control" rows="4"
                                          placeholder="Plot size, timeline, special requirements, etc..."><?= sanitizeOutput($_POST['message'] ?? '') ?></textarea>
                                <div class="form-text">Tell us more about your dream project</div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-3 fw-bold" type="submit">
                                    <i class="fas fa-paper-plane me-2"></i> Submit Enquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- PACKAGE DETAILS -->
                <div class="package-card">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h3 class="mb-1"><?= sanitizeOutput($package['title']) ?></h3>
                            <p class="text-muted mb-0">Premium Construction Package</p>
                        </div>
                        <div class="text-end">
                            <?php if (!empty($package['price_per_sqft']) && (float)$package['price_per_sqft'] > 0): ?>
                                <div class="price-highlight">₹<?= number_format((float)$package['price_per_sqft']) ?></div>
                                <div class="price-note">per sq.ft</div>
                            <?php else: ?>
                                <div class="price-highlight">Custom Quote</div>
                                <div class="price-note">Tailored to your needs</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($package['description'])): ?>
                        <p class="mb-4"><?= nl2br(sanitizeOutput($package['description'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($package['features'])): ?>
                        <?php $features = array_filter(array_map('trim', explode('|', $package['features']))); ?>
                        <?php if ($features): ?>
                            <!-- <h5 class="mb-3">What's Included</h5>
                            <ul class="feature-list mb-4">
                                <?php foreach ($features as $f): ?>
                                    <li><i class="fas fa-check"></i> <?= sanitizeOutput($f) ?></li>
                                <?php endforeach; ?>
                            </ul> -->
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="d-flex gap-3 flex-wrap">
                        <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline-secondary">
                            <i class="fas fa-headset me-2"></i> Contact Sales
                        </a>
                        <a href="#" class="btn btn-primary"
                           onclick="alert('Brochure download coming soon!'); return false;">
                            <i class="fas fa-download me-2"></i> Download Brochure
                        </a>
                    </div>
                </div>

            <?php endif; ?>
        </div>

        <!-- SIDEBAR -->
        <aside class="col-lg-4">
            <div class="sticky-sidebar">

                <!-- Search -->
                <div class="sidebar-card">
                    <h5 class="section-title mb-3">Search Packages</h5>
                    <form action="<?= SITE_URL ?>/packages.php" method="get">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="e.g. Premium Villa"
                                   value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>

                <!-- Categories -->
                <div class="sidebar-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="section-title mb-0">Categories</h5>
                        <small class="text-muted"><?= $total_packages ?> total</small>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li>
                            <a class="d-flex justify-content-between align-items-center py-2 text-decoration-none"
                               href="<?= SITE_URL ?>/packages.php">
                                <span>All Packages</span>
                                <span class="badge bg-dark text-white"><?= $total_packages ?></span>
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a class="d-flex justify-content-between align-items-center py-2 text-decoration-none"
                                   href="<?= SITE_URL ?>/packages.php?category=<?= urlencode($cat['cat']) ?>">
                                    <span><?= ucfirst(sanitizeOutput($cat['cat'])) ?></span>
                                    <span class="badge bg-dark text-white"><?= (int)$cat['cnt'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Popular -->
                <div class="sidebar-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="section-title mb-0">Popular Packages</h5>
                        <a href="<?= SITE_URL ?>/packages.php" class="small text-muted">See all →</a>
                    </div>
                    <?php if (empty($popular_packages)): ?>
                        <p class="text-muted small mb-0">No packages available.</p>
                    <?php else: ?>
                        <?php foreach ($popular_packages as $p): ?>
                            <div class="popular-package">
                                <div class="popular-thumb"><i class="fas fa-box-open"></i></div>
                                <div class="flex-grow-1">
                                    <a href="<?= SITE_URL ?>/select-plan.php?plan=<?= urlencode($p['title']) ?>"
                                       class="text-decoration-none fw-semibold">
                                        <?= sanitizeOutput($p['title']) ?>
                                    </a>
                                    <div class="small text-muted">
                                        <?= !empty($p['price_per_sqft']) && (float)$p['price_per_sqft'] > 0
                                            ? '₹' . number_format((float)$p['price_per_sqft']) . '/sq.ft'
                                            : 'Custom Quote' ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </aside>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Client-side validation
    document.getElementById('enquiryForm')?.addEventListener('submit', function(e) {
        const required = ['first_name', 'last_name', 'email', 'phone'];
        for (const field of required) {
            const input = this.elements[field];
            if (!input.value.trim()) {
                e.preventDefault();
                input.focus();
                alert('Please fill in all required fields.');
                return;
            }
        }
    });
</script>
</body>
</html>