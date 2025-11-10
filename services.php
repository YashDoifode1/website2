<?php
/**
 * Services Page - Grand Jyothi Construction
 * BuildDream Theme: Modern, Professional, Yellow + Charcoal
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Our Services | Grand Jyothi Construction';

// Fetch all services
$sql = "SELECT title, description, icon FROM services ORDER BY created_at DESC";
$stmt = executeQuery($sql);
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>Our Construction Services</h1>
        <p>Comprehensive solutions from concept to completion</p>
    </div>
</section>

<main>
    <!-- Services Grid -->
    <section class="section-padding">
        <div class="container">
            <p class="text-center mb-5 lead text-muted">
                At Grand Jyothi Construction, we offer a complete range of construction services. 
                From concept to completion, we handle every aspect of your project with expertise and care.
            </p>

            <?php if (empty($services)): ?>
                <div class="text-center py-5">
                    <p class="text-muted">No services available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <div class="services-grid">
                    <?php foreach ($services as $service): 
                        $icon = sanitizeOutput($service['icon']) ?: 'tool';
                    ?>
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fas fa-<?= $icon ?> fa-2x"></i>
                            </div>
                            <h3 class="service-title"><?= sanitizeOutput($service['title']) ?></h3>
                            <p class="service-desc"><?= sanitizeOutput($service['description']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Our Process -->
    <section class="section-padding bg-light">
        <div class="container">
            <h2 class="section-title">Our Proven Process</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="process-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="process-number">1</div>
                        <h4 class="h5 fw-bold">Consultation</h4>
                        <p class="small">We begin with a detailed consultation to understand your requirements, budget, and timeline.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="process-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="process-number">2</div>
                        <h4 class="h5 fw-bold">Planning & Design</h4>
                        <p class="small">Our architects and engineers create detailed plans and designs that align with your vision.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="process-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="process-number">3</div>
                        <h4 class="h5 fw-bold">Execution</h4>
                        <p class="small">Skilled team begins construction using quality materials and modern techniques.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="process-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="process-number">4</div>
                        <h4 class="h5 fw-bold">Quality Check</h4>
                        <p class="small">Regular inspections ensure the highest standards are maintained throughout.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="process-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="process-number">5</div>
                        <h4 class="h5 fw-bold">Handover</h4>
                        <p class="small">Final walkthrough, documentation, and handover with complete satisfaction.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="process-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="process-number">6</div>
                        <h4 class="h5 fw-bold">After-Sales Support</h4>
                        <p class="small">Ongoing support and warranty services to ensure long-term satisfaction.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Service Areas -->
    <section class="section-padding">
        <div class="container">
            <h2 class="section-title">Our Service Areas</h2>
            <p class="text-center mb-5 lead text-muted">Serving clients across Nagpur and surrounding regions</p>
            <div class="areas-grid">
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Nagpur City</h4>
                    <p>Core city and surrounding areas</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Dharampeth</h4>
                    <p>Premium residential & commercial zone</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Sadar</h4>
                    <p>Central business district</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Ramdaspeth</h4>
                    <p>High-end residential area</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Civil Lines</h4>
                    <p>Heritage and government zone</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Sitabuldi</h4>
                    <p>Commercial & market hub</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Wardha Road</h4>
                    <p>Industrial & logistics corridor</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Kamptee</h4>
                    <p>Suburban residential growth</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Hingna</h4>
                    <p>Industrial & manufacturing zone</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Koradi</h4>
                    <p>Emerging residential area</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>Manish Nagar</h4>
                    <p>Modern residential locality</p>
                </div>
                <div class="area-card">
                    <div class="area-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h4>And Surrounding Areas</h4>
                    <p>We serve all nearby regions</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <!-- <section class="cta-section">
        <div class="container">
            <h2>Ready to Build Your Vision?</h2>
            <p>Contact us today for a free consultation and personalized quote</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Request a Quote</a>
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
                    url('https://images.unsplash.com/photo-1503387762-592deb58ef4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
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

    /* Services Grid */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .service-card {
        background-color: var(--white);
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }

    .service-icon {
        color: var(--primary-yellow);
        margin-bottom: 20px;
    }

    .service-title {
        font-size: 1.3rem;
        margin-bottom: 15px;
        color: var(--charcoal);
    }

    .service-desc {
        color: #666;
        font-size: 0.95rem;
    }

    /* Process Cards */
    .process-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .process-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    }

    .process-number {
        width: 50px;
        height: 50px;
        background-color: var(--primary-yellow);
        color: var(--charcoal);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 auto 15px;
    }

    /* Service Areas */
    .areas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }

    .area-card {
        background-color: var(--light-gray);
        border-radius: 10px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .area-card:hover {
        background-color: var(--primary-yellow);
        color: var(--charcoal);
        transform: translateY(-5px);
    }

    .area-card:hover .area-icon {
        color: var(--charcoal);
    }

    .area-icon {
        color: var(--primary-yellow);
        font-size: 1.8rem;
        margin-bottom: 15px;
        transition: color 0.3s ease;
    }

    .area-card h4 {
        margin-bottom: 10px;
        font-size: 1.1rem;
    }

    .area-card p {
        font-size: 0.9rem;
        margin: 0;
        color: #666;
    }

    .area-card:hover p {
        color: #444;
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
        .process-number {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }
    }
</style>