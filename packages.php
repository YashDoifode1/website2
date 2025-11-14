<?php
/**
 * Packages Page - Modern Design (Updated with package_sections)
 * 
 * Dynamic construction packages with section accordions, comparison table,
 * and responsive layout.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Construction Packages | BuildDream Construction';

// Fetch all active packages
$sql = "SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC";
$stmt = executeQuery($sql);
$packages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all active sections
$sqlSections = "SELECT * FROM package_sections WHERE is_active = 1 ORDER BY package_id, display_order";
$stmtSections = executeQuery($sqlSections);
$sectionsRaw = $stmtSections->fetchAll(PDO::FETCH_ASSOC);

// Group sections by package_id
$packageSections = [];
foreach ($sectionsRaw as $section) {
    $packageSections[$section['package_id']][] = $section;
}

// Get all unique features for comparison (same as before)
$allFeatures = [];
foreach ($packages as $package) {
    $features = explode('|', $package['features']);
    $allFeatures = array_merge($allFeatures, array_map('trim', $features));
}
$uniqueFeatures = array_unique($allFeatures);

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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-yellow: #F9A826;
            --charcoal: #1A1A1A;
            --white: #FFFFFF;
            --light-gray: #f8f9fa;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--charcoal);
            background-color: var(--light-gray);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary-yellow);
            border-color: var(--primary-yellow);
            color: var(--charcoal);
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #e89a1f;
            border-color: #e89a1f;
            color: var(--charcoal);
        }

        .btn-outline-primary {
            border-color: var(--primary-yellow);
            color: var(--primary-yellow);
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-yellow);
            border-color: var(--primary-yellow);
            color: var(--charcoal);
        }

        .navbar {
            background-color: var(--charcoal);
            padding: 15px 0;
        }

        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-yellow) !important;
        }

        .nav-link {
            color: var(--white) !important;
            font-weight: 500;
            margin: 0 10px;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-yellow) !important;
        }

        .page-header {
            background: linear-gradient(rgba(26, 26, 26, 0.8), rgba(26, 26, 26, 0.8)),
                        url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
            background-size: cover;
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
        }

        .comparison-toggle {
            background-color: var(--white);
            border-radius: 50px;
            padding: 10px 20px;
            display: inline-flex;
            align-items: center;
            margin-top: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .comparison-toggle span {
            margin-right: 10px;
            font-weight: 500;
        }

        .package-section {
            padding: 80px 0;
        }

        .package-card {
            background-color: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            margin-bottom: 30px;
            position: relative;
        }

        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .package-header {
            background-color: var(--charcoal);
            color: var(--white);
            padding: 25px;
            text-align: center;
            position: relative;
        }

        .package-popular {
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--primary-yellow);
            color: var(--charcoal);
            padding: 5px 20px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 10;
        }

        .package-body {
            padding: 30px;
        }

        .package-price {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-yellow);
            margin-bottom: 15px;
            text-align: center;
        }

        .package-description {
            text-align: center;
            margin-bottom: 25px;
            color: #666;
            font-size: 0.95rem;
        }

        /* Accordion styling */
        .accordion-button {
            font-weight: 600;
            background-color: #fff;
            color: var(--charcoal);
        }

        .accordion-button:not(.collapsed) {
            background-color: var(--primary-yellow);
            color: var(--charcoal);
        }

        .accordion-body {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.5;
        }

        .package-footer {
            padding: 0 30px 30px;
            text-align: center;
        }

        .comparison-section {
            padding: 60px 0;
            background-color: var(--white);
            display: none;
        }

        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .comparison-table th {
            background-color: var(--charcoal);
            color: var(--white);
            padding: 15px;
            text-align: center;
            font-weight: 600;
        }

        .comparison-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }

        .comparison-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .comparison-table .feature-name {
            text-align: left;
            font-weight: 500;
            background-color: #fff !important;
            position: sticky;
            left: 0;
            z-index: 1;
        }

        .comparison-table .check-mark {
            color: var(--primary-yellow);
            font-size: 1.2rem;
        }

        .comparison-table .cross-mark {
            color: #ccc;
            font-size: 1.2rem;
        }

        .cta-section {
            background-color: var(--charcoal);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .cta-section h2 {
            margin-bottom: 20px;
        }

        .cta-section p {
            max-width: 700px;
            margin: 0 auto 30px;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 60px 0;
            }
            .page-header h1 {
                font-size: 2.2rem;
            }
            .comparison-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            .comparison-table .feature-name {
                min-width: 180px;
            }
        }
    </style>
