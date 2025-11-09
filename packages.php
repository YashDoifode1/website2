<?php
/**
 * Packages Page
 * 
 * Displays construction packages
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Construction Packages';

// Fetch all packages
$sql = "SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC";
$stmt = executeQuery($sql);
$packages = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Construction Packages</h1>
            <p class="lead">Tailored solutions for every need and budget</p>
        </div>
    </div>
</header>

<main class="container">
    <section class="section">
        <div class="section-header text-center">
            <h2>Our Construction Packages</h2>
            <p class="lead">Choose the perfect package for your project</p>
        </div>
        
        <?php if (empty($packages)): ?>
            <article class="card text-center">
                <p>No packages available at the moment. Please check back later.</p>
            </article>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($packages as $package): ?>
                    <article class="package-card">
                        <div class="package-header">
                            <h3><?= sanitizeOutput($package['title']) ?></h3>
                            <div class="package-price">
                                <span class="price-amount">₹<?= number_format((float)$package['price_per_sqft']) ?></span>
                                <span class="price-unit">/sq.ft</span>
                            </div>
                        </div>
                        <p class="package-description"><?= sanitizeOutput($package['description']) ?></p>
                        <div class="package-features">
                            <?php 
                            $features = explode('|', $package['features']);
                            foreach ($features as $feature): 
                            ?>
                                <div class="feature-item">
                                    <i data-feather="check"></i>
                                    <span><?= sanitizeOutput(trim($feature)) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="/constructioninnagpur/select-plan.php?plan=<?= urlencode($package['title']) ?>" class="btn btn-primary" style="width: 100%; margin-top: 1.5rem;">
                            Select Package
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    
    <!-- Package Comparison -->
    <section class="section bg-light">
        <div class="section-header text-center">
            <h2>Package Comparison</h2>
            <p class="lead">Find the best fit for your requirements</p>
        </div>
        
        <div class="package-comparison-container">
            <table class="package-comparison">
                <thead>
                    <tr>
                        <th style="position: sticky; left: 0; background-color: #fff;">Features</th>
                        <?php foreach ($packages as $package): ?>
                            <th><?= sanitizeOutput($package['title']) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Get all unique features from all packages
                    $allFeatures = [];
                    foreach ($packages as $package) {
                        $features = explode('|', $package['features']);
                        $allFeatures = array_merge($allFeatures, $features);
                    }
                    $uniqueFeatures = array_unique($allFeatures);
                    ?>
                    
                    <?php foreach ($uniqueFeatures as $feature): ?>
                        <tr>
                            <th style="position: sticky; left: 0; background-color: #fff;"><?= sanitizeOutput(trim($feature)) ?></th>
                            <?php foreach ($packages as $package): ?>
                                <?php 
                                $packageFeatures = explode('|', $package['features']);
                                $hasFeature = in_array(trim($feature), array_map('trim', $packageFeatures));
                                ?>
                                <td>
                                    <?php if ($hasFeature): ?>
                                        <i data-feather="check" class="text-success"></i>
                                    <?php else: ?>
                                        <i data-feather="x" class="text-muted"></i>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    
    <!-- Custom Package -->
    <section class="section text-center">
        <h2>Need a Custom Solution?</h2>
        <p class="lead">We can create a package tailored to your specific requirements</p>
        <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Request Custom Package</a>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
