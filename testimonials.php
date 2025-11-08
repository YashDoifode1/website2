<?php
/**
 * Testimonials Page
 * 
 * Displays client testimonials and reviews
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Testimonials';

// Fetch all testimonials with project information
$sql = "SELECT t.*, p.title as project_title, p.location as project_location 
        FROM testimonials t 
        LEFT JOIN projects p ON t.project_id = p.id 
        ORDER BY t.created_at DESC";
$stmt = executeQuery($sql);
$testimonials = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero">
    <div class="container">
        <h1>Client Testimonials</h1>
        <p>What our satisfied clients say about us</p>
    </div>
</header>

<main class="container">
    <section>
        <p style="text-align: center; font-size: 1.1rem; margin-bottom: 3rem;">
            Don't just take our word for it. Here's what our clients have to say about their 
            experience working with Grand Jyothi Construction.
        </p>
        
        <?php if (empty($testimonials)): ?>
            <article>
                <p>Testimonials will be available soon.</p>
            </article>
        <?php else: ?>
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial">
                    <blockquote>
                        <p style="font-size: 1.1rem; font-style: italic; margin-bottom: 1rem;">
                            "<?= sanitizeOutput($testimonial['text']) ?>"
                        </p>
                        <footer>
                            <strong style="font-size: 1.1rem;">
                                — <?= sanitizeOutput($testimonial['client_name']) ?>
                            </strong>
                            <?php if ($testimonial['project_title']): ?>
                                <br>
                                <small>
                                    <i data-feather="briefcase"></i> 
                                    <?= sanitizeOutput($testimonial['project_title']) ?>
                                    <?php if ($testimonial['project_location']): ?>
                                        | <?= sanitizeOutput($testimonial['project_location']) ?>
                                    <?php endif; ?>
                                </small>
                            <?php endif; ?>
                            <br>
                            <small>
                                <i data-feather="calendar"></i> 
                                <?= date('F Y', strtotime($testimonial['created_at'])) ?>
                            </small>
                        </footer>
                    </blockquote>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- Trust Indicators -->
    <section style="background-color: var(--card-background-color); padding: 2rem; border-radius: 8px;">
        <h2 style="text-align: center;">Why Clients Trust Us</h2>
        <div class="grid">
            <article class="card">
                <i data-feather="check-circle" class="card-icon"></i>
                <h4>Quality Assurance</h4>
                <p>
                    We maintain the highest quality standards in every project, ensuring durability 
                    and excellence in craftsmanship.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="clock" class="card-icon"></i>
                <h4>On-Time Delivery</h4>
                <p>
                    Our efficient project management ensures timely completion without compromising 
                    on quality or safety.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="dollar-sign" class="card-icon"></i>
                <h4>Transparent Pricing</h4>
                <p>
                    No hidden costs or surprises. We provide detailed quotations and maintain 
                    transparency throughout the project.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="headphones" class="card-icon"></i>
                <h4>Excellent Support</h4>
                <p>
                    Our relationship doesn't end at project completion. We provide ongoing support 
                    and warranty services.
                </p>
            </article>
        </div>
    </section>

    <!-- Client Statistics -->
    <section style="text-align: center; padding: 2rem 0;">
        <h2>Our Client Satisfaction Record</h2>
        <div class="grid">
            <div>
                <h3 style="color: var(--primary); font-size: 2.5rem; margin-bottom: 0;">98%</h3>
                <p><strong>Client Satisfaction Rate</strong></p>
            </div>
            <div>
                <h3 style="color: var(--primary); font-size: 2.5rem; margin-bottom: 0;">450+</h3>
                <p><strong>Happy Clients</strong></p>
            </div>
            <div>
                <h3 style="color: var(--primary); font-size: 2.5rem; margin-bottom: 0;">85%</h3>
                <p><strong>Repeat & Referral Business</strong></p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section style="text-align: center; padding: 3rem 0;">
        <h2>Become Our Next Success Story</h2>
        <p>Join hundreds of satisfied clients who trusted us with their construction projects.</p>
        <a href="/constructioninnagpur/contact.php" role="button">Start Your Project</a>
        <a href="/constructioninnagpur/projects.php" role="button" class="secondary">View Our Projects</a>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
