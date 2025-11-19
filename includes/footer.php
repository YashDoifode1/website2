<?php
/**
 * includes/footer.php
 * Uniform, responsive footer – works with your current site
 */

declare(strict_types=1);

// Fallback sanitize
if (!function_exists('sanitizeOutput')) {
    function sanitizeOutput($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

// Site info (replace with DB or config later)
$site_name = 'Grand Jyothi Construction';
$company_desc = 'Building excellence since 2005 with unmatched quality and customer satisfaction.';
$contact_info = [
    'address' => 'Nagpur, Maharashtra',
    'phone'   => '+91 98765 43210',
    'email'   => 'info@grandjyothi.com'
];

$quick_links = [
    'index.php' => 'Home',
    'about.php' => 'About Us',
    'services.php' => 'Services',
    'projects.php' => 'Projects',
    'packages.php' => 'Packages',
    'blog.php' => 'Blog',
    'contact.php' => 'Contact'
];

$resource_links = [
    'faq.php' => 'FAQ',
    'team.php' => 'Our Team',
    'testimonials.php' => 'Testimonials',
    'privacy-policy.php' => 'Privacy Policy',
    'terms-of-service.php' => 'Terms of Service',
    'disclaimer.php' => 'Disclaimer'
];

$social_links = [
    'facebook'  => ['url' => '#', 'icon' => 'facebook', 'label' => 'Facebook'],
    'instagram' => ['url' => '#', 'icon' => 'instagram', 'label' => 'Instagram'],
    'linkedin'  => ['url' => '#', 'icon' => 'linkedin', 'label' => 'LinkedIn'],
    'youtube'   => ['url' => '#', 'icon' => 'youtube', 'label' => 'YouTube']
];
?>

</main> <!-- End #main-content -->

<!-- ====================== FOOTER ====================== -->
<footer class="footer" role="contentinfo">
    <div class="container footer-container">

        <!-- Company Info -->
        <div class="footer-section">
            <h3 class="footer-title"><?= sanitizeOutput($site_name) ?></h3>
            <p class="footer-desc"><?= sanitizeOutput($company_desc) ?></p>

            <!-- Social Links -->
            <div class="social-links">
                <?php foreach ($social_links as $platform => $social): ?>
                    <?php if ($social['url'] !== '#'): ?>
                        <a href="<?= sanitizeOutput($social['url']) ?>" 
                           class="social-link" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           aria-label="Follow us on <?= sanitizeOutput($social['label']) ?>">
                            <i class="fab fa-<?= sanitizeOutput($social['icon']) ?>"></i>
                        </a>
                    <?php else: ?>
                        <span class="social-link disabled" aria-hidden="true">
                            <i class="fab fa-<?= sanitizeOutput($social['icon']) ?>"></i>
                        </span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Project Badges -->
            <div class="project-badges">
                <span>Residential</span>
                <span>Commercial</span>
                <span>Interior</span>
                <span>Renovation</span>
                <span>Consultation</span>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="footer-section">
            <h3 class="footer-title">Quick Links</h3>
            <div class="footer-links">
                <?php foreach ($quick_links as $url => $title): ?>
                    <a href="<?= SITE_URL ?>/<?= sanitizeOutput($url) ?>" class="footer-link">
                        <?= sanitizeOutput($title) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Resources -->
        <div class="footer-section">
            <h3 class="footer-title">Resources</h3>
            <div class="footer-links">
                <?php foreach ($resource_links as $url => $title): ?>
                    <a href="<?= SITE_URL ?>/<?= sanitizeOutput($url) ?>" class="footer-link">
                        <?= sanitizeOutput($title) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="footer-section">
            <h3 class="footer-title">Contact Us</h3>
            <address class="contact-info">
                <p><i class="fas fa-map-marker-alt"></i> <?= sanitizeOutput($contact_info['address']) ?></p>
                <p><i class="fas fa-phone"></i> <?= sanitizeOutput($contact_info['phone']) ?></p>
                <p><i class="fas fa-envelope"></i> <?= sanitizeOutput($contact_info['email']) ?></p>
                <p><i class="fas fa-clock"></i> Mon-Sat: 9:00 AM - 6:00 PM</p>
            </address>
        </div>

    </div>

    <!-- Copyright -->
    <div class="copyright">
        <p>&copy; <?= date('Y') ?> <?= sanitizeOutput($site_name) ?>. All Rights Reserved.</p>
    </div>
</footer>

<!-- Font Awesome (replaces Feather) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* === UNIFORM FOOTER STYLES === */
:root {
    --primary-yellow: #F9A826;
    --charcoal: #1A1A1A;
    --white: #fff;
    --light-gray: #f8f9fa;
    --text-muted: #bbb;
    --border-color: #333;
}

.footer {
    background: var(--charcoal);
    color: var(--white);
    font-family: 'Roboto', sans-serif;
    margin-top: auto;
}

.footer-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2.5rem;
    padding: 3.5rem 1rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
}

.footer-title {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: 1.25rem;
    color: var(--primary-yellow);
    margin-bottom: 1.2rem;
    position: relative;
    padding-bottom: 0.75rem;
}

.footer-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--primary-yellow);
    border-radius: 2px;
}

