<?php
/**
 * Common Footer Template
 * Modern, Professional Design
 */

declare(strict_types=1);

// Load settings for footer
require_once __DIR__ . '/settings.php';
$site_name = getSetting('site_name', 'Grand Jyothi Construction');
$company_desc = getSetting('company_description', 'Building excellence since 2005 with unmatched quality and customer satisfaction.');
$contact_info = getContactInfo();

// Define quick links for maintainability
$quick_links = [
    '/constructioninnagpur/index.php' => 'Home',
    '/constructioninnagpur/about.php' => 'About Us',
    '/constructioninnagpur/services.php' => 'Services',
    '/constructioninnagpur/projects.php' => 'Projects',
    '/constructioninnagpur/packages.php' => 'Packages',
    '/constructioninnagpur/blog.php' => 'Blog',
    '/constructioninnagpur/contact.php' => 'Contact'
];

$resource_links = [
    '/constructioninnagpur/faq.php' => 'FAQ',
    '/constructioninnagpur/team.php' => 'Our Team',
    '/constructioninnagpur/testimonials.php' => 'Testimonials',
    '/constructioninnagpur/privacy-policy.php' => 'Privacy Policy',
    '/constructioninnagpur/terms-of-service.php' => 'Terms of Service',
    '/constructioninnagpur/disclaimer.php' => 'Disclaimer'
];

// Social media links
$social_links = [
    'facebook' => [
        'url' => getSetting('facebook_url', '#'),
        'icon' => 'facebook',
        'label' => 'Facebook'
    ],
    'instagram' => [
        'url' => getSetting('instagram_url', '#'),
        'icon' => 'instagram',
        'label' => 'Instagram'
    ],
    'linkedin' => [
        'url' => getSetting('linkedin_url', '#'),
        'icon' => 'linkedin',
        'label' => 'LinkedIn'
    ],
    'youtube' => [
        'url' => getSetting('youtube_url', '#'),
        'icon' => 'youtube',
        'label' => 'YouTube'
    ]
];
?>
    
    </main><!-- End #main-content -->

    <!-- Footer -->
    <footer class="footer" role="contentinfo">
        <div class="container footer-container">
            <div class="footer-section">
                <h3><?= sanitizeOutput($site_name) ?></h3>
                <p><?= sanitizeOutput($company_desc) ?></p>
                <div class="social-links">
                    <?php foreach ($social_links as $platform => $social): ?>
                        <?php if ($social['url'] !== '#'): ?>
                        <a href="<?= sanitizeOutput($social['url']) ?>" 
                           class="social-link" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           aria-label="Follow us on <?= sanitizeOutput($social['label']) ?>">
                            <i data-feather="<?= sanitizeOutput($social['icon']) ?>" aria-hidden="true"></i>
                        </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <div class="project-type-badge">
                    <span>Residential Construction</span>
                    <span>Commercial Construction</span>
                    <span>Interior Design</span>
                    <span>Renovation</span>
                    <span>Consultation</span>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Quick Links</h3>
                <div class="footer-links">
                    <?php foreach ($quick_links as $url => $title): ?>
                    <a href="<?= sanitizeOutput($url) ?>" class="footer-link">
                        <?= sanitizeOutput($title) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Resources</h3>
                <div class="footer-links">
                    <?php foreach ($resource_links as $url => $title): ?>
                    <a href="<?= sanitizeOutput($url) ?>" class="footer-link">
                        <?= sanitizeOutput($title) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>Contact Us</h3>
                <address>
                    <p><i data-feather="map-pin"></i> <?= sanitizeOutput($contact_info['address'] ?? 'Nagpur, Maharashtra') ?></p>
                    <p><i data-feather="phone"></i> <?= sanitizeOutput($contact_info['phone'] ?? '+91 XXXXX XXXXX') ?></p>
                    <p><i data-feather="mail"></i> <?= sanitizeOutput($contact_info['email'] ?? 'info@example.com') ?></p>
                    <p><i data-feather="clock"></i> Mon-Sat: 9:00 AM - 6:00 PM</p>
                </address>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; <?= date('Y') ?> <?= sanitizeOutput($site_name) ?>. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Enhanced Scripts -->
    <script>
        // Enhanced Feather Icons initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            } else {
                console.warn('Feather Icons not loaded');
            }
        });
    </script>
</body>
</html>