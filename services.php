<?php
/**
 * Services Page
 * 
 * Displays all construction services offered
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Our Services';

// Fetch all services
$sql = "SELECT * FROM services ORDER BY created_at DESC";
$stmt = executeQuery($sql);
$services = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero">
    <div class="container">
        <h1>Our Services</h1>
        <p>Comprehensive construction solutions tailored to your needs</p>
    </div>
</header>

<main class="container">
    <section class="section">
        <p class="lead text-center">
            At Grand Jyothi Construction, we offer a complete range of construction services. 
            From concept to completion, we handle every aspect of your project with expertise and care.
        </p>
        
        <?php if (empty($services)): ?>
            <article class="card text-center">
                <p>No services available at the moment. Please check back later.</p>
            </article>
        <?php else: ?>
            <div class="grid grid-4">
                <?php foreach ($services as $service): ?>
                    <article class="service-card">
                        <div class="service-icon">
                            <i data-feather="<?= sanitizeOutput($service['icon']) ?>"></i>
                        </div>
                        <h3><?= sanitizeOutput($service['title']) ?></h3>
                        <p><?= sanitizeOutput($service['description']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Additional Service Information -->
    <section class="section">
        <h2 class="text-center">Our Process</h2>
        <div class="grid grid-3">
            <article class="card">
                <h4>1. Consultation</h4>
                <p>
                    We begin with a detailed consultation to understand your requirements, budget, 
                    and timeline. Our experts provide professional advice and feasibility analysis.
                </p>
            </article>
            
            <article class="card">
                <h4>2. Planning & Design</h4>
                <p>
                    Our architects and engineers create detailed plans and designs that align with 
                    your vision while ensuring structural integrity and compliance.
                </p>
            </article>
            
            <article class="card">
                <h4>3. Execution</h4>
                <p>
                    With approved plans, our skilled team begins construction using quality materials 
                    and modern techniques, ensuring safety and efficiency.
                </p>
            </article>
            
            <article class="card">
                <h4>4. Quality Check</h4>
                <p>
                    Regular inspections and quality checks are conducted at every stage to ensure 
                    the highest standards are maintained throughout the project.
                </p>
            </article>
            
            <article class="card">
                <h4>5. Handover</h4>
                <p>
                    Upon completion, we conduct a final walkthrough, address any concerns, and hand 
                    over your project with complete documentation.
                </p>
            </article>
            
            <article class="card">
                <h4>6. After-Sales Support</h4>
                <p>
                    Our relationship doesn't end at handover. We provide ongoing support and warranty 
                    services to ensure your complete satisfaction.
                </p>
            </article>
        </div>
    </section>

    <!-- Service Areas -->
    <section class="section">
        <div class="section-header text-center">
            <h2>Our Service Areas</h2>
            <p class="lead">Serving clients across Nagpur and surrounding regions</p>
        </div>
        
        <div class="grid grid-4">
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Nagpur City</h4>
                <p>Nagpur city and its surrounding areas are our primary service locations.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Dharampeth</h4>
                <p>We provide construction services in Dharampeth and its nearby areas.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Sadar</h4>
                <p>Sadar and its surrounding areas are also covered under our service locations.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Ramdaspeth</h4>
                <p>We offer construction services in Ramdaspeth and its nearby areas.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Civil Lines</h4>
                <p>Civil Lines and its surrounding areas are also covered under our service locations.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Sitabuldi</h4>
                <p>We provide construction services in Sitabuldi and its nearby areas.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Wardha Road</h4>
                <p>Wardha Road and its surrounding areas are also covered under our service locations.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Kamptee</h4>
                <p>We offer construction services in Kamptee and its nearby areas.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Hingna</h4>
                <p>Hingna and its surrounding areas are also covered under our service locations.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Koradi</h4>
                <p>We provide construction services in Koradi and its nearby areas.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>Manish Nagar</h4>
                <p>Manish Nagar and its surrounding areas are also covered under our service locations.</p>
            </div>
            
            <div class="service-area-card">
                <div class="area-icon">
                    <i data-feather="map-pin"></i>
                </div>
                <h4>And surrounding areas</h4>
                <p>We also serve clients in surrounding areas of Nagpur.</p>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section cta-section text-center">
        <h2>Ready to Get Started?</h2>
        <p class="lead">Contact us today for a free consultation and quote.</p>
        <div class="cta-buttons">
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Request a Quote</a>
            <a href="/constructioninnagpur/projects.php" class="btn btn-outline">View Our Projects</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
