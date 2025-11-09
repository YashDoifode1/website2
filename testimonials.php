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
    <section class="section">
        <div class="section-header text-center">
            <h2>Hear From Our Clients</h2>
            <p class="lead">
                Don't just take our word for it. Here's what our clients have to say about their 
                experience working with Grand Jyothi Construction.
            </p>
        </div>
        
        <?php if (empty($testimonials)): ?>
            <article class="card text-center">
                <p>Testimonials will be available soon.</p>
            </article>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($testimonials as $testimonial): ?>
                    <article class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="testimonial-icon">
                                <i data-feather="message-circle"></i>
                            </div>
                            <blockquote>
                                <p>"<?= sanitizeOutput($testimonial['text']) ?>"</p>
                            </blockquote>
                        </div>
                        <div class="testimonial-footer">
                            <div class="client-info">
                                <h4><?= sanitizeOutput($testimonial['client_name']) ?></h4>
                                <?php if ($testimonial['project_title']): ?>
                                    <p class="project-info">
                                        <i data-feather="briefcase"></i> 
                                        <?= sanitizeOutput($testimonial['project_title']) ?>
                                        <?php if ($testimonial['project_location']): ?>
                                            | <?= sanitizeOutput($testimonial['project_location']) ?>
                                        <?php endif; ?>
                                    </p>
                                <?php endif; ?>
                                <p class="testimonial-date">
                                    <i data-feather="calendar"></i> 
                                    <?= date('F Y', strtotime($testimonial['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Trust Indicators -->
    <section class="section">
        <div class="section-header text-center">
            <h2>Why Clients Trust Us</h2>
            <p class="lead">The pillars of our client relationships</p>
        </div>
        
        <div class="grid grid-2">
            <article class="card">
                <div class="card-icon">
                    <i data-feather="check-circle"></i>
                </div>
                <h4>Quality Assurance</h4>
                <p>
                    We maintain the highest quality standards in every project, ensuring durability 
                    and excellence in craftsmanship.
                </p>
            </article>
            
            <article class="card">
                <div class="card-icon">
                    <i data-feather="clock"></i>
                </div>
                <h4>On-Time Delivery</h4>
                <p>
                    Our efficient project management ensures timely completion without compromising 
                    on quality or safety.
                </p>
            </article>
            
            <article class="card">
                <div class="card-icon">
                    <i data-feather="dollar-sign"></i>
                </div>
                <h4>Transparent Pricing</h4>
                <p>
                    No hidden costs or surprises. We provide detailed quotations and maintain 
                    transparency throughout the project.
                </p>
            </article>
            
            <article class="card">
                <div class="card-icon">
                    <i data-feather="headphones"></i>
                </div>
                <h4>Excellent Support</h4>
                <p>
                    Our relationship doesn't end at project completion. We provide ongoing support 
                    and warranty services.
                </p>
            </article>
        </div>
    </section>

    <!-- Client Statistics -->
    <section class="section bg-light">
        <div class="section-header text-center">
            <h2>Our Client Satisfaction Record</h2>
            <p class="lead">Numbers that speak for themselves</p>
        </div>
        
        <div class="grid grid-3">
            <div class="stat-card text-center">
                <h3 class="stat-number">98%</h3>
                <p><strong>Client Satisfaction Rate</strong></p>
            </div>
            <div class="stat-card text-center">
                <h3 class="stat-number">450+</h3>
                <p><strong>Happy Clients</strong></p>
            </div>
            <div class="stat-card text-center">
                <h3 class="stat-number">85%</h3>
                <p><strong>Repeat & Referral Business</strong></p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section cta-section text-center">
        <h2>Become Our Next Success Story</h2>
        <p class="lead">Join hundreds of satisfied clients who trusted us with their construction projects.</p>
        <div class="cta-buttons">
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Start Your Project</a>
            <a href="/constructioninnagpur/projects.php" class="btn btn-outline">View Our Projects</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<style>/* Testimonial Cards */
.testimonial-card {
    background: var(--card-background-color);
    border-radius: 8px;
    box-shadow: var(--shadow);
    padding: 2rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.testimonial-content {
    flex-grow: 1;
    margin-bottom: 1.5rem;
}

.testimonial-icon {
    color: var(--primary);
    margin-bottom: 1rem;
    font-size: 1.5rem;
}

.testimonial-content blockquote p {
    font-style: italic;
    line-height: 1.6;
    margin: 0;
    color: var(--text-color);
}

.testimonial-footer {
    border-top: 1px solid var(--border-color);
    padding-top: 1.5rem;
}

.client-info h4 {
    margin: 0 0 0.5rem 0;
    color: var(--heading-color);
}

.project-info, .testimonial-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0.25rem 0;
    font-size: 0.9rem;
    color: var(--text-muted);
}

.project-info i, .testimonial-date i {
    width: 16px;
    height: 16px;
}

/* Statistics Cards */
.stat-card {
    padding: 2rem 1rem;
}

.stat-number {
    color: var(--primary);
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

/* Grid Layouts */
.grid-2 {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}

.grid-3 {
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
}

/* Card Icon Styles */
.card-icon {
    background: var(--primary-light);
    color: var(--primary);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
}

/* Section Styles */
.section {
    padding: 4rem 0;
}

.section-header {
    margin-bottom: 3rem;
}

.text-center {
    text-align: center;
}

.bg-light {
    background-color: var(--background-light);
}

.lead {
    font-size: 1.2rem;
    color: var(--text-muted);
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

/* CTA Section */
.cta-section {
    padding: 4rem 0;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
    flex-wrap: wrap;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .grid-2, .grid-3 {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .cta-buttons .btn {
        width: 100%;
        max-width: 300px;
    }

}

</style>