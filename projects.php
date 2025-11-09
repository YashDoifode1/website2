<?php
/**
 * Projects Page
 * 
 * Displays all completed projects
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Our Projects';

// Fetch all projects
$sql = "SELECT * FROM projects ORDER BY completed_on DESC, created_at DESC";
$stmt = executeQuery($sql);
$projects = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Our Portfolio of Excellence</h1>
            <p class="lead">Showcasing premium construction projects across Nagpur</p>
        </div>
    </div>
</header>

<main class="container">
    <!-- Project Gallery -->
    <section class="section">
        <div class="section-header text-center">
            <h2>Completed Projects</h2>
            <p class="lead">Explore our handcrafted residential and commercial spaces</p>
        </div>
        
        <?php if (empty($projects)): ?>
            <article class="card text-center">
                <p>No projects available at the moment. Please check back later.</p>
            </article>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($projects as $project): ?>
                    <article class="project-card">
                        <div class="project-image">
                            <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($project['image'] ?? '') ?>" 
                                 alt="<?= sanitizeOutput($project['title'] ?? '') ?>"
                                 onerror="this.src='https://via.placeholder.com/600x400?text=<?= urlencode($project['title'] ?? '') ?>'">
                            <?php if (isset($project['type']) && !empty($project['type'])): ?>
                                <div class="project-badge">
                                    <?= sanitizeOutput($project['type']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="project-content">
                            <h3><?= sanitizeOutput($project['title'] ?? '') ?></h3>
                            
                            <div class="project-meta">
                                <i data-feather="map-pin"></i>
                                <span><?= sanitizeOutput($project['location'] ?? '') ?></span>
                            </div>
                            
                            <p><?= sanitizeOutput($project['description'] ?? '') ?></p>
                            
                            <?php if (isset($project['completed_on']) && $project['completed_on']): ?>
                                <div class="project-meta">
                                    <i data-feather="calendar"></i>
                                    <span>Completed: <?= date('F Y', strtotime($project['completed_on'])) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($project['size']) || isset($project['duration'])): ?>
                                <div class="project-stats">
                                    <?php if (isset($project['size']) && !empty($project['size'])): ?>
                                        <div class="stat-item">
                                            <span class="stat-value"><?= sanitizeOutput($project['size']) ?> sq.ft</span>
                                            <span class="stat-label">Size</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($project['duration']) && !empty($project['duration'])): ?>
                                        <div class="stat-item">
                                            <span class="stat-value"><?= sanitizeOutput($project['duration']) ?> months</span>
                                            <span class="stat-label">Duration</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="text-center">
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Start Your Project</a>
        </div>
    </section>

    <!-- Project Statistics -->
    <section class="section">
        <div class="stats-card">
            <div class="grid grid-4">
                <div class="stat-item">
                    <h3>500+</h3>
                    <p>Projects Completed</p>
                </div>
                <div class="stat-item">
                    <h3>18+</h3>
                    <p>Years of Experience</p>
                </div>
                <div class="stat-item">
                    <h3>450+</h3>
                    <p>Happy Clients</p>
                </div>
                <div class="stat-item">
                    <h3>100%</h3>
                    <p>Quality Commitment</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="section bg-light">
        <div class="section-header text-center">
            <h2>Our Construction Process</h2>
            <p class="lead">Systematic excellence from concept to completion</p>
        </div>
        
        <div class="grid grid-5 text-center">
            <div class="process-step">
                <div class="step-icon">
                    <i data-feather="clipboard"></i>
                </div>
                <h4>Consultation</h4>
                <p>Understanding your vision and requirements</p>
            </div>
            
            <div class="process-step">
                <div class="step-icon">
                    <i data-feather="layout"></i>
                </div>
                <h4>Design</h4>
                <p>Creating detailed architectural plans</p>
            </div>
            
            <div class="process-step">
                <div class="step-icon">
                    <i data-feather="tool"></i>
                </div>
                <h4>Construction</h4>
                <p>Quality execution with premium materials</p>
            </div>
            
            <div class="process-step">
                <div class="step-icon">
                    <i data-feather="check-circle"></i>
                </div>
                <h4>Inspection</h4>
                <p>Rigorous quality checks at every stage</p>
            </div>
            
            <div class="process-step">
                <div class="step-icon">
                    <i data-feather="home"></i>
                </div>
                <h4>Handover</h4>
                <p>Timely delivery with complete documentation</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section text-center">
        <h2>Ready to Build Your Vision?</h2>
        <p class="lead">Contact us for a personalized consultation and quote</p>
        <div class="cta-buttons">
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Get in Touch</a>
            <a href="tel:+919876543210" class="btn btn-outline">Call Now: +91 98765 43210</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
