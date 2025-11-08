<?php
/**
 * Privacy Policy Page
 * 
 * Details about how user data is collected, used, and protected
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Privacy Policy';

require_once __DIR__ . '/includes/header.php';
?>

<main class="container section">
    <article class="content-page">
        <h1>Privacy Policy</h1>
        <p class="text-muted">Last Updated: <?= date('F d, Y') ?></p>
        
        <section>
            <h2>1. Introduction</h2>
            <p>
                Grand Jyothi Construction ("we," "our," or "us") is committed to protecting your privacy. 
                This Privacy Policy explains how we collect, use, disclose, and safeguard your information 
                when you visit our website or use our services.
            </p>
        </section>
        
        <section>
            <h2>2. Information We Collect</h2>
            
            <h3>2.1 Personal Information</h3>
            <p>We may collect personal information that you voluntarily provide to us when you:</p>
            <ul>
                <li>Fill out contact forms</li>
                <li>Request quotes or consultations</li>
                <li>Subscribe to our newsletter</li>
                <li>Participate in surveys or promotions</li>
                <li>Contact us via email or phone</li>
            </ul>
            <p>This information may include:</p>
            <ul>
                <li>Name and contact information (email, phone number, address)</li>
                <li>Project details and requirements</li>
                <li>Payment information (processed securely through third-party payment processors)</li>
                <li>Any other information you choose to provide</li>
            </ul>
            
            <h3>2.2 Automatically Collected Information</h3>
            <p>When you visit our website, we may automatically collect certain information about your device, including:</p>
            <ul>
                <li>IP address and browser type</li>
                <li>Operating system and device information</li>
                <li>Pages visited and time spent on pages</li>
                <li>Referring website addresses</li>
                <li>Cookies and similar tracking technologies</li>
            </ul>
        </section>
        
        <section>
            <h2>3. How We Use Your Information</h2>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Provide, operate, and maintain our services</li>
                <li>Process your requests and respond to inquiries</li>
                <li>Send you quotes, proposals, and project updates</li>
                <li>Improve our website and services</li>
                <li>Send marketing communications (with your consent)</li>
                <li>Detect and prevent fraud or security issues</li>
                <li>Comply with legal obligations</li>
            </ul>
        </section>
        
        <section>
            <h2>4. Information Sharing and Disclosure</h2>
            <p>We do not sell, trade, or rent your personal information to third parties. We may share your information with:</p>
            <ul>
                <li><strong>Service Providers:</strong> Third-party vendors who assist us in operating our website and conducting our business</li>
                <li><strong>Legal Requirements:</strong> When required by law or to protect our rights and safety</li>
                <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
            </ul>
        </section>
        
        <section>
            <h2>5. Data Security</h2>
            <p>
                We implement appropriate technical and organizational security measures to protect your personal information 
                from unauthorized access, disclosure, alteration, or destruction. However, no method of transmission over 
                the Internet or electronic storage is 100% secure.
            </p>
        </section>
        
        <section>
            <h2>6. Cookies and Tracking Technologies</h2>
            <p>
                We use cookies and similar tracking technologies to enhance your browsing experience, analyze website traffic, 
                and understand user preferences. You can control cookie settings through your browser preferences.
            </p>
        </section>
        
        <section>
            <h2>7. Your Rights and Choices</h2>
            <p>You have the right to:</p>
            <ul>
                <li>Access and review your personal information</li>
                <li>Request correction of inaccurate information</li>
                <li>Request deletion of your information (subject to legal requirements)</li>
                <li>Opt-out of marketing communications</li>
                <li>Disable cookies through your browser settings</li>
            </ul>
        </section>
        
        <section>
            <h2>8. Children's Privacy</h2>
            <p>
                Our services are not intended for individuals under the age of 18. We do not knowingly collect 
                personal information from children.
            </p>
        </section>
        
        <section>
            <h2>9. Third-Party Links</h2>
            <p>
                Our website may contain links to third-party websites. We are not responsible for the privacy 
                practices of these external sites. We encourage you to review their privacy policies.
            </p>
        </section>
        
        <section>
            <h2>10. Changes to This Privacy Policy</h2>
            <p>
                We may update this Privacy Policy from time to time. We will notify you of any changes by posting 
                the new Privacy Policy on this page and updating the "Last Updated" date.
            </p>
        </section>
        
        <section>
            <h2>11. Contact Us</h2>
            <p>If you have any questions about this Privacy Policy, please contact us:</p>
            <ul>
                <li><strong>Email:</strong> <?= CONTACT_EMAIL ?></li>
                <li><strong>Phone:</strong> <?= CONTACT_PHONE ?></li>
                <li><strong>Address:</strong> <?= CONTACT_ADDRESS ?></li>
            </ul>
        </section>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="/constructioninnagpur/index.php" role="button">Back to Home</a>
        </div>
    </article>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
