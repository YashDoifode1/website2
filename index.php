<?php
/**
 * Home Page - Grand Jyothi Construction
 * Full Package Display (with Accordions) + Estimator
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$page_title = 'Grand Jyothi Construction | Build Your Dream Home';

// Fetch all active packages
$packages = executeQuery("
    SELECT id, title, price_per_sqft, description, features 
    FROM packages 
    WHERE is_active = 1 
    ORDER BY display_order ASC, created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch package sections (accordions)
$sections = executeQuery("
    SELECT package_id, title, content
    FROM package_sections
    WHERE is_active = 1
    ORDER BY display_order ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Group sections by package_id
$package_sections = [];
foreach ($sections as $s) {
    $package_sections[$s['package_id']][] = $s;
}

// Fetch testimonials
$testimonials = executeQuery("
    SELECT t.*, p.title AS project_title 
    FROM testimonials t
    LEFT JOIN projects p ON t.project_id = p.id
    ORDER BY t.created_at DESC LIMIT 6
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>

    <!-- Bootstrap + Fonts + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-yellow: #F9A826;
            --charcoal: #1A1A1A;
            --light-gray: #f8f9fa;
            --border-color: #e5e5e5;
        }
        body {
            font-family: 'Roboto', sans-serif;
            color: var(--charcoal);
            background-color: #fff;
        }
        h1, h2, h3, h4 { font-family: 'Poppins', sans-serif; font-weight: 600; }

        .btn-primary {
            background-color: var(--primary-yellow);
            border: none;
            color: var(--charcoal);
            font-weight: 600;
            border-radius: 8px;
            padding: 12px 30px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #e89a1f;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(249, 168, 38, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-yellow);
            color: var(--primary-yellow);
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 25px;
            transition: 0.3s;
        }
        .btn-outline-primary:hover {
            background-color: var(--primary-yellow);
            color: var(--charcoal);
        }

        /* Hero */
        .hero-section {
            background: linear-gradient(rgba(26,26,26,0.7), rgba(26,26,26,0.7)),
                        url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1770&q=80') center/cover;
            color: #fff;
            text-align: center;
            padding: 150px 0;
        }

        /* Estimator */
        .estimator-section { background: var(--light-gray); padding: 80px 0; }
        .estimator-box {
            background: #fff;
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }

        /* Packages */
        .packages-section { padding: 80px 0; }
        .package-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .package-card:hover { transform: translateY(-8px); }

        .package-header {
            background: var(--charcoal);
            color: #fff;
            padding: 25px 20px;
            text-align: center;
        }
        .package-body { padding: 30px; }
        .package-price {
            font-size: 2rem;
            color: var(--primary-yellow);
            font-weight: 700;
            margin-bottom: 10px;
        }
        .accordion-button:not(.collapsed) {
            background: var(--primary-yellow);
            color: var(--charcoal);
        }
        .accordion-button {
            font-weight: 600;
        }
    </style>
