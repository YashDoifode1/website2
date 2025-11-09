<?php
/**
 * About Page
 * 
 * Information about the company, mission, and values
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'About Us';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero">
    <div class="container">
        <h1>Crafting Excellence in Construction Since 2005</h1>
        <p>Your Trusted Partner for Premium Construction Solutions in Nagpur</p>
    </div>
</header>

<main class="container">
    <!-- Company Overview -->
    <section class="section">
        <div class="grid">
            <div>
                <hgroup>
                    <h2>Our Journey of Excellence</h2>
                    <p class="subtitle">Building visions into reality for over 18 years</p>
                </hgroup>
                <p>
                    Founded in 2005, Grand Jyothi Construction has evolved from a local contractor into Nagpur's premier construction partner. Our journey is defined by an unwavering commitment to quality, innovation, and client satisfaction that has stood the test of time.
                </p>
                <p>
                    Over nearly two decades, we've transformed visions into reality through hundreds of residential, commercial, and industrial projects across Maharashtra. Our multidisciplinary team of architects, engineers, and craftsmen brings unparalleled expertise to every project, ensuring exceptional results that endure.
                </p>
                <p>
                    What sets us apart is our personalized approach. From initial consultation to final handover, we collaborate closely with clients to ensure every detail reflects their unique vision while meeting our exacting standards of excellence.
                </p>
            </div>
            <div>
                <div class="featured-image">
                    <img src="https://via.placeholder.com/600x400?text=Grand+Jyothi+Construction" 
                         alt="Grand Jyothi Construction Team" 
                         style="width: 100%; border-radius: 8px;">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="section">
        <div class="section-header text-center">
            <h2>Our Guiding Principles</h2>
            <p class="lead">The foundation of our success</p>
        </div>
        
        <div class="grid">
            <article class="mission-card">
                <div class="mission-icon">
                    <i data-feather="target"></i>
                </div>
                <h3>Our Mission</h3>
                <p>
                    To redefine construction excellence by delivering innovative, sustainable solutions that exceed client expectations. We combine cutting-edge techniques with time-honored craftsmanship to build structures that inspire confidence and stand as testaments to quality.
                </p>
            </article>
            
            <article class="mission-card">
                <div class="mission-icon">
                    <i data-feather="eye"></i>
                </div>
                <h3>Our Vision</h3>
                <p>
                    To be Central India's most trusted construction partner, recognized for setting industry benchmarks in quality, reliability, and client satisfaction. We envision shaping sustainable communities through transformative construction practices.
                </p>
            </article>
        </div>
    </section>

    <!-- Core Values -->
    <section class="section">
        <div class="section-header text-center">
            <h2>Our Core Values</h2>
            <p class="lead">The foundation of everything we do</p>
        </div>
        
        <div class="grid grid-3">
            <article class="value-card">
                <div class="value-icon">
                    <i data-feather="award"></i>
                </div>
                <h4>Quality Excellence</h4>
                <p>
                    We never compromise on quality. Every project is executed with meticulous attention 
                    to detail and adherence to the highest industry standards.
                </p>
            </article>
            
            <article class="value-card">
                <div class="value-icon">
                    <i data-feather="shield"></i>
                </div>
                <h4>Integrity & Trust</h4>
                <p>
                    Transparency and honesty are the foundations of our business. We build lasting 
                    relationships through ethical practices and open communication.
                </p>
            </article>
            
            <article class="value-card">
                <div class="value-icon">
                    <i data-feather="users"></i>
                </div>
                <h4>Customer Focus</h4>
                <p>
                    Our clients are at the heart of everything we do. We listen, understand, and deliver 
                    solutions that align with their vision and requirements.
                </p>
            </article>
            
            <article class="value-card">
                <div class="value-icon">
                    <i data-feather="zap"></i>
                </div>
                <h4>Innovation</h4>
                <p>
                    We embrace new technologies and construction methods to deliver efficient, sustainable, 
                    and cost-effective solutions.
                </p>
            </article>
            
            <article class="value-card">
                <div class="value-icon">
                    <i data-feather="clock"></i>
                </div>
                <h4>Timely Delivery</h4>
                <p>
                    We understand the importance of deadlines. Our efficient project management ensures 
                    on-time completion without compromising quality.
                </p>
            </article>
            
            <article class="value-card">
                <div class="value-icon">
                    <i data-feather="leaf"></i>
                </div>
                <h4>Sustainability</h4>
                <p>
                    We are committed to environmentally responsible construction practices that minimize 
                    impact and promote sustainable development.
                </p>
            </article>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="section bg-light">
        <div class="section-header text-center">
            <h2>Why Partner With Grand Jyothi Construction?</h2>
            <p class="lead">The competitive advantages that set us apart</p>
        </div>
        
        <div class="grid">
            <div class="why-choose-image">
                <img src="https://via.placeholder.com/600x400?text=Quality+Construction" 
                     alt="Quality Construction" 
                     style="width: 100%; border-radius: 8px;">
            </div>
            <div>
                <div class="why-choose-points">
                    <div class="point-item">
                        <div class="point-icon">
                            <i data-feather="award"></i>
                        </div>
                        <div>
                            <h4>18+ Years of Excellence</h4>
                            <p>Proven expertise in residential and commercial construction across Maharashtra.</p>
                        </div>
                    </div>
                    
                    <div class="point-item">
                        <div class="point-icon">
                            <i data-feather="check-circle"></i>
                        </div>
                        <div>
                            <h4>Quality Assurance</h4>
                            <p>Rigorous quality control processes ensure superior craftsmanship and materials.</p>
                        </div>
                    </div>
                    
                    <div class="point-item">
                        <div class="point-icon">
                            <i data-feather="clock"></i>
                        </div>
                        <div>
                            <h4>Timely Completion</h4>
                            <p>Efficient project management delivers projects on schedule, every time.</p>
                        </div>
                    </div>
                    
                    <div class="point-item">
                        <div class="point-icon">
                            <i data-feather="dollar-sign"></i>
                        </div>
                        <div>
                            <h4>Transparent Pricing</h4>
                            <p>Honest quotes with no hidden costs - exceptional value for your investment.</p>
                        </div>
                    </div>
                    
                    <div class="point-item">
                        <div class="point-icon">
                            <i data-feather="users"></i>
                        </div>
                        <div>
                            <h4>Expert Team</h4>
                            <p>Certified professionals dedicated to realizing your construction vision.</p>
                        </div>
                    </div>
                    
                    <div class="point-item">
                        <div class="point-icon">
                            <i data-feather="headphones"></i>
                        </div>
                        <div>
                            <h4>Dedicated Support</h4>
                            <p>Comprehensive after-sales service and 10-year structural warranty.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="section cta-section text-center">
        <h2>Let's Build Something Amazing Together</h2>
        <p class="lead">Contact us today to discuss your construction project.</p>
        <div class="cta-buttons">
            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Get in Touch</a>
            <a href="/constructioninnagpur/projects.php" class="btn btn-outline">View Our Work</a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