.footer-desc {
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--text-muted);
    margin-bottom: 1.5rem;
}

/* Social Links */
.social-links {
    display: flex;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(249, 168, 38, 0.15);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-yellow);
    text-decoration: none;
    transition: all .3s ease;
}

.social-link:hover {
    background: var(--primary-yellow);
    color: var(--charcoal);
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(249, 168, 38, 0.3);
}

.social-link i {
    font-size: 1.1rem;
}

.social-link.disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

/* Project Badges */
.project-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.project-badges span {
    font-size: 0.75rem;
    background: rgba(249, 168, 38, 0.15);
    color: var(--primary-yellow);
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-weight: 500;
}

/* Footer Links */
.footer-links {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.footer-link {
    color: var(--text-muted);
    text-decoration: none;
    font-size: 0.95rem;
    transition: color .3s ease;
    position: relative;
    padding-left: 1.2rem;
}

.footer-link::before {
    content: '→';
    position: absolute;
    left: 0;
    color: var(--primary-yellow);
    font-weight: bold;
    opacity: 0;
    transition: opacity .3s ease;
}

.footer-link:hover {
    color: var(--white);
}

.footer-link:hover::before {
    opacity: 1;
}

/* Contact Info */
.contact-info p {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.95rem;
    color: var(--text-muted);
    margin-bottom: 0.75rem;
}

.contact-info i {
    width: 16px;
    color: var(--primary-yellow);
    flex-shrink: 0;
}

/* Copyright */
.copyright {
    text-align: center;
    padding: 1.5rem 1rem;
    background: #111;
    font-size: 0.875rem;
    color: var(--text-muted);
    border-top: 1px solid var(--border-color);
}

/* === RESPONSIVE === */
@media (max-width: 992px) {
    .footer-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
        padding: 3rem 1rem 2rem;
    }
}

@media (max-width: 768px) {
    .footer-container {
        grid-template-columns: 1fr;
        gap: 2rem;
        text-align: center;
    }

    .footer-title::after {
        left: 50%;
        transform: translateX(-50%);
    }

    .social-links,
    .project-badges,
    .footer-links,
    .contact-info p {
        justify-content: center;
    }

    .footer-link {
        padding-left: 0;
    }

    .footer-link::before {
        display: none;
    }
}

@media (max-width: 480px) {
    .footer-title { font-size: 1.1rem; }
    .footer-desc, .footer-link, .contact-info p { font-size: 0.9rem; }
    .social-link { width: 36px; height: 36px; }
    .project-badges span { font-size: 0.7rem; padding: 0.3rem 0.6rem; }
}
</style>

</body>
</html>