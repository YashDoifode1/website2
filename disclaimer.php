<?php
/**
 * Disclaimer Page
 * 
 * Legal disclaimers and limitations
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Disclaimer';

require_once __DIR__ . '/includes/header.php';
?>

<main class="container section">
    <article class="content-page">
        <h1>Disclaimer</h1>
        <p class="text-muted">Last Updated: <?= date('F d, Y') ?></p>
        
        <section>
            <h2>1. General Information</h2>
            <p>
                The information provided by Grand Jyothi Construction on this website is for general informational 
                purposes only. All information on the site is provided in good faith, however, we make no representation 
                or warranty of any kind, express or implied, regarding the accuracy, adequacy, validity, reliability, 
                availability, or completeness of any information on the site.
            </p>
        </section>
        
        <section>
            <h2>2. Professional Disclaimer</h2>
            <p>
                The content on this website does not constitute professional advice. Before making any decisions or 
                taking any actions based on the information provided on this website, you should consult with a 
                qualified professional. We are not liable for any losses or damages in connection with the use of 
                our website.
            </p>
        </section>
        
        <section>
            <h2>3. Website Content Disclaimer</h2>
            <p>
                Under no circumstance shall we have any liability to you for any loss or damage of any kind incurred 
                as a result of the use of the site or reliance on any information provided on the site. Your use of 
                the site and your reliance on any information on the site is solely at your own risk.
            </p>
        </section>
        
        <section>
            <h2>4. Project Estimates and Quotes</h2>
            <p>
                All project estimates, quotes, and pricing information provided on this website or through consultations 
                are approximate and subject to change. Final pricing will be determined based on:
            </p>
            <ul>
                <li>Detailed site inspection and assessment</li>
                <li>Specific project requirements and specifications</li>
                <li>Material costs at the time of project commencement</li>
                <li>Labor availability and market conditions</li>
                <li>Any unforeseen circumstances or complications</li>
            </ul>
            <p>
                Estimates are not binding contracts and should not be relied upon as final pricing.
            </p>
        </section>
        
        <section>
            <h2>5. Project Timelines</h2>
            <p>
                Project timelines and completion dates mentioned on this website or in initial discussions are estimates 
                only. Actual project duration may vary due to:
            </p>
            <ul>
                <li>Weather conditions</li>
                <li>Material delivery delays</li>
                <li>Permit and approval processes</li>
                <li>Unforeseen site conditions</li>
                <li>Changes in project scope</li>
                <li>Force majeure events</li>
            </ul>
        </section>
        
        <section>
            <h2>6. External Links Disclaimer</h2>
            <p>
                This website may contain links to external websites that are not provided or maintained by Grand Jyothi 
                Construction. We do not guarantee the accuracy, relevance, timeliness, or completeness of any information 
                on these external websites. We are not responsible for the content, privacy policies, or practices of 
                any third-party websites.
            </p>
        </section>
        
        <section>
            <h2>7. Testimonials and Reviews</h2>
            <p>
                Testimonials and reviews displayed on this website represent individual experiences and opinions. Results 
                may vary, and we do not guarantee that every client will experience the same results. Testimonials are 
                not necessarily representative of all client experiences.
            </p>
        </section>
        
        <section>
            <h2>8. Project Images and Portfolio</h2>
            <p>
                Images of completed projects displayed on this website are for illustrative purposes only. Actual results 
                may vary based on specific project requirements, site conditions, and client preferences. Images may have 
                been edited for presentation purposes.
            </p>
        </section>
        
        <section>
            <h2>9. Errors and Omissions</h2>
            <p>
                While we strive to provide accurate and up-to-date information, this website may contain technical 
                inaccuracies, typographical errors, or outdated information. We reserve the right to correct any errors, 
                inaccuracies, or omissions and to change or update information at any time without prior notice.
            </p>
        </section>
        
        <section>
            <h2>10. No Warranty</h2>
            <p>
                This website and its content are provided on an "as is" and "as available" basis without any warranties 
                of any kind, either express or implied, including but not limited to:
            </p>
            <ul>
                <li>Warranties of merchantability</li>
                <li>Fitness for a particular purpose</li>
                <li>Non-infringement</li>
                <li>Accuracy or completeness of content</li>
                <li>Uninterrupted or error-free operation</li>
            </ul>
        </section>
        
        <section>
            <h2>11. Limitation of Liability</h2>
            <p>
                In no event shall Grand Jyothi Construction, its directors, employees, or agents be liable for any 
                direct, indirect, incidental, consequential, special, or punitive damages arising out of or relating to:
            </p>
            <ul>
                <li>Your use or inability to use this website</li>
                <li>Any information obtained from this website</li>
                <li>Unauthorized access to or alteration of your data</li>
                <li>Any other matter relating to this website</li>
            </ul>
        </section>
        
        <section>
            <h2>12. Regulatory Compliance</h2>
            <p>
                While we strive to comply with all applicable building codes, regulations, and standards, it is the 
                client's responsibility to ensure that all necessary permits and approvals are obtained. We are not 
                liable for any regulatory violations or penalties resulting from incomplete or inaccurate information 
                provided by the client.
            </p>
        </section>
        
        <section>
            <h2>13. Changes to This Disclaimer</h2>
            <p>
                We reserve the right to modify this disclaimer at any time. Changes will be effective immediately upon 
                posting to our website. Your continued use of the website following the posting of changes constitutes 
                acceptance of those changes.
            </p>
        </section>
        
        <section>
            <h2>14. Contact Us</h2>
            <p>If you have any questions about this disclaimer, please contact us:</p>
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
