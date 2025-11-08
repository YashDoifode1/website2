<?php
/**
 * Home Page
 * 
 * Displays hero banner, featured services, projects, and testimonials
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Home';

// Fetch featured services (limit 4)
$services_sql = "SELECT * FROM services ORDER BY created_at DESC LIMIT 4";
$services_stmt = executeQuery($services_sql);
$services = $services_stmt->fetchAll();

// Fetch latest projects (limit 3)
$projects_sql = "SELECT * FROM projects ORDER BY created_at DESC LIMIT 3";
$projects_stmt = executeQuery($projects_sql);
$projects = $projects_stmt->fetchAll();

// Fetch testimonials (limit 3)
$testimonials_sql = "SELECT t.*, p.title as project_title 
                     FROM testimonials t 
                     LEFT JOIN projects p ON t.project_id = p.id 
                     ORDER BY t.created_at DESC LIMIT 3";
$testimonials_stmt = executeQuery($testimonials_sql);
$testimonials = $testimonials_stmt->fetchAll();

// Fetch featured packages (limit 3)
$packages_sql = "SELECT * FROM packages WHERE is_active = 1 ORDER BY display_order ASC LIMIT 3";
$packages_stmt = executeQuery($packages_sql);
$packages = $packages_stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero">
    <div class="hero-content">
        <h1>Welcome to Grand Jyothi Construction</h1>
        <p>Building your vision with excellence and trust.</p>
        <div class="hero-buttons">
            <a href="/constructioninnagpur/projects.php" class="btn btn-primary">View Our Projects</a>
            <a href="/constructioninnagpur/contact.php" class="btn btn-secondary">Get in Touch</a>
        </div>
    </div>
</header>

<!-- About Section -->
<main class="container section">
    <section>
        <hgroup>
            <h2>About Grand Jyothi Construction</h2>
            <p>Your trusted partner in construction excellence</p>
        </hgroup>
        <p>
            With over 18 years of experience in the construction industry, Grand Jyothi Construction has established 
            itself as a leading name in Nagpur. We specialize in residential, commercial, and industrial projects, 
            delivering quality craftsmanship and innovative solutions that exceed client expectations.
        </p>
        <p>
            Our commitment to excellence, attention to detail, and customer satisfaction has earned us the trust 
            of hundreds of satisfied clients across Maharashtra.
        </p>
        <a href="/constructioninnagpur/about.php">Learn More About Us →</a>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-choose-us">
        <div class="section-header">
            <h2>Why Choose Grand Jyothi Construction</h2>
            <p>Experience the difference of working with industry leaders</p>
        </div>
        
        <div class="grid grid-3">
            <div class="feature-box">
                <div class="feature-icon">
                    <i data-feather="award"></i>
                </div>
                <h3>18+ Years of Excellence</h3>
                <p>Nearly two decades of proven track record in delivering high-quality construction projects across Nagpur and Maharashtra.</p>
            </div>
            
            <div class="feature-box">
                <div class="feature-icon">
                    <i data-feather="shield"></i>
                </div>
                <h3>Quality Assurance</h3>
                <p>We use only premium materials and follow strict quality control measures to ensure durability and safety in every project.</p>
            </div>
            
            <div class="feature-box">
                <div class="feature-icon">
                    <i data-feather="clock"></i>
                </div>
                <h3>Timely Delivery</h3>
                <p>Our efficient project management ensures that your project is completed on time without compromising on quality.</p>
            </div>
            
            <div class="feature-box">
                <div class="feature-icon">
                    <i data-feather="users"></i>
                </div>
                <h3>Expert Team</h3>
                <p>Our team of skilled architects, engineers, and craftsmen bring expertise and innovation to every project.</p>
            </div>
            
            <div class="feature-box">
                <div class="feature-icon">
                    <i data-feather="dollar-sign"></i>
                </div>
                <h3>Competitive Pricing</h3>
                <p>We offer transparent pricing and flexible packages that provide excellent value for your investment.</p>
            </div>
            
            <div class="feature-box">
                <div class="feature-icon">
                    <i data-feather="headphones"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Our dedicated customer support team is always available to address your queries and concerns.</p>
            </div>
        </div>
    </section>

    <!-- Featured Services -->
    <section>
        <div class="section-header">
            <h2>Our Services</h2>
            <p>Comprehensive construction solutions for all your needs</p>
        </div>
        
        <div class="grid grid-4">
            <?php foreach ($services as $service): ?>
                <div class="card service-card">
                    <i data-feather="<?= sanitizeOutput($service['icon']) ?>" class="card-icon"></i>
                    <h3><?= sanitizeOutput($service['title']) ?></h3>
                    <p><?= sanitizeOutput($service['description']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <p style="text-align: center;">
            <a href="/constructioninnagpur/services.php">View All Services →</a>
        </p>
    </section>

    <!-- Featured Projects -->
    <section>
        <div class="section-header">
            <h2>Featured Projects</h2>
            <p>Showcasing our latest completed works</p>
        </div>
        
        <div class="grid grid-3">
            <?php foreach ($projects as $project): ?>
                <article class="card project-card">
                    <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($project['image']) ?>" 
                         alt="<?= sanitizeOutput($project['title']) ?>"
                         onerror="this.src='https://via.placeholder.com/400x250?text=<?= urlencode($project['title']) ?>'">
                    <h3><?= sanitizeOutput($project['title']) ?></h3>
                    <div class="project-meta">
                        <i data-feather="map-pin"></i>
                        <span><?= sanitizeOutput($project['location']) ?></span>
                    </div>
                    <p><?= sanitizeOutput(substr($project['description'], 0, 120)) ?>...</p>
                    <?php if ($project['completed_on']): ?>
                        <div class="project-meta">
                            <i data-feather="calendar"></i>
                            <span>Completed: <?= date('M Y', strtotime($project['completed_on'])) ?></span>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        
        <p style="text-align: center;">
            <a href="/constructioninnagpur/projects.php">View All Projects →</a>
        </p>
    </section>

    <!-- Packages Section -->
    <section>
        <div class="section-header">
            <h2>Our Construction Packages</h2>
            <p>Choose from our carefully designed packages to suit your budget and requirements</p>
        </div>
        
        <?php if (!empty($packages)): ?>
            <div class="grid grid-3">
                <?php foreach ($packages as $package): ?>
                    <article class="card package-card">
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
                            $displayFeatures = array_slice($features, 0, 4);
                            foreach ($displayFeatures as $feature): 
                            ?>
                                <div class="feature-item">
                                    <i data-feather="check-circle"></i>
                                    <span><?= sanitizeOutput(trim($feature)) ?></span>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($features) > 4): ?>
                                <p class="text-muted" style="font-size: 0.875rem; margin-top: 0.5rem;">
                                    +<?= count($features) - 4 ?> more features
                                </p>
                            <?php endif; ?>
                        </div>
                        <a href="/constructioninnagpur/packages.php" class="btn btn-outline" style="width: 100%; margin-top: 1rem;">
                            View Details
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <p style="text-align: center; margin-top: 2rem;">
            <a href="/constructioninnagpur/packages.php">View All Packages →</a>
        </p>
    </section>

    <!-- Testimonials -->
    <section>
        <div class="section-header">
            <h2>What Our Clients Say</h2>
            <p>Testimonials from satisfied customers</p>
        </div>
        
        <?php foreach ($testimonials as $testimonial): ?>
            <div class="testimonial">
                <blockquote>
                    <?= sanitizeOutput($testimonial['text']) ?>
                </blockquote>
                <p class="testimonial-author">— <?= sanitizeOutput($testimonial['client_name']) ?></p>
                <?php if ($testimonial['project_title']): ?>
                    <p class="testimonial-project"><?= sanitizeOutput($testimonial['project_title']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        
        <p style="text-align: center;">
            <a href="/constructioninnagpur/testimonials.php">Read More Testimonials →</a>
        </p>
    </section>

    <!-- Call to Action -->
    <section style="text-align: center; padding: 3rem 0;">
        <h2>Ready to Start Your Project?</h2>
        <p>Let's discuss how we can bring your vision to life.</p>
        <a href="/constructioninnagpur/contact.php" role="button">Contact Us Today</a>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
