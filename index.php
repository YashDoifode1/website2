<?php
/**
 * Home Page - Grand Jyothi Construction
 * DB-Powered Packages in Estimate + Display
 * Uniform Header/Footer | Yellow + Charcoal
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

$page_title = 'Grand Jyothi Construction | Build Your Dream Home';

// Fetch active packages for estimator & display
$packages = executeQuery("
    SELECT id, title, price_per_sqft, description, features 
    FROM packages 
    WHERE is_active = 1 
    ORDER BY display_order ASC, created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

if (empty($packages)) {
    $packages = []; // Fallback
}

// Fetch other sections
$services = executeQuery("SELECT * FROM services ORDER BY created_at DESC LIMIT 6")->fetchAll();
$projects = executeQuery("SELECT * FROM projects ORDER BY created_at DESC LIMIT 6")->fetchAll();
$testimonials = executeQuery("
    SELECT t.*, p.title as project_title 
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
    
    <!-- Page Styles (Uniform) -->
    <style>
        :root {
            --primary-yellow: #F9A826;
            --charcoal: #1A1A1A;
            --white: #FFFFFF;
            --light-gray: #f8f9fa;
            --text-muted: #666;
            --border-color: #eee;
        }

        body {
            font-family: 'Roboto', sans-serif;
            color: var(--charcoal);
            background-color: var(--white);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary-yellow);
            border-color: var(--primary-yellow);
            color: var(--charcoal);
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #e89a1f;
            border-color: #e89a1f;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(249, 168, 38, 0.3);
        }

        .btn-outline-primary {
            border-color: var(--primary-yellow);
            color: var(--primary-yellow);
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-outline-primary:hover {
            background-color: var(--primary-yellow);
            color: var(--charcoal);
        }

        /* Hero */
        .hero-section {
            background: linear-gradient(rgba(26, 26, 26, 0.75), rgba(26, 26, 26, 0.75)),
                        url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') center/cover no-repeat;
            color: var(--white);
            padding: 150px 0;
            text-align: center;
        }
        .hero-section h1 { font-size: 3.5rem; margin-bottom: 1.5rem; }
        .hero-section p { font-size: 1.2rem; max-width: 700px; margin: 0 auto 2rem; }

        /* Estimator */
        .estimator-section { background-color: var(--light-gray); padding: 80px 0; }
        .estimator-box {
            background: var(--white);
            border-radius: 12px;
            padding: 35px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        }
        .form-label { font-weight: 600; color: var(--charcoal); }
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 1rem;
        }
        .result-box {
            background-color: var(--primary-yellow);
            color: var(--charcoal);
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
            font-weight: 700;
            font-size: 1.3rem;
            display: none;
            animation: fadeIn 0.5s ease;
        }

        /* Packages */
        .packages-section { padding: 80px 0; }
        .package-card {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }
        .package-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        }
        .package-header {
            background-color: var(--charcoal);
            color: var(--white);
            padding: 25px 20px;
            text-align: center;
        }
        .package-header h3 { margin: 0; font-size: 1.5rem; }
        .package-body { padding: 30px; }
        .package-price {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-yellow);
            margin-bottom: 15px;
        }
        .package-features {
            list-style: none;
            padding: 0;
            margin: 0 0 20px;
        }
        .package-features li {
            padding: 10px 0;
            border-bottom: 1px dashed #eee;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }
        .package-features li:last-child { border-bottom: none; }
        .package-features i { color: var(--primary-yellow); margin-right: 10px; width: 18px; }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-section { padding: 100px 0; }
            .hero-section h1 { font-size: 2.5rem; }
            .estimator-box { padding: 25px; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Build Your Dream Home with Confidence</h1>
            <p>With over 15 years of experience, we transform your vision into reality with quality craftsmanship and transparent pricing.</p>
            <div class="hero-buttons">
                <a href="#packages" class="btn btn-primary me-3">View Packages</a>
                <a href="#estimator" class="btn btn-outline-primary">Get Estimate</a>
            </div>
        </div>
    </section>

    <!-- Quick Cost Estimator -->
    <section id="estimator" class="estimator-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="estimator-box">
                        <h3 class="text-center mb-4">Quick Cost Estimator</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="squareFootage" class="form-label">Square Footage</label>
                                <input type="number" class="form-control" id="squareFootage" placeholder="e.g. 1500" min="100" required>
                            </div>
                            <div class="col-md-6">
                                <label for="packageType" class="form-label">Package Type</label>
                                <select class="form-select" id="packageType" required>
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
                        <button class="btn btn-primary w-100 mt-3" onclick="calculateEstimate()">
                            Calculate Estimate
                        </button>
                        <div class="result-box" id="estimateResult">
                            Estimated Cost: <span id="estimateAmount">₹0</span>
                            <small class="d-block mt-2" id="packageName"></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Packages -->
    <section id="packages" class="packages-section">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Our Construction Packages</h2>
                <p class="lead">Choose the package that best fits your needs and budget</p>
            </div>
            <div class="row g-4">
                <?php if (!empty($packages)): ?>
                    <?php foreach ($packages as $pkg): ?>
                        <div class="col-md-4">
                            <div class="package-card">
                                <div class="package-header">
                                    <h3><?= sanitizeOutput($pkg['title']) ?></h3>
                                </div>
                                <div class="package-body">
                                    <div class="package-price">
                                        ₹<?= number_format((float)$pkg['price_per_sqft']) ?>/sq.ft
                                    </div>
                                    <p class="text-muted"><?= sanitizeOutput($pkg['description']) ?></p>
                                    <ul class="package-features">
                                        <?php 
                                        $features = array_filter(array_map('trim', explode('|', $pkg['features'])));
                                        $display = array_slice($features, 0, 5);
                                        foreach ($display as $f): 
                                            if (empty($f)) continue;
                                        ?>
                                            <li><i class="fas fa-check"></i> <?= sanitizeOutput($f) ?></li>
                                        <?php endforeach; ?>
                                        <?php if (count($features) > 5): ?>
                                            <li class="text-muted">+<?= count($features) - 5 ?> more</li>
                                        <?php endif; ?>
                                    </ul>
                                    <a href="select-plan.php?plan=<?= urlencode($pkg['title']) ?>" 
                                       class="btn btn-primary w-100">Select Package</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">No packages available at the moment. Please check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-4">
                <a href="packages.php" class="btn btn-outline-primary">View All Packages</a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-choose-section py-5" style="background-color: var(--light-gray);">
        <div class="container">
            <div class="text-center mb-5">
                <h2>Why Choose Grand Jyothi</h2>
                <p class="lead">Excellence in every brick, trust in every promise</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="text-center p-4">
                        <div class="feature-icon mb-3"><i class="fas fa-award fa-3x" style="color: var(--primary-yellow);"></i></div>
                        <h4>15+ Years Experience</h4>
                        <p>Delivering quality homes since 2005.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="text-center p-4">
                        <div class="feature-icon mb-3"><i class="fas fa-rupee-sign fa-3x" style="color: var(--primary-yellow);"></i></div>
                        <h4>Transparent Pricing</h4>
                        <p>No hidden costs. Clear quotes.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="text-center p-4">
                        <div class="feature-icon mb-3"><i class="fas fa-users fa-3x" style="color: var(--primary-yellow);"></i></div>
                        <h4>Expert Team</h4>
                        <p>Architects, engineers, craftsmen.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="text-center p-4">
                        <div class="feature-icon mb-3"><i class="fas fa-calendar-check fa-3x" style="color: var(--primary-yellow);"></i></div>
                        <h4>On-Time Delivery</h4>
                        <p>We respect your timeline.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section py-5" style="background-color: var(--light-gray);">
        <div class="container">
            <div class="text-center mb-5">
                <h2>What Our Clients Say</h2>
                <p class="lead">Real stories from happy homeowners</p>
            </div>
            <div class="row g-4">
                <?php foreach ($testimonials as $t): ?>
                    <div class="col-md-4">
                        <div class="testimonial-card p-4 bg-white rounded shadow-sm h-100">
                            <p class="testimonial-text fst-italic">"<?= sanitizeOutput($t['text']) ?>"</p>
                            <div class="d-flex align-items-center">
                                <div class="client-avatar me-3">
                                    <img src="https://randomuser.me/api/portraits/men/<?= rand(1,99) ?>.jpg" 
                                         alt="<?= sanitizeOutput($t['client_name']) ?>" class="rounded-circle">
                                </div>
                                <div>
                                    <h5 class="client-name mb-0"><?= sanitizeOutput($t['client_name']) ?></h5>
                                    <?php if ($t['project_title']): ?>
                                        <small class="text-muted"><?= sanitizeOutput($t['project_title']) ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="testimonials.php" class="btn btn-outline-primary">Read More</a>
            </div>
        </div>
    </section>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function calculateEstimate() {
            const sqft = parseFloat(document.getElementById('squareFootage').value);
            const select = document.getElementById('packageType');
            const rate = parseFloat(select.value);
            const pkgName = select.options[select.selectedIndex]?.dataset.name || '';

            const resultBox = document.getElementById('estimateResult');
            const amountSpan = document.getElementById('estimateAmount');
            const nameSpan = document.getElementById('packageName');

            if (!sqft || sqft < 100 || !rate) {
                alert('Please enter at least 100 sq.ft and select a package.');
                return;
            }

            const estimate = sqft * rate;
            amountSpan.textContent = '₹' + estimate.toLocaleString('en-IN');
            nameSpan.textContent = pkgName ? `(${pkgName})` : '';
            resultBox.style.display = 'block';
            resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Reset on input
        ['squareFootage', 'packageType'].forEach(id => {
            document.getElementById(id).addEventListener('input', () => {
                document.getElementById('estimateResult').style.display = 'none';
            });
        });
    </script>
</body>
</html>