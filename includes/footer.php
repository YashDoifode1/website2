    <?php
    // Load settings for footer
    require_once __DIR__ . '/settings.php';
    $site_name = getSetting('site_name', 'Grand Jyothi Construction');
    $company_desc = getSetting('company_description', 'Building your vision with excellence and trust since 2005.');
    $contact_info = getContactInfo();
    ?>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4><?= sanitizeOutput($site_name) ?></h4>
                    <p><?= sanitizeOutput($company_desc) ?></p>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="/constructioninnagpur/index.php">Home</a></li>
                        <li><a href="/constructioninnagpur/about.php">About Us</a></li>
                        <li><a href="/constructioninnagpur/services.php">Services</a></li>
                        <li><a href="/constructioninnagpur/projects.php">Projects</a></li>
                        <li><a href="/constructioninnagpur/packages.php">Packages</a></li>
                        <li><a href="/constructioninnagpur/blog.php">Blog</a></li>
                        <li><a href="/constructioninnagpur/contact.php">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul class="footer-links">
                        <li><a href="/constructioninnagpur/faq.php">FAQ</a></li>
                        <li><a href="/constructioninnagpur/team.php">Our Team</a></li>
                        <li><a href="/constructioninnagpur/testimonials.php">Testimonials</a></li>
                        <li><a href="/constructioninnagpur/privacy-policy.php">Privacy Policy</a></li>
                        <li><a href="/constructioninnagpur/terms-of-service.php">Terms of Service</a></li>
                        <li><a href="/constructioninnagpur/disclaimer.php">Disclaimer</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <div class="footer-contact">
                        <i data-feather="map-pin"></i>
                        <span><?= sanitizeOutput($contact_info['address']) ?></span>
                    </div>
                    <div class="footer-contact">
                        <i data-feather="phone"></i>
                        <span><?= sanitizeOutput($contact_info['phone']) ?></span>
                    </div>
                    <div class="footer-contact">
                        <i data-feather="mail"></i>
                        <span><?= sanitizeOutput($contact_info['email']) ?></span>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= sanitizeOutput($site_name) ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Initialize Feather Icons with error handling -->
    <script>
        // Wait for DOM and feather to be ready
        if (typeof feather !== 'undefined') {
            feather.replace();
        } else {
            // Fallback if feather doesn't load
            window.addEventListener('load', function() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            });
        }
    </script>
</body>
</html>