</head>
<body>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Construction Packages</h1>
            <p class="lead">Choose the perfect package for your dream home. Transparent pricing, quality materials, and expert craftsmanship.</p>
            <div class="comparison-toggle">
                <span>Compare Packages</span>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="comparisonToggle">
                </div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="package-section">
        <div class="container">
            <?php if (empty($packages)): ?>
                <div class="text-center py-5">
                    <p class="lead text-muted">No packages available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($packages as $package): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="package-card">
                                <?php if (!empty($package['is_popular'])): ?>
                                    <div class="package-popular">MOST POPULAR</div>
                                <?php endif; ?>
                                <div class="package-header">
                                    <h3><?= sanitizeOutput($package['title']) ?></h3>
                                    <p><?= sanitizeOutput($package['subtitle'] ?? 'Quality Construction') ?></p>
                                </div>
                                <div class="package-body">
                                    <div class="package-price">
                                        <?php if ($package['price_per_sqft'] > 0): ?>
                                            ₹<?= number_format((float)$package['price_per_sqft']) ?>/sq.ft
                                        <?php else: ?>
                                            Custom Quote
                                        <?php endif; ?>
                                    </div>
                                    <p class="package-description"><?= sanitizeOutput($package['description']) ?></p>

                                    <?php if (!empty($packageSections[$package['id']])): ?>
                                        <div class="accordion" id="accordion<?= $package['id'] ?>">
                                            <?php foreach ($packageSections[$package['id']] as $index => $section): 
                                                $collapseId = 'collapse' . $package['id'] . '_' . $index;
                                                $headingId  = 'heading' . $package['id'] . '_' . $index;
                                            ?>
                                                <div class="accordion-item mb-2">
                                                    <h2 class="accordion-header" id="<?= $headingId ?>">
                                                        <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>"
                                                                aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                                                                aria-controls="<?= $collapseId ?>">
                                                            <?= sanitizeOutput($section['title']) ?>
                                                        </button>
                                                    </h2>
                                                    <div id="<?= $collapseId ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                                                         aria-labelledby="<?= $headingId ?>" data-bs-parent="#accordion<?= $package['id'] ?>">
                                                        <div class="accordion-body">
                                                            <?= $section['content'] ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center">No details available for this package.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="package-footer">
                                    <a href="/constructioninnagpur/select-plan.php?plan=<?= urlencode($package['title']) ?>" 
                                       class="btn btn-primary w-100">
                                        <?= $package['price_per_sqft'] > 0 ? 'Select Package' : 'Get Quote' ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Comparison Section -->
    <section class="comparison-section" id="comparisonSection">
        <div class="container">
            <h2 class="text-center mb-5">Package Comparison</h2>
            <div class="table-responsive">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th class="feature-name">Features</th>
                            <?php foreach ($packages as $package): ?>
                                <th><?= sanitizeOutput($package['title']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($uniqueFeatures as $feature): 
                            if (empty(trim($feature))) continue;
                        ?>
                            <tr>
                                <td class="feature-name"><?= sanitizeOutput(trim($feature)) ?></td>
                                <?php foreach ($packages as $package): 
                                    $packageFeatures = array_map('trim', explode('|', $package['features']));
                                    $hasFeature = in_array(trim($feature), $packageFeatures);
                                ?>
                                    <td>
                                        <?php if ($hasFeature): ?>
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
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Start Your Dream Project?</h2>
            <p>Contact us today for a free consultation and detailed quote tailored to your specific needs.</p>
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary me-3">Get Free Estimate</a>
            <a href="/constructioninnagpur/contact.php" class="btn btn-outline-primary">Schedule Consultation</a>
        </div>
    </section>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle comparison table
        document.getElementById('comparisonToggle').addEventListener('change', function() {
            const comparisonSection = document.getElementById('comparisonSection');
            if (this.checked) {
                comparisonSection.style.display = 'block';
                setTimeout(() => {
                    comparisonSection.scrollIntoView({ behavior: 'smooth' });
                }, 100);
            } else {
                comparisonSection.style.display = 'none';
            }
        });
    </script>
</body>
</html>
