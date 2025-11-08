<?php
/**
 * Packages Page
 * 
 * Displays all construction packages with pricing and features
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Our Packages';

// Fetch all active packages
$sql = "SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC, price_per_sqft ASC";
$stmt = executeQuery($sql);
$packages = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero">
    <div class="hero-content">
        <h1>Our Packages</h1>
        <p>Explore Diverse Home Packages Tailored to Your Needs and Desires</p>
    </div>
</header>

<main class="container section">
    <section>
        <div class="section-header">
            <h2>Choose Your Perfect Package</h2>
            <p>From budget-friendly to ultra-luxury, we have a package that fits your vision and budget</p>
        </div>
        
        <?php if (empty($packages)): ?>
            <div class="card">
                <p class="text-center">No packages available at the moment. Please contact us for custom quotes.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-2">
                <?php foreach ($packages as $package): 
                    $features = explode('|', $package['features']);
                ?>
                    <article class="card" style="position: relative;">
                        <?php if ($package['title'] === 'Platinum Plan'): ?>
                            <div style="position: absolute; top: -10px; right: 20px; background: var(--primary-orange); color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                                Most Popular
                            </div>
                        <?php endif; ?>
                        
                        <div style="text-align: center; padding: 1.5rem 0; border-bottom: 2px solid var(--border-color); margin-bottom: 1.5rem;">
                            <h3 style="color: var(--primary-blue); font-size: 1.75rem; margin-bottom: 0.5rem;">
                                <?= sanitizeOutput($package['title']) ?>
                            </h3>
                            <div style="font-size: 2.5rem; font-weight: 700; color: var(--text-dark); margin: 1rem 0;">
                                ₹<?= number_format((float)$package['price_per_sqft'], 0) ?>
                                <span style="font-size: 1rem; font-weight: 400; color: var(--text-gray);">/sqft</span>
                            </div>
                            <?php if ($package['description']): ?>
                                <p style="color: var(--text-gray); font-size: 0.95rem;">
                                    <?= sanitizeOutput($package['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <h4 style="font-size: 1.125rem; margin-bottom: 1rem; color: var(--text-dark);">
                                <i data-feather="check-circle" style="width: 20px; height: 20px; color: var(--primary-blue);"></i>
                                Package Includes:
                            </h4>
                            <ul style="list-style: none; padding: 0;">
                                <?php foreach ($features as $feature): ?>
                                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--bg-gray); display: flex; align-items: flex-start; gap: 0.75rem;">
                                        <i data-feather="check" style="width: 18px; height: 18px; color: var(--primary-blue); flex-shrink: 0; margin-top: 2px;"></i>
                                        <span style="color: var(--text-gray);"><?= sanitizeOutput(trim($feature)) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <?php if ($package['notes']): ?>
                            <div style="background: var(--bg-light); padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem;">
                                <p style="font-size: 0.875rem; color: var(--text-gray); margin: 0;">
                                    <i data-feather="info" style="width: 16px; height: 16px;"></i>
                                    <?= sanitizeOutput($package['notes']) ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <a href="/constructioninnagpur/select-plan.php?plan=<?= urlencode($package['title']) ?>" 
                           class="btn btn-primary" 
                           style="width: 100%; text-align: center; display: block;">
                            <i data-feather="arrow-right"></i> Select This Plan
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Why Choose Our Packages -->
    <section class="card" style="background: var(--bg-light); margin-top: 3rem;">
        <div class="section-header">
            <h2>Why Choose Our Packages?</h2>
            <p>Transparent pricing, quality materials, and expert craftsmanship</p>
        </div>
        
        <div class="grid grid-3">
            <div style="text-align: center; padding: 1.5rem;">
                <i data-feather="shield" style="width: 48px; height: 48px; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Quality Assured</h3>
                <p style="color: var(--text-gray);">All packages include premium materials and expert workmanship</p>
            </div>
            <div style="text-align: center; padding: 1.5rem;">
                <i data-feather="dollar-sign" style="width: 48px; height: 48px; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Transparent Pricing</h3>
                <p style="color: var(--text-gray);">No hidden costs - what you see is what you pay</p>
            </div>
            <div style="text-align: center; padding: 1.5rem;">
                <i data-feather="clock" style="width: 48px; height: 48px; color: var(--primary-blue); margin-bottom: 1rem;"></i>
                <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Timely Delivery</h3>
                <p style="color: var(--text-gray);">We complete projects on schedule without compromising quality</p>
            </div>
        </div>
    </section>

    <!-- Custom Package CTA -->
    <section class="text-center" style="padding: 3rem 0;">
        <h2>Need a Custom Package?</h2>
        <p style="font-size: 1.125rem; color: var(--text-gray); margin-bottom: 2rem;">
            Don't see what you're looking for? We can create a custom package tailored to your specific needs.
        </p>
        <div class="hero-buttons">
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Request Custom Quote</a>
            <a href="/constructioninnagpur/projects.php" class="btn btn-outline">View Our Projects</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
