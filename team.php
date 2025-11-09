<?php
/**
 * Team Page
 * 
 * Displays team members with their roles and bios
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Our Team';

// Fetch all team members
$sql = "SELECT * FROM team ORDER BY created_at ASC";
$stmt = executeQuery($sql);
$team_members = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero hero-about">
    <div class="container">
        <h1>Meet Our Team</h1>
        <p>The experts behind our success</p>
    </div>
</header>

<main class="container">
    <!-- Team Introduction -->
    <section class="section">
        <div class="section-header text-center">
            <h2>Our Expert Team</h2>
            <p class="lead">
                Our team of experienced professionals is dedicated to delivering exceptional results. 
                With diverse expertise in architecture, engineering, and project management, we work 
                together to bring your vision to life.
            </p>
        </div>
        
        <?php if (empty($team_members)): ?>
            <article class="card text-center">
                <p>Team information will be available soon.</p>
            </article>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($team_members as $member): ?>
                    <article class="team-card">
                        <div class="team-card-image">
                            <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($member['photo']) ?>" 
                                 alt="<?= sanitizeOutput($member['name']) ?>" 
                                 class="team-member-photo"
                                 onerror="this.src='https://via.placeholder.com/300x300?text=<?= urlencode($member['name']) ?>'">
                            <div class="team-card-overlay">
                                <div class="social-links">
                                    <?php if ($member['linkedin']): ?>
                                        <a href="<?= sanitizeOutput($member['linkedin']) ?>" class="social-link">
                                            <i data-feather="linkedin"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($member['email']): ?>
                                        <a href="mailto:<?= sanitizeOutput($member['email']) ?>" class="social-link">
                                            <i data-feather="mail"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="team-card-content">
                            <h3><?= sanitizeOutput($member['name']) ?></h3>
                            <p class="team-role"><?= sanitizeOutput($member['role']) ?></p>
                            
                            <?php if ($member['bio']): ?>
                                <p class="team-bio"><?= sanitizeOutput($member['bio']) ?></p>
                            <?php endif; ?>
                            
                            <?php if ($member['expertise']): ?>
                                <div class="expertise-tags">
                                    <?php 
                                    $expertise_list = explode(',', $member['expertise']);
                                    foreach ($expertise_list as $expertise): 
                                    ?>
                                        <span class="expertise-tag"><?= trim($expertise) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Team Values -->
    <section class="section bg-light">
        <div class="section-header text-center">
            <h2>What Makes Our Team Special</h2>
            <p class="lead">The qualities that set our team apart</p>
        </div>
        
        <div class="grid grid-2">
            <article class="value-card">
                <div class="value-icon">
                    <i data-feather="award"></i>
                </div>
                <h4>Expertise & Experience</h4>
                <p>
                    Our team brings decades of combined experience in construction, architecture, 
                    and project management. Each member is a specialist in their field.
                </p>
            </article>
            
            <article class="value-card">
                <div class="value-icon">
                    <i data-feather="users"></i>
                </div>
                <h4>Collaborative Approach</h4>
                <p>
                    We believe in teamwork and collaboration. Our integrated approach ensures 
                    seamless coordination across all project phases.
                </p>
            </article>
            
            <article class="value-card">
                <div class="value-icon">
                    <i data-feather="heart"></i>
                </div>
                <h4>Client-Focused</h4>
                <p>
                    Your satisfaction is our priority. We listen to your needs, provide expert 
                    guidance, and work tirelessly to exceed your expectations.
                </p>
            </article>
            
            <article class="value-card">
                <div class="value-icon">
                    <i data-feather="trending-up"></i>
                </div>
                <h4>Continuous Learning</h4>
                <p>
                    We stay updated with the latest construction technologies, materials, and 
                    best practices to deliver innovative solutions.
                </p>
            </article>
        </div>
    </section>

    <!-- Team Statistics -->
    <section class="section">
        <div class="section-header text-center">
            <h2>Our Team By The Numbers</h2>
            <p class="lead">The strength behind our operations</p>
        </div>
        
        <div class="grid grid-4">
            <div class="stat-card text-center">
                <h3 class="stat-number">50+</h3>
                <p><strong>Team Members</strong></p>
            </div>
            <div class="stat-card text-center">
                <h3 class="stat-number">15+</h3>
                <p><strong>Years Average Experience</strong></p>
            </div>
            <div class="stat-card text-center">
                <h3 class="stat-number">25+</h3>
                <p><strong>Professional Certifications</strong></p>
            </div>
            <div class="stat-card text-center">
                <h3 class="stat-number">100%</h3>
                <p><strong>Quality Commitment</strong></p>
            </div>
        </div>
    </section>

    <!-- Join Our Team -->
    <section class="section cta-section text-center">
        <h2>Join Our Team</h2>
        <p class="lead">
            We're always looking for talented individuals who share our passion for excellence. 
            If you're interested in joining Grand Jyothi Construction, we'd love to hear from you.
        </p>
        <div class="cta-buttons">
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Get in Touch</a>
            <a href="/constructioninnagpur/careers.php" class="btn btn-outline">View Open Positions</a>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section text-center">
        <h2>Let's Work Together</h2>
        <p class="lead">Ready to start your construction project? Our team is here to help.</p>
        <div class="cta-buttons">
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Contact Us</a>
            <a href="/constructioninnagpur/projects.php" class="btn btn-outline">View Our Work</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<style>.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url(data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><path d="M0 0L100 100M100 0L0 100" stroke="rgba(255,255,255,0.05)" stroke-width="2"/></svg>);
    opacity: 0.3;
}</style>