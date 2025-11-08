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
    <div class="hero-content">
        <h1>Our Latest Completed Projects</h1>
        <p>Showcasing excellence in residential construction across Bangalore</p>
    </div>
</header>

<main class="container section">
    <section>
        <div class="section-header">
            <h2>Recent Projects</h2>
            <p>Explore our latest completed residential projects with quality craftsmanship and timely delivery</p>
        </div>
        
        <?php if (empty($projects)): ?>
            <article>
                <p>No projects available at the moment. Please check back later.</p>
            </article>
        <?php else: ?>
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
                        
                        <p><?= sanitizeOutput($project['description']) ?></p>
                        
                        <?php if ($project['completed_on']): ?>
                            <div class="project-meta">
                                <i data-feather="calendar"></i>
                                <span>Completed: <?= date('F Y', strtotime($project['completed_on'])) ?></span>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Project Statistics -->
    <section class="card" style="background: linear-gradient(135deg, var(--primary-blue) 0%, #003a8c 100%); color: white; text-align: center;">
        <h2 style="color: white; margin-bottom: 2rem;">Our Track Record</h2>
        <div class="grid grid-4">
            <div>
                <h3 style="color: var(--primary-orange); font-size: 3rem; margin-bottom: 0.5rem;">500+</h3>
                <p style="color: white;"><strong>Projects Completed</strong></p>
            </div>
            <div>
                <h3 style="color: var(--primary-orange); font-size: 3rem; margin-bottom: 0.5rem;">18+</h3>
                <p style="color: white;"><strong>Years of Experience</strong></p>
            </div>
            <div>
                <h3 style="color: var(--primary-orange); font-size: 3rem; margin-bottom: 0.5rem;">450+</h3>
                <p style="color: white;"><strong>Happy Clients</strong></p>
            </div>
            <div>
                <h3 style="color: var(--primary-orange); font-size: 3rem; margin-bottom: 0.5rem;">100%</h3>
                <p style="color: white;"><strong>Quality Assured</strong></p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="text-center" style="padding: 3rem 0;">
        <h2>Have a Project in Mind?</h2>
        <p style="font-size: 1.125rem; color: var(--text-gray); margin-bottom: 2rem;">Let's discuss how we can help bring your vision to life.</p>
        <div class="hero-buttons">
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Start Your Project</a>
            <a href="/constructioninnagpur/packages.php" class="btn btn-secondary">View Packages</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
