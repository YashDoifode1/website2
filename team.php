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
<header class="hero">
    <div class="container">
        <h1>Meet Our Team</h1>
        <p>The experts behind our success</p>
    </div>
</header>

<main class="container">
    <section>
        <p style="text-align: center; font-size: 1.1rem; margin-bottom: 3rem;">
            Our team of experienced professionals is dedicated to delivering exceptional results. 
            With diverse expertise in architecture, engineering, and project management, we work 
            together to bring your vision to life.
        </p>
        
        <?php if (empty($team_members)): ?>
            <article>
                <p>Team information will be available soon.</p>
            </article>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($team_members as $member): ?>
                    <article class="card" style="text-align: center;">
                        <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($member['photo']) ?>" 
                             alt="<?= sanitizeOutput($member['name']) ?>" 
                             class="team-photo"
                             onerror="this.src='https://via.placeholder.com/150?text=<?= urlencode($member['name']) ?>'">
                        
                        <h3><?= sanitizeOutput($member['name']) ?></h3>
                        <p><strong><?= sanitizeOutput($member['role']) ?></strong></p>
                        
                        <?php if ($member['bio']): ?>
                            <p style="text-align: left;"><?= sanitizeOutput($member['bio']) ?></p>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Team Values -->
    <section>
        <h2>What Makes Our Team Special</h2>
        <div class="grid">
            <article class="card">
                <i data-feather="award" class="card-icon"></i>
                <h4>Expertise & Experience</h4>
                <p>
                    Our team brings decades of combined experience in construction, architecture, 
                    and project management. Each member is a specialist in their field.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="users" class="card-icon"></i>
                <h4>Collaborative Approach</h4>
                <p>
                    We believe in teamwork and collaboration. Our integrated approach ensures 
                    seamless coordination across all project phases.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="heart" class="card-icon"></i>
                <h4>Client-Focused</h4>
                <p>
                    Your satisfaction is our priority. We listen to your needs, provide expert 
                    guidance, and work tirelessly to exceed your expectations.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="trending-up" class="card-icon"></i>
                <h4>Continuous Learning</h4>
                <p>
                    We stay updated with the latest construction technologies, materials, and 
                    best practices to deliver innovative solutions.
                </p>
            </article>
        </div>
    </section>

    <!-- Join Our Team -->
    <section style="background-color: var(--card-background-color); padding: 2rem; border-radius: 8px; text-align: center;">
        <h2>Join Our Team</h2>
        <p>
            We're always looking for talented individuals who share our passion for excellence. 
            If you're interested in joining Grand Jyothi Construction, we'd love to hear from you.
        </p>
        <a href="/constructioninnagpur/contact.php" role="button">Get in Touch</a>
    </section>

    <!-- Call to Action -->
    <section style="text-align: center; padding: 3rem 0;">
        <h2>Let's Work Together</h2>
        <p>Ready to start your construction project? Our team is here to help.</p>
        <a href="/constructioninnagpur/contact.php" role="button">Contact Us</a>
        <a href="/constructioninnagpur/projects.php" role="button" class="secondary">View Our Work</a>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
