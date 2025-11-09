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
    <div class="container">
        <div class="hero-content">
            <h1>Building Excellence Since 2005</h1>
            <p class="lead">Premium construction services in Nagpur with unmatched quality and trust</p>
            <div class="hero-cta">
                <a href="/constructioninnagpur/projects.php" class="btn btn-primary">View Our Projects</a>
                <a href="/constructioninnagpur/contact.php" class="btn btn-outline">Get a Free Quote</a>
            </div>
        </div>
    </div>
</header>

<main class="container">
    <!-- About Section -->
    <section class="section">
        <div class="grid">
            <div>
                <hgroup>
                    <h2>Your Trusted Construction Partner</h2>
                    <p class="subtitle">Building visions into reality for over 18 years</p>
                </hgroup>
                <p>
                    Grand Jyothi Construction brings expertise, innovation, and integrity to every project. 
                    From residential homes to commercial complexes, we deliver exceptional quality that stands the test of time.
                </p>
                <p>
                    Our client-focused approach ensures personalized solutions tailored to your unique requirements, 
                    while our commitment to sustainable practices builds a better future for our community.
                </p>
                <a href="/constructioninnagpur/about.php" class="btn-link">Learn More About Us →</a>
            </div>
            <div>
                <div class="featured-image">
                    <img src="https://via.placeholder.com/600x400?text=Grand+Jyothi+Construction" 
                         alt="Grand Jyothi Construction Project" 
                         style="width: 100%; border-radius: 8px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="section bg-light">
        <div class="section-header text-center">
            <h2>Why Choose Grand Jyothi</h2>
            <p class="lead">Excellence in every detail</p>
        </div>
        
        <div class="grid grid-3">
            <div class="feature-card">
                <div class="feature-icon">
                    <i data-feather="award"></i>
                </div>
                <h3>18+ Years Experience</h3>
                <p>Proven expertise in residential and commercial construction across Maharashtra.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i data-feather="check-circle"></i>
                </div>
                <h3>Quality Assurance</h3>
                <p>Rigorous quality control processes ensure superior craftsmanship and materials.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i data-feather="clock"></i>
                </div>
                <h3>Timely Completion</h3>
                <p>Efficient project management delivers projects on schedule, every time.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i data-feather="dollar-sign"></i>
                </div>
                <h3>Transparent Pricing</h3>
                <p>Honest quotes with no hidden costs - exceptional value for your investment.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i data-feather="users"></i>
                </div>
                <h3>Expert Team</h3>
                <p>Certified professionals dedicated to realizing your construction vision.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i data-feather="headphones"></i>
                </div>
                <h3>Dedicated Support</h3>
                <p>Comprehensive after-sales service and 10-year structural warranty.</p>
            </div>
        </div>
    </section>

    <!-- Featured Services -->
    <section class="section">
        <div class="section-header text-center">
            <h2>Our Premium Services</h2>
            <p class="lead">Comprehensive construction solutions</p>
        </div>
        
        <div class="grid grid-4">
            <?php foreach ($services as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <i data-feather="<?= sanitizeOutput($service['icon']) ?>"></i>
                    </div>
                    <h3><?= sanitizeOutput($service['title']) ?></h3>
                    <p><?= sanitizeOutput($service['description']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center">
            <a href="/constructioninnagpur/services.php" class="btn btn-outline">View All Services</a>
        </div>
    </section>

    <!-- Featured Projects -->
    <section class="section bg-light">
        <div class="section-header text-center">
            <h2>Featured Projects</h2>
            <p class="lead">Our latest completed works</p>
        </div>
        
        <div class="grid grid-3">
            <?php foreach ($projects as $project): ?>
                <article class="project-card">
                    <div class="project-image">
                        <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($project['image']) ?>" 
                             alt="<?= sanitizeOutput($project['title']) ?>"
                             onerror="this.src='https://via.placeholder.com/600x400?text=<?= urlencode($project['title']) ?>'">
                    </div>
                    <div class="project-content">
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
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center">
            <a href="/constructioninnagpur/projects.php" class="btn btn-outline">View All Projects</a>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="section">
        <div class="section-header text-center">
            <h2>Construction Packages</h2>
            <p class="lead">Tailored solutions for every need</p>
        </div>
        
        <?php if (!empty($packages)): ?>
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
                            $displayFeatures = array_slice($features, 0, 4);
                            foreach ($displayFeatures as $feature): 
                            ?>
                                <div class="feature-item">
                                    <i data-feather="check"></i>
                                    <span><?= sanitizeOutput(trim($feature)) ?></span>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($features) > 4): ?>
                                <p class="text-muted">
                                    +<?= count($features) - 4 ?> additional features
                                </p>
                            <?php endif; ?>
                        </div>
                        <a href="/constructioninnagpur/packages.php" class="btn btn-outline" style="width: 100%; margin-top: 1rem;">
                            Package Details
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center">
            <a href="/constructioninnagpur/packages.php" class="btn btn-primary">View All Packages</a>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section bg-light">
        <div class="section-header text-center">
            <h2>Client Testimonials</h2>
            <p class="lead">Hear from our satisfied customers</p>
        </div>
        
        <div class="testimonials-grid">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <i data-feather="quote" class="quote-icon"></i>
                        <blockquote>
                            <?= sanitizeOutput($testimonial['text']) ?>
                        </blockquote>
                    </div>
                    <div class="testimonial-author">
                        <p class="client-name"><?= sanitizeOutput($testimonial['client_name']) ?></p>
                        <?php if ($testimonial['project_title']): ?>
                            <p class="project-name"><?= sanitizeOutput($testimonial['project_title']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center">
            <a href="/constructioninnagpur/testimonials.php" class="btn btn-outline">Read More Testimonials</a>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="section cta-section text-center">
        <h2>Ready to Start Your Construction Project?</h2>
        <p class="lead">Contact us today for a free consultation and quote</p>
        <div class="cta-buttons">
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Get in Touch</a>
            <a href="tel:+919876543210" class="btn btn-outline">Call Now: +91 98765 43210</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<style>.btn-outline {
    background: #2b1ef7;
    border: 2px solid var(--bg-light);
    color: white;
    padding: 0.75rem 1.5rem;
    /* border-radius: var(--radius-md); */
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    text-align: center;
    /* transition: all 0.3s 
ease;</style>