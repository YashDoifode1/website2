<?php
/**
 * FAQ Page
 * 
 * Frequently Asked Questions about our services
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Frequently Asked Questions';

require_once __DIR__ . '/includes/header.php';
?>

<main class="container section">
    <article class="content-page">
        <h1>Frequently Asked Questions</h1>
        <p class="text-muted">Find answers to common questions about our construction services</p>
        
        <section class="faq-section">
            <h2>General Questions</h2>
            
            <div class="faq-item">
                <h3>How long has Grand Jyothi Construction been in business?</h3>
                <p>
                    Grand Jyothi Construction has been serving Nagpur and Maharashtra for over 18 years, 
                    delivering high-quality construction projects since 2005.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>What types of projects do you handle?</h3>
                <p>
                    We specialize in residential, commercial, and industrial construction projects. Our services 
                    include new construction, renovations, interior design, and project management.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>What areas do you serve?</h3>
                <p>
                    We primarily serve Nagpur and surrounding areas in Maharashtra. For larger projects, we may 
                    consider locations throughout Maharashtra. Contact us to discuss your project location.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Are you licensed and insured?</h3>
                <p>
                    Yes, Grand Jyothi Construction is fully licensed and insured. We maintain all necessary 
                    certifications and insurance coverage to protect our clients and projects.
                </p>
            </div>
        </section>
        
        <section class="faq-section">
            <h2>Project Planning & Estimates</h2>
            
            <div class="faq-item">
                <h3>How do I get started with a project?</h3>
                <p>
                    Simply contact us through our website, phone, or email. We'll schedule a consultation to 
                    discuss your project requirements, visit the site if necessary, and provide you with a 
                    detailed proposal and estimate.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Do you provide free estimates?</h3>
                <p>
                    Yes, we offer free initial consultations and estimates for most projects. For complex projects 
                    requiring detailed architectural plans or engineering assessments, there may be a nominal fee 
                    which will be credited toward your project if you proceed.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>How long does it take to receive an estimate?</h3>
                <p>
                    For standard projects, we typically provide estimates within 3-5 business days after the initial 
                    consultation. Complex projects may require additional time for detailed planning and assessment.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Can I make changes to the project after it starts?</h3>
                <p>
                    Yes, changes can be made through our change order process. However, modifications may affect 
                    the project timeline and cost. We'll provide updated estimates for any requested changes.
                </p>
            </div>
        </section>
        
        <section class="faq-section">
            <h2>Costs & Payment</h2>
            
            <div class="faq-item">
                <h3>What factors affect the cost of a construction project?</h3>
                <p>
                    Project costs depend on several factors including: size and complexity, materials selected, 
                    site conditions, labor requirements, permits and fees, and current market conditions for 
                    materials and labor.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>What payment methods do you accept?</h3>
                <p>
                    We accept various payment methods including bank transfers, checks, and online payments. 
                    Payment terms will be outlined in your project contract.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Do you require a deposit?</h3>
                <p>
                    Yes, we typically require a deposit to secure your project schedule and begin material 
                    procurement. The deposit amount varies by project size and will be specified in your contract.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Do you offer financing options?</h3>
                <p>
                    We can provide information about financing options and may work with lending institutions. 
                    Contact us to discuss available financing solutions for your project.
                </p>
            </div>
        </section>
        
        <section class="faq-section">
            <h2>Project Timeline & Process</h2>
            
            <div class="faq-item">
                <h3>How long will my project take?</h3>
                <p>
                    Project duration varies based on size, complexity, and scope. Small renovations may take 2-4 weeks, 
                    while new home construction typically takes 6-12 months. We'll provide a detailed timeline in your 
                    project proposal.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Will I be able to live in my home during renovation?</h3>
                <p>
                    This depends on the scope of work. For minor renovations, you may be able to stay in your home. 
                    Major renovations might require temporary relocation. We'll discuss this during project planning.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>How do you handle permits and approvals?</h3>
                <p>
                    We handle all necessary permits and approvals as part of our service. We'll coordinate with local 
                    authorities and ensure all work complies with building codes and regulations.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>How often will I receive project updates?</h3>
                <p>
                    We provide regular updates throughout your project. You'll have a dedicated project manager who 
                    will communicate progress, address concerns, and keep you informed at every stage.
                </p>
            </div>
        </section>
        
        <section class="faq-section">
            <h2>Materials & Quality</h2>
            
            <div class="faq-item">
                <h3>Can I choose my own materials?</h3>
                <p>
                    Absolutely! We encourage client involvement in material selection. We can also provide 
                    recommendations based on quality, durability, and budget considerations.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Do you use quality materials?</h3>
                <p>
                    Yes, we use only high-quality materials from reputable suppliers. We believe in building 
                    projects that stand the test of time and provide excellent value.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>What if materials are damaged during construction?</h3>
                <p>
                    We take full responsibility for materials once they're on-site. Any damaged materials will be 
                    replaced at no additional cost to you.
                </p>
            </div>
        </section>
        
        <section class="faq-section">
            <h2>Warranty & Support</h2>
            
            <div class="faq-item">
                <h3>Do you provide warranties on your work?</h3>
                <p>
                    Yes, we provide warranties on our workmanship and materials. Warranty terms vary by project 
                    type and will be detailed in your contract. We also honor manufacturer warranties on materials 
                    and fixtures.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>What happens if there's a problem after completion?</h3>
                <p>
                    We stand behind our work. If any issues arise during the warranty period, contact us immediately 
                    and we'll address them promptly. We're committed to your complete satisfaction.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Do you provide maintenance services?</h3>
                <p>
                    Yes, we offer maintenance and repair services for projects we've completed. We can also provide 
                    guidance on proper maintenance to extend the life of your construction.
                </p>
            </div>
        </section>
        
        <section class="faq-section">
            <h2>Safety & Environment</h2>
            
            <div class="faq-item">
                <h3>What safety measures do you follow?</h3>
                <p>
                    Safety is our top priority. We follow all OSHA guidelines and local safety regulations. Our team 
                    is trained in safety protocols, and we maintain a clean and organized work site.
                </p>
            </div>
            
            <div class="faq-item">
                <h3>Do you follow environmentally friendly practices?</h3>
                <p>
                    Yes, we're committed to sustainable construction practices. We minimize waste, recycle materials 
                    when possible, and can incorporate eco-friendly materials and energy-efficient solutions into 
                    your project.
                </p>
            </div>
        </section>
        
        <section style="text-align: center; margin-top: 3rem; padding: 2rem; background: #f8f9fa; border-radius: 8px;">
            <h2>Still Have Questions?</h2>
            <p>Can't find the answer you're looking for? We're here to help!</p>
            <a href="/constructioninnagpur/contact.php" role="button">Contact Us</a>
        </section>
    </article>
</main>

<style>
.faq-section {
    margin-bottom: 3rem;
}

.faq-item {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e0e0e0;
}

.faq-item:last-child {
    border-bottom: none;
}

.faq-item h3 {
    color: #2c3e50;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.faq-item p {
    color: #555;
    line-height: 1.6;
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
