<?php
/**
 * Testimonials Page - Grand Jyothi Construction
 * BuildDream Theme: Modern, Professional, Yellow + Charcoal
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Client Testimonials | Grand Jyothi Construction';

// Fetch testimonials with project info
$sql = "SELECT t.*, p.title AS project_title, p.location AS project_location 
        FROM testimonials t 
        LEFT JOIN projects p ON t.project_id = p.id 
        ORDER BY t.created_at DESC";
$stmt = executeQuery($sql);
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>Client Testimonials</h1>
        <p>What our satisfied clients say about working with us</p>
    </div>
</section>

<main>
    <!-- Testimonials Grid -->
    <section class="section-padding">
        <div class="container">
            <h2 class="section-title">Hear From Our Clients</h2>
            <p class="text-center mb-5 lead text-muted">
                Don't just take our word for it. Here's what our clients have to say about their 
                experience with Grand Jyothi Construction.
            </p>

            <?php if (empty($testimonials)): ?>
                <div class="text-center py-5">
                    <p class="text-muted">Testimonials will be available soon.</p>
                </div>
            <?php else: ?>
                <div class="testimonials-grid">
                    <?php foreach ($testimonials as $t): ?>
                        <div class="testimonial-card">
                            <div class="testimonial-content">
                                <div class="testimonial-icon">
                                    <i class="fas fa-quote-left"></i>
                                </div>
                                <blockquote>
                                    <p>"<?= sanitizeOutput($t['text']) ?>"</p>
                                </blockquote>
                            </div>
                            <div class="testimonial-footer">
                                <div class="client-info">
                                    <h4 class="client-name"><?= sanitizeOutput($t['client_name']) ?></h4>
                                    
                                    <?php if ($t['project_title']): ?>
                                        <p class="project-info">
                                            <i class="fas fa-briefcase"></i>
                                            <?= sanitizeOutput($t['project_title']) ?>
                                            <?php if ($t['project_location']): ?>
                                                <span class="text-muted">| <?= sanitizeOutput($t['project_location']) ?></span>
                                            <?php endif; ?>
                                        </p>
                                    <?php endif; ?>

                                    <p class="testimonial-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?= date('F Y', strtotime($t['created_at'])) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Trust Indicators -->
    <section class="section-padding bg-light">
        <div class="container">
            <h2 class="section-title">Why Clients Trust Us</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="trust-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="trust-icon text-primary mb-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Quality Assurance</h4>
                        <p class="small">We maintain the highest standards in every project, ensuring durability and excellence in craftsmanship.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="trust-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="trust-icon text-primary mb-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">On-Time Delivery</h4>
                        <p class="small">Efficient project management ensures timely completion without compromising quality or safety.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="trust-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="trust-icon text-primary mb-3">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Transparent Pricing</h4>
                        <p class="small">No hidden costs. We provide detailed quotes and maintain transparency throughout.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="trust-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="trust-icon text-primary mb-3">
                            <i class="fas fa-headset fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Excellent Support</h4>
                        <p class="small">Our relationship continues post-completion with ongoing support and warranty.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Client Statistics -->
    <section class="section-padding">
        <div class="container">
            <h2 class="section-title">Our Client Satisfaction Record</h2>
            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Client Satisfaction Rate</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">450+</div>
                    <div class="stat-label">Happy Clients</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">85%</div>
                    <div class="stat-label">Repeat & Referral Business</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <!-- <section class="cta-section">
        <div class="container">
            <h2>Become Our Next Success Story</h2>
            <p>Join hundreds of satisfied clients who trusted us with their construction dreams.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Start Your Project</a>
                <a href="/constructioninnagpur/projects.php" class="btn btn-outline-light">View Our Projects</a>
            </div>
        </div>
    </section> -->
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- BuildDream Theme Styles -->
<style>
    :root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --white: #FFFFFF;
        --light-gray: #f8f9fa;
        --medium-gray: #e9ecef;
    }

    body {
        font-family: 'Roboto', sans-serif;
        color: var(--charcoal);
        background-color: var(--white);
        line-height: 1.6;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
    }

    .btn-primary {
        background-color: var(--primary-yellow);
        border-color: var(--primary-yellow);
        color: var(--charcoal);
        font-weight: 600;
        padding: 10px 25px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #e89a1f;
        border-color: #e89a1f;
        color: var(--charcoal);
    }

    .btn-outline-light {
        border-color: var(--white);
        color: var(--white);
    }

    .btn-outline-light:hover {
        background-color: var(--white);
        color: var(--charcoal);
    }

    .hero-section {
        background: linear-gradient(rgba(26, 26, 26, 0.7), rgba(26, 26, 26, 0.7)),
                    url('https://images.unsplash.com/photo-1581093450021-4a7360e9a6b5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
        background-size: cover;
        color: var(--white);
        padding: 120px 0;
        text-align: center;
    }

    .hero-section h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
    }

    .hero-section p {
        font-size: 1.2rem;
        max-width: 700px;
        margin: 0 auto;
    }

    .section-padding {
        padding: 80px 0;
    }

    .section-title {
        font-size: 2.2rem;
        margin-bottom: 50px;
        text-align: center;
        position: relative;
    }

    .section-title:after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background-color: var(--primary-yellow);
    }

    /* Testimonials Grid */
    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .testimonial-card {
        background-color: var(--white);
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }

    .testimonial-content {
        flex-grow: 1;
        margin-bottom: 20px;
    }

    .testimonial-icon {
        color: var(--primary-yellow);
        font-size: 2rem;
        margin-bottom: 15px;
    }

    .testimonial-content blockquote p {
        font-style: italic;
        line-height: 1.7;
        margin: 0;
        color: #555;
        font-size: 1.05rem;
    }

    .testimonial-footer {
        border-top: 1px solid #eee;
        padding-top: 20px;
    }

    .client-name {
        margin: 0 0 8px 0;
        font-size: 1.1rem;
        color: var(--charcoal);
    }

    .project-info, .testimonial-date {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 4px 0;
        font-size: 0.9rem;
        color: #777;
    }

    .project-info i, .testimonial-date i {
        color: var(--primary-yellow);
        width: 16px;
    }

    /* Trust Cards */
    .trust-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .trust-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    }

    .trust-icon {
        width: 60px;
        height: 60px;
        background-color: #fff3cd;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: var(--primary-yellow);
    }

    /* Stats */
    .stats-container {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 50px;
    }

    .stat-item {
        text-align: center;
        flex: 1;
        min-width: 180px;
        padding: 20px;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: var(--primary-yellow);
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 1.1rem;
        color: var(--charcoal);
    }

    /* CTA */
    .cta-section {
        background-color: var(--charcoal);
        color: var(--white);
        padding: 80px 0;
        text-align: center;
    }

    .cta-section h2 {
        margin-bottom: 20px;
    }

    .cta-section p {
        max-width: 700px;
        margin: 0 auto 30px;
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 80px 0;
        }
        .hero-section h1 {
            font-size: 2.5rem;
        }
        .section-padding {
            padding: 60px 0;
        }
        .stats-container {
            flex-direction: column;
            align-items: center;
        }
        .stat-item {
            margin-bottom: 20px;
        }
    }
</style>