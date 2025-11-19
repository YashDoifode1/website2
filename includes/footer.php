<?php
/**
 * includes/footer.php
 * Modern, Responsive, Config-Driven Footer – 100% Fixed & Clean
 */

declare(strict_types=1);

// Load config (SITE_NAME, CONTACT_*, SOCIAL_MEDIA, etc.)
require_once __DIR__ . '/../config.php';

// Fallback sanitize
if (!function_exists('sanitizeOutput')) {
    function sanitizeOutput($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

// Site info
$site_name     = defined('SITE_NAME') ? SITE_NAME : 'Rakhi Construction & Consultancy Pvt Ltd';
$company_desc  = defined('SITE_TAGLINE') ? SITE_TAGLINE : 'Building Dreams in Nagpur Since Years';

// Contact info
$contact_info = [
    'address' => CONTACT_ADDRESS ?? 'PL NO 55, CHAKRADHAR HO NEAR NAGAR PANCHAYAT BAHADURA ROAD, Nagpur, MAHARASHTRA - 440034',
    'phone'   => CONTACT_PHONE ?? '+91 90759 56483',
    'email'   => CONTACT_EMAIL ?? 'info@rakhiconstruction.com',
    'phone2'  => defined('SECONDARY_PHONE') ? SECONDARY_PHONE : '+91 91128 41057'
];

// Navigation
$quick_links = [
    ''                => 'Home',
    'about.php'       => 'About Us',
    'services.php'    => 'Services',
    'projects.php'    => 'Projects',
    'packages.php'    => 'Packages',
    'blog.php'        => 'Blog',
    'contact.php'     => 'Contact'
];

$resource_links = [
    'faq.php'              => 'FAQ',
    'team.php'             => 'Our Team',
    'testimonials.php'     => 'Testimonials',
    'privacy-policy.php'   => 'Privacy Policy',
    'terms-of-service.php' => 'Terms of Service',
    'disclaimer.php'       => 'Disclaimer'
];

// Social Media Links (from config.php)
$social_links = [];
if (defined('SOCIAL_MEDIA') && is_array(SOCIAL_MEDIA)) {
    foreach (SOCIAL_MEDIA as $platform => $data) {
        if (!empty($data['url']) && $data['url'] !== '#') {
            $social_links[] = [
                'url'   => $data['url'],
                'icon'  => $data['icon'] ?? 'globe',
                'name'  => $data['name'] ?? ucfirst($platform)
            ];
        }
    }
}
?>

</main> <!-- Closes <main id="main-content"> from header.php -->

<!-- ====================== FOOTER ====================== -->
<footer class="footer" role="contentinfo">
    <div class="container footer-container">

        <!-- Company Info -->
        <div class="footer-about">
            <h3 class="footer-title"><?= sanitizeOutput($site_name) ?></h3>
            <p class="footer-description"><?= sanitizeOutput($company_desc) ?></p>

            <!-- Social Media -->
            <?php if (!empty($social_links)): ?>
                <div class="footer-social">
                    <?php foreach ($social_links as $social): ?>
                        <a href="<?= sanitizeOutput($social['url']) ?>" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           aria-label="<?= sanitizeOutput($social['name']) ?>" 
                           class="social-icon">
                            <i class="fab fa-<?= sanitizeOutput($social['icon']) ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

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
                <?php foreach ($quick_links as $page => $title): 
                    $url = $page === '' ? SITE_URL . '/' : SITE_URL . '/' . $page;
                ?>
                    <a href="<?= $url ?>" class="footer-link">
                        <?= sanitizeOutput($title) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Resources -->
        <div class="footer-section">
            <h3 class="footer-title">Resources</h3>
            <div class="footer-links">
                <?php foreach ($resource_links as $page => $title): ?>
                    <a href="<?= SITE_URL ?>/<?= $page ?>" class="footer-link">
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
                <p><i class="fas fa-phone"></i> <a href="tel:<?= sanitizeOutput(str_replace(' ', '', $contact_info['phone'])) ?>">
                    <?= sanitizeOutput($contact_info['phone']) ?>
                </a><br>
                <span style="margin-left: 1.5rem;">
                    <a href="tel:<?= sanitizeOutput(str_replace(' ', '', $contact_info['phone2'])) ?>">
                        <?= sanitizeOutput($contact_info['phone2']) ?>
                    </a>
                </span></p>
                <p><i class="fas fa-envelope"></i> <a href="mailto:<?= sanitizeOutput($contact_info['email']) ?>">
                    <?= sanitizeOutput($contact_info['email']) ?>
                </a></p>
                <p><i class="fas fa-clock"></i> Mon–Sat: 9:00 AM – 6:00 PM</p>
            </address>
        </div>

    </div>

    <!-- Copyright -->
    <div class="copyright">
        <p>&copy; <?= date('Y') ?> <?= sanitizeOutput($site_name) ?>. All Rights Reserved.</p>
    </div>
</footer>

<!-- Font Awesome (already loaded in header, but safe to keep) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
      crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    :root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --white: #fff;
        --text-muted: #bbbbbb;
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
        padding: 3.5rem 1.5rem 2rem;
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

    .footer-description {
        font-size: 0.95rem;
        line-height: 1.7;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }

    /* Social Icons */
    .footer-social {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .social-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(249, 168, 38, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-yellow);
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }

    .social-icon:hover {
        background: var(--primary-yellow);
        color: var(--charcoal);
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(249, 168, 38, 0.4);
    }

    /* Project Badges */
    .project-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .project-badges span {
        font-size: 0.75rem;
        background: rgba(249, 168, 38, 0.15);
        color: var(--primary-yellow);
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 500;
    }

    /* Links */
    .footer-links {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .footer-link {
        color: var(--text-muted);
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.3s ease;
        position: relative;
        padding-left: 1.3rem;
    }

    .footer-link::before {
        content: '→';
        position: absolute;
        left: 0;
        color: var(--primary-yellow);
        font-weight: bold;
        opacity: 0;
        transition: opacity 0.3s ease;
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
        gap: 0.8rem;
        font-size: 0.95rem;
        color: var(--text-muted);
        margin-bottom: 0.8rem;
    }

    .contact-info i {
        width: 18px;
        color: var(--primary-yellow);
        flex-shrink: 0;
    }

    .contact-info a {
        color: var(--text-muted);
        text-decoration: none;
        transition: color 0.3s;
    }

    .contact-info a:hover {
        color: var(--primary-yellow);
    }

    /* Copyright */
    .copyright {
        text-align: center;
        padding: 1.5rem 1rem;
        background: #0d0d0d;
        font-size: 0.875rem;
        color: var(--text-muted);
        border-top: 1px solid var(--border-color);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .footer-container {
            grid-template-columns: repeat(2, 1fr);
            gap: 2.5rem;
        }
    }

    @media (max-width: 768px) {
        .footer-container {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 2.5rem;
        }

        .footer-title::after {
            left: 50%;
            transform: translateX(-50%);
        }

        .footer-social,
        .project-badges,
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
        .footer-title { font-size: 1.15rem; }
        .footer-description, .footer-link, .contact-info p { font-size: 0.92rem; }
        .social-icon { width: 38px; height: 38px; font-size: 1rem; }
    }
</style>

</body>
</html>