</head>
<body>

    <!-- Hero -->
    <section class="hero-section">
        <div class="container">
            <h1>Build Your Dream Home with Confidence</h1>
            <p>Modern designs. Transparent pricing. Trusted craftsmanship.</p>
            <a href="#packages" class="btn btn-primary me-3">View Packages</a>
            <a href="#estimator" class="btn btn-outline-primary">Get Estimate</a>
        </div>
    </section>

    <!-- Estimator -->
    <section id="estimator" class="estimator-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="estimator-box">
                        <h3 class="text-center mb-4">Quick Cost Estimator</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Square Footage</label>
                                <input type="number" id="squareFootage" class="form-control" placeholder="e.g. 1500" min="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Package Type</label>
                                <select id="packageType" class="form-select">
                                    <option value="">Select Package</option>
                                    <?php foreach ($packages as $pkg): ?>
                                        <option value="<?= (float)$pkg['price_per_sqft'] ?>" 
                                                data-name="<?= sanitizeOutput($pkg['title']) ?>">
                                            <?= sanitizeOutput($pkg['title']) ?> 
                                            (₹<?= number_format((float)$pkg['price_per_sqft']) ?>/sq.ft)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 mt-3" onclick="calculateEstimate()">Calculate</button>
                        <div class="text-center mt-4" id="estimateResult" style="display:none;">
                            <h5>Estimated Cost:</h5>
                            <h3 id="estimateAmount" class="fw-bold">₹0</h3>
                            <small id="packageName" class="text-muted d-block"></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Packages -->
    <section id="packages" class="packages-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Our Construction Packages</h2>
                <p class="lead">Comprehensive inclusions with full transparency</p>
            </div>
            <div class="row g-4">
                <?php foreach ($packages as $pkg): ?>
                    <div class="col-md-6">
                        <div class="package-card">
                            <div class="package-header">
                                <h3><?= sanitizeOutput($pkg['title']) ?></h3>
                            </div>
                            <div class="package-body">
                                <div class="package-price">₹<?= number_format((float)$pkg['price_per_sqft']) ?>/sq.ft</div>
                                <p><?= sanitizeOutput($pkg['description']) ?></p>

                                <!-- Accordion -->
                                <?php if (!empty($package_sections[$pkg['id']])): ?>
                                    <div class="accordion" id="accordion<?= $pkg['id'] ?>">
                                        <?php foreach ($package_sections[$pkg['id']] as $index => $sec): 
                                            $collapseId = "collapse{$pkg['id']}_{$index}";
                                        ?>
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="heading<?= $collapseId ?>">
                                                    <button class="accordion-button <?= $index !== 0 ? 'collapsed' : '' ?>" 
                                                            type="button" data-bs-toggle="collapse" 
                                                            data-bs-target="#<?= $collapseId ?>" 
                                                            aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" 
                                                            aria-controls="<?= $collapseId ?>">
                                                        <?= sanitizeOutput($sec['title']) ?>
                                                    </button>
                                                </h2>
                                                <div id="<?= $collapseId ?>" 
                                                     class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" 
                                                     aria-labelledby="heading<?= $collapseId ?>" 
                                                     data-bs-parent="#accordion<?= $pkg['id'] ?>">
                                                    <div class="accordion-body">
                                                        <?= $sec['content'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <a href="packages.php?id=<?= (int)$pkg['id'] ?>" class="btn btn-outline-primary w-100 mt-3">
                                    View Full Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-5">
                <a href="packages.php" class="btn btn-primary">View All Packages</a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5" style="background: var(--light-gray);">
        <div class="container">
            <div class="text-center mb-5">
                <h2>What Our Clients Say</h2>
                <p class="lead">Trusted by hundreds of happy homeowners</p>
            </div>
            <div class="row g-4">
                <?php foreach ($testimonials as $t): ?>
                    <div class="col-md-4">
                        <div class="p-4 bg-white rounded shadow-sm h-100">
                            <p class="fst-italic">"<?= sanitizeOutput($t['text']) ?>"</p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="https://randomuser.me/api/portraits/men/<?= rand(1,99) ?>.jpg" 
                                     alt="<?= sanitizeOutput($t['client_name']) ?>" 
                                     class="rounded-circle me-3" width="50">
                                <div>
                                    <h6 class="mb-0"><?= sanitizeOutput($t['client_name']) ?></h6>
                                    <?php if ($t['project_title']): ?>
                                        <small class="text-muted"><?= sanitizeOutput($t['project_title']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateEstimate() {
            const sqft = parseFloat(document.getElementById('squareFootage').value);
            const select = document.getElementById('packageType');
            const rate = parseFloat(select.value);
            const pkgName = select.options[select.selectedIndex]?.dataset.name || '';

            if (!sqft || sqft < 100 || !rate) {
                alert('Please enter at least 100 sq.ft and select a package.');
                return;
            }

            const estimate = sqft * rate;
            document.getElementById('estimateAmount').textContent = '₹' + estimate.toLocaleString('en-IN');
            document.getElementById('packageName').textContent = pkgName ? '(' + pkgName + ')' : '';
            document.getElementById('estimateResult').style.display = 'block';
        }
    </script>
</body>
</html>
