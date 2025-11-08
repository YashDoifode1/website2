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
    <section>
        <p style="text-align: center; font-size: 1.1rem; margin-bottom: 3rem;">
            At Grand Jyothi Construction, we offer a complete range of construction services. 
            From concept to completion, we handle every aspect of your project with expertise and care.
        </p>
        
        <?php if (empty($services)): ?>
            <article>
                <p>No services available at the moment. Please check back later.</p>
            </article>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($services as $service): ?>
                    <article class="card">
                        <i data-feather="<?= sanitizeOutput($service['icon']) ?>" class="card-icon"></i>
                        <h3><?= sanitizeOutput($service['title']) ?></h3>
                        <p><?= sanitizeOutput($service['description']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Additional Service Information -->
    <section>
        <h2>Our Process</h2>
        <div class="grid">
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
    <section>
        <h2>Service Areas</h2>
        <p>
            We proudly serve clients across Nagpur and surrounding areas including:
        </p>
        <div class="grid">
            <div>
                <ul>
                    <li>Nagpur City</li>
                    <li>Dharampeth</li>
                    <li>Sadar</li>
                    <li>Ramdaspeth</li>
                    <li>Civil Lines</li>
                </ul>
            </div>
            <div>
                <ul>
                    <li>Sitabuldi</li>
                    <li>Wardha Road</li>
                    <li>Kamptee</li>
                    <li>Hingna</li>
                    <li>And surrounding areas</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section style="text-align: center; padding: 3rem 0;">
        <h2>Ready to Get Started?</h2>
        <p>Contact us today for a free consultation and quote.</p>
        <a href="/constructioninnagpur/contact.php" role="button">Request a Quote</a>
        <a href="/constructioninnagpur/projects.php" role="button" class="secondary">View Our Projects</a>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
