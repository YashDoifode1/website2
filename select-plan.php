<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

/**
 * select-plan.php – DEBUG VERSION
 * Shows PDO errors & logs the query + parameters.
 */

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

    // ---- CSRF ----
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid security token. Please try again.';
        logSecurityEvent('CSRF token validation failed on select-plan form', 'WARNING');
    }
    // ---- Rate-limit ----
    elseif (!checkRateLimit('select_plan_form', 5, 300)) {
        $remaining     = getRateLimitRemaining('select_plan_form', 300);
        $error_message = 'Too many submissions. Please try again in '
                       . ceil($remaining / 60) . ' minutes.';
        logSecurityEvent('Rate limit exceeded on select-plan form', 'WARNING');
    } else {

        // ----- INPUT -----
        $first_name   = sanitizeInput(trim($_POST['first_name'] ?? ''));
        $last_name    = sanitizeInput(trim($_POST['last_name'] ?? ''));
        $email        = sanitizeInput(trim($_POST['email'] ?? ''));
        $phone        = sanitizeInput(trim($_POST['phone'] ?? ''));
        $project_type = sanitizeInput(trim($_POST['project_type'] ?? ''));
        $budget       = sanitizeInput(trim($_POST['budget'] ?? ''));
        $message      = sanitizeInput(trim($_POST['message'] ?? ''));
        $plan         = sanitizeInput(trim($_POST['selected_plan'] ?? ''));

        // ----- VALIDATION -----
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
                // ---- BUILD INSERT ----
                $sql = "INSERT INTO contact_messages
                        (first_name, last_name, email, phone,
                         project_type, budget, message,
                         selected_plan, submitted_at,
                         ip_address, user_agent, created_at)
                        VALUES
                        (?, ?, ?, ?, ?, ?, ?, ?, NOW(),
                         ?, ?, NOW())";

                $params = [
                    $first_name,
                    $last_name,
                    $email,
                    $phone,
                    $project_type ?: null,
                    $budget       ?: null,
                    $message,
                    $plan,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                ];

                // ---- DEBUG LOG ----
                error_log("SELECT-PLAN INSERT → SQL:\n" . $sql);
                error_log("SELECT-PLAN INSERT → PARAMS: " . json_encode($params, JSON_UNESCAPED_UNICODE));

                // ---- EXECUTE ----
                executeQuery($sql, $params);

                $success_message = "Thank you for your interest in the <strong>"
                                 . htmlspecialchars($plan)
                                 . "</strong> plan! We'll contact you within 24 hours.";
                logSecurityEvent("Plan enquiry submitted: $plan", 'INFO');

                // reset form
                $first_name = $last_name = $email = $phone = $project_type = $budget = $message = '';
            } catch (PDOException $e) {
                // ---- SHOW EXACT ERROR (development only) ----
                $error_message = 'Error submitting your enquiry: <strong>'
                               . htmlspecialchars($e->getMessage()) . '</strong>';
                error_log('Plan Enquiry PDO Error: ' . $e->getMessage()
                          . ' | SQL: ' . $sql
                          . ' | Params: ' . json_encode($params));
            } catch (Throwable $e) {
                $error_message = 'Unexpected error: ' . htmlspecialchars($e->getMessage());
                error_log('Plan Enquiry Fatal: ' . $e->getMessage());
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
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
        :root {
            --primary-yellow: #F9A826;
            --charcoal: #1A1A1A;
            --light-gray: #f8f9fa;
            --radius: 12px;
        }
        body {
            font-family: 'Roboto', sans-serif;
            color: var(--charcoal);
            background: #fff;
        }
        h1,h2,h3,h4 { font-family:'Poppins',sans-serif; }

        .btn-primary {
            background: var(--primary-yellow);
            color: var(--charcoal);
            border: 0;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 20px;
        }
        .btn-primary:hover { filter: brightness(.98); }

        .hero {
            background: linear-gradient(rgba(26,26,26,.65), rgba(26,26,26,.65)),
                        url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?q=80&w=1600&auto=format&fit=crop') center/cover;
            color: #fff;
            padding: 70px 0;
            display: flex;
            align-items: flex-end;
        }
        .hero .breadcrumb a { color: rgba(255,255,255,.85); text-decoration:none; }

        main.container { padding-bottom: 100px; }
        .package-card {
            background: #fff;
            border-radius: var(--radius);
            padding: 28px;
            box-shadow: 0 8px 30px rgba(6,6,6,.06);
        }
        .sidebar-card {
            background: var(--light-gray);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .sticky-sidebar { position: sticky; top: 24px; }
        @media (max-width: 991px) { .sticky-sidebar { position: static; } }

        .popular-package {
            display:flex; gap:12px; align-items:center;
            padding-bottom:12px; border-bottom:1px solid rgba(0,0,0,.04);
            margin-bottom:12px;
        }
        .popular-package:last-child { border-bottom: none; margin-bottom:0; padding-bottom:0; }
        .popular-thumb {
            width:64px; height:64px; border-radius:8px; overflow:hidden; flex-shrink:0;
            background:#eee; display:flex; align-items:center; justify-content:center; color:#aaa;
        }
        .feature-list { list-style:none; padding:0; margin:0; }
        .feature-list li { display:flex; gap:10px; align-items:flex-start; padding:8px 0; color:#555; }
        .muted { color:#6c757d; }
    </style>
</head>
<body>

<!-- HERO -->
<section class="hero">
    <div class="container position-relative" style="z-index:2;">
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

<main class="container my-5">
    <div class="row g-5 align-items-start">

        <!-- LEFT COLUMN -->
        <div class="col-lg-8">

            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= $success_message ?></div>
            <?php endif; ?>

            <?php if (!$package): ?>
                <div class="alert alert-warning">
                    No package selected.
                    <a href="/constructioninnagpur/packages.php" class="alert-link">Choose a package</a> first.
                </div>
            <?php else: ?>

                <!-- ENQUIRY FORM -->
                <div class="card package-card mb-4">
                    <h3 class="mb-3">Request Quote</h3>
                    <form method="post" id="enquiryForm" novalidate>
                        <?= getCsrfTokenField() ?>
                        <input type="hidden" name="selected_plan" value="<?= sanitizeOutput($package['title']) ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control" required
                                       value="<?= sanitizeOutput($_POST['first_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control" required
                                       value="<?= sanitizeOutput($_POST['last_name'] ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required
                                       value="<?= sanitizeOutput($_POST['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="tel" name="phone" class="form-control" required
                                       value="<?= sanitizeOutput($_POST['phone'] ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Project Type</label>
                                <input type="text" name="project_type" class="form-control"
                                       value="<?= sanitizeOutput($_POST['project_type'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Budget (approx.)</label>
                                <input type="text" name="budget" class="form-control"
                                       value="<?= sanitizeOutput($_POST['budget'] ?? '') ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Message (Optional)</label>
                                <textarea name="message" class="form-control" rows="4"
                                          placeholder="Plot size, timeline, special requests..."><?= sanitizeOutput($_POST['message'] ?? '') ?></textarea>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">Submit Enquiry</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- PACKAGE SUMMARY -->
                <div class="package-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h4 class="mb-0"><?= sanitizeOutput($package['title']) ?></h4>
                        <div class="text-end">
                            <?php if (!empty($package['price_per_sqft']) && (float)$package['price_per_sqft'] > 0): ?>
                                <div class="h4 mb-0">₹<?= number_format((float)$package['price_per_sqft']) ?>/sq.ft</div>
                            <?php else: ?>
                                <div class="muted">Custom Quote</div>
                            <?php endif; ?>
                            <small class="muted">Starting price</small>
                        </div>
                    </div>

                    <?php if (!empty($package['description'])): ?>
                        <p class="mb-3"><?= nl2br(sanitizeOutput($package['description'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($package['features'])): ?>
                        <?php $features = array_filter(array_map('trim', explode('|', $package['features']))); ?>
                        <?php if ($features): ?>
                            <ul class="feature-list mb-3">
                                <?php foreach ($features as $f): ?>
                                    <li><i class="fa-solid fa-check" style="color:var(--primary-yellow);width:18px;"></i>
                                        <?= sanitizeOutput($f) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <a href="/constructioninnagpur/contact.php" class="btn btn-outline-dark">Contact Sales</a>
                        <a href="#" class="btn btn-primary"
                           onclick="alert('Brochure download coming soon!'); return false;">
                            Download Brochure
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- SIDEBAR -->
        <aside class="col-lg-4">
            <div class="sticky-sidebar">
                <!-- Search -->
                <div class="sidebar-card mb-3">
                    <form action="/constructioninnagpur/packages.php" method="get">
                        <label class="form-label sidebar-title">Search Packages</label>
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search packages..."
                                   value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>
                </div>

                <!-- Categories -->
                <div class="sidebar-card mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Categories</strong>
                        <small class="muted"><?= $total_packages ?> total</small>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li><a class="d-flex justify-content-between align-items-center py-2"
                               href="/constructioninnagpur/packages.php">
                            <span>All Packages</span>
                            <span class="badge bg-dark text-white"><?= $total_packages ?></span>
                        </a></li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a class="d-flex justify-content-between align-items-center py-2"
                                   href="/constructioninnagpur/packages.php?category=<?= urlencode($cat['cat']) ?>">
                                    <span><?= ucfirst(sanitizeOutput($cat['cat'])) ?></span>
                                    <span class="badge bg-dark text-white"><?= (int)$cat['cnt'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Popular -->
                <div class="sidebar-card mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Popular Packages</strong>
                        <a href="/constructioninnagpur/packages.php" class="muted small">See all</a>
                    </div>
                    <?php if (empty($popular_packages)): ?>
                        <div class="muted">No packages available.</div>
                    <?php else: ?>
                        <?php foreach ($popular_packages as $p): ?>
                            <div class="popular-package">
                                <div class="popular-thumb"><i class="fa-solid fa-box-open"></i></div>
                                <div>
                                    <div><a href="/constructioninnagpur/select-plan.php?plan=<?= urlencode($p['title']) ?>">
                                        <?= sanitizeOutput($p['title']) ?>
                                    </a></div>
                                    <small class="muted">
                                        <?= !empty($p['price_per_sqft']) && (float)$p['price_per_sqft'] > 0
                                            ? '₹' . number_format((float)$p['price_per_sqft']) . '/sq.ft'
                                            : 'Custom' ?>
                                    </small>
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
document.getElementById('enquiryForm')?.addEventListener('submit', function (e) {
    const req = ['first_name','last_name','email','phone'];
    for (const n of req) {
        if (!this.querySelector(`[name="${n}"]`).value.trim()) {
            e.preventDefault();
            alert('Please fill out all required fields.');
            return;
        }
    }
});
</script>
</body>
</html>