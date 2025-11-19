<?php
/**
 * Terms of Service Page
 * 
 * Legal terms and conditions for using our services
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Terms of Service';

require_once __DIR__ . '/includes/header.php';
?>

<main class="container section">
    <article class="content-page">
        <h1>Terms of Service</h1>
        <p class="text-muted">Last Updated: <?= date('F d, Y') ?></p>
        
        <section>
            <h2>1. Acceptance of Terms</h2>
            <p>
                By accessing and using the Rakhi Construction & Consultancy Pvt Ltd website and services, you accept and agree to be 
                bound by the terms and provisions of this agreement. If you do not agree to these terms, please do not 
                use our services.
            </p>
        </section>
        
        <section>
            <h2>2. Services Description</h2>
            <p>
                Rakhi Construction & Consultancy Pvt Ltd provides construction, renovation, and related services including but not limited to:
            </p>
            <ul>
                <li>Residential construction and renovation</li>
                <li>Commercial building projects</li>
                <li>Interior design and decoration</li>
                <li>Project consultation and planning</li>
                <li>Construction management services</li>
            </ul>
        </section>
        
        <section>
            <h2>3. User Obligations</h2>
            <p>When using our services, you agree to:</p>
            <ul>
                <li>Provide accurate and complete information</li>
                <li>Maintain the confidentiality of any account credentials</li>
                <li>Notify us immediately of any unauthorized use</li>
                <li>Use our services only for lawful purposes</li>
                <li>Not interfere with or disrupt our services</li>
                <li>Not attempt to gain unauthorized access to our systems</li>
            </ul>
        </section>
        
        <section>
            <h2>4. Project Agreements</h2>
            <p>
                All construction projects are subject to separate written agreements that will specify:
            </p>
            <ul>
                <li>Project scope and specifications</li>
                <li>Timeline and milestones</li>
                <li>Payment terms and schedule</li>
                <li>Warranty and guarantee provisions</li>
                <li>Change order procedures</li>
            </ul>
            <p>
                These Terms of Service do not constitute a construction contract. Specific project terms will be 
                outlined in individual project agreements.
            </p>
        </section>
        
        <section>
            <h2>5. Pricing and Payment</h2>
            <ul>
                <li>All prices are subject to change without notice</li>
                <li>Quotes are valid for 30 days unless otherwise specified</li>
                <li>Payment terms will be specified in individual project contracts</li>
                <li>Late payments may incur additional charges</li>
                <li>We reserve the right to suspend work for non-payment</li>
            </ul>
        </section>
        
        <section>
            <h2>6. Intellectual Property</h2>
            <p>
                All content on this website, including text, graphics, logos, images, and software, is the property of 
                Rakhi Construction & Consultancy Pvt Ltd and is protected by copyright and intellectual property laws. You may not 
                reproduce, distribute, or create derivative works without our express written permission.
            </p>
        </section>
        
        <section>
            <h2>7. Warranties and Disclaimers</h2>
            <p>
                While we strive for excellence in all our projects:
            </p>
            <ul>
                <li>We provide warranties as specified in individual project contracts</li>
                <li>We do not warrant that our website will be uninterrupted or error-free</li>
                <li>Information on our website is provided "as is" without warranties of any kind</li>
                <li>We are not responsible for delays caused by circumstances beyond our control</li>
            </ul>
        </section>
        
        <section>
            <h2>8. Limitation of Liability</h2>
            <p>
                To the maximum extent permitted by law, Rakhi Construction & Consultancy Pvt Ltd shall not be liable for any indirect, 
                incidental, special, consequential, or punitive damages arising from your use of our services or website.
            </p>
        </section>
        
        <section>
            <h2>9. Indemnification</h2>
            <p>
                You agree to indemnify and hold harmless Rakhi Construction & Consultancy Pvt Ltd, its officers, directors, employees, 
                and agents from any claims, damages, losses, liabilities, and expenses arising from your use of our 
                services or violation of these terms.
            </p>
        </section>
        
        <section>
            <h2>10. Cancellation and Refunds</h2>
            <p>
                Cancellation and refund policies will be specified in individual project contracts. Generally:
            </p>
            <ul>
                <li>Consultation fees are non-refundable</li>
                <li>Project cancellations must be made in writing</li>
                <li>Refunds will be processed according to contract terms</li>
                <li>Work completed prior to cancellation will be billed</li>
            </ul>
        </section>
        
        <section>
            <h2>11. Force Majeure</h2>
            <p>
                We shall not be liable for any failure to perform our obligations due to circumstances beyond our 
                reasonable control, including natural disasters, acts of government, labor disputes, or material shortages.
            </p>
        </section>
        
        <section>
            <h2>12. Governing Law</h2>
            <p>
                These Terms of Service shall be governed by and construed in accordance with the laws of India. 
                Any disputes arising from these terms shall be subject to the exclusive jurisdiction of the courts 
                in Nagpur, Maharashtra.
            </p>
        </section>
        
        <section>
            <h2>13. Modifications to Terms</h2>
            <p>
                We reserve the right to modify these Terms of Service at any time. Changes will be effective immediately 
                upon posting to our website. Your continued use of our services constitutes acceptance of the modified terms.
            </p>
        </section>
        
        <section>
            <h2>14. Severability</h2>
            <p>
                If any provision of these Terms of Service is found to be invalid or unenforceable, the remaining 
                provisions shall continue in full force and effect.
            </p>
        </section>
        
        <section>
            <h2>15. Contact Information</h2>
            <p>For questions about these Terms of Service, please contact us:</p>
            <ul>
                <li><strong>Email:</strong> <?= CONTACT_EMAIL ?></li>
                <li><strong>Phone:</strong> <?= CONTACT_PHONE ?></li>
                <li><strong>Address:</strong> <?= CONTACT_ADDRESS ?></li>
            </ul>
        </section>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo SITE_URL; ?>/index.php" role="button">Back to Home</a>
        </div>
    </article>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
