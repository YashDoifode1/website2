<?php
/**
 * About Page - Grand Jyothi Construction
 * BuildDream Theme (Yellow + Charcoal) - Modern, Professional, Responsive
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'About Us | Grand Jyothi Construction';

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>Crafting Excellence Since 2005</h1>
        <p>Your Trusted Partner for Premium Construction Solutions in Nagpur</p>
    </div>
</section>

<main>
    <!-- Company Overview -->
    <section class="company-overview section-padding">
        <div class="container">
            <h2 class="section-title">Company Overview</h2>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="overview-content">
                        <p>Founded in 2005, <strong>Grand Jyothi Construction</strong> has evolved from a local contractor into Nagpur's premier construction partner. Our journey is defined by an unwavering commitment to quality, innovation, and client satisfaction that has stood the test of time.</p>
                        <p>Over nearly two decades, we've transformed visions into reality through hundreds of residential, commercial, and industrial projects across Maharashtra. Our multidisciplinary team of architects, engineers, and craftsmen brings unparalleled expertise to every project, ensuring exceptional results that endure.</p>
                        <p>What sets us apart is our personalized approach. From initial consultation to final handover, we collaborate closely with clients to ensure every detail reflects their unique vision while meeting our exacting standards of excellence.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="/constructioninnagpur/assets/images/placeholder.jpeg" 
                         alt="Grand Jyothi Construction Team" 
                         class="img-fluid rounded"
                         onerror="this.src='https://via.placeholder.com/600x400?text=Team+Photo'">
                </div>
            </div>

            <!-- Stats -->
            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-number">18+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Projects Completed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">450+</div>
                    <div class="stat-label">Happy Clients</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Quality Commitment</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="mission-vision-section section-padding">
        <div class="container">
            <h2 class="section-title">Our Mission & Vision</h2>
            <div class="mission-vision-container">
                <div class="mission-card">
                    <!-- <div class="mission-icon">
                        <i class="fas fa-bullseye"></i>
                    </div> -->
                    <h3>Our Mission</h3>
                    <p>
                        To redefine construction excellence by delivering innovative, sustainable solutions that exceed client expectations. We combine cutting-edge techniques with time-honored craftsmanship to build structures that inspire confidence and stand as testaments to quality.
                    </p>
                </div>
                <div class="vision-card">
                    <!-- <div class="vision-icon">
                        <i class="fas fa-eye"></i>
                    </div> -->
                    <h3>Our Vision</h3>
                    <p>
                        To be Central India's most trusted construction partner, recognized for setting industry benchmarks in quality, reliability, and client satisfaction. We envision shaping sustainable communities through transformative construction practices.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values
    <section class="section-padding">
        <div class="container">
            <h2 class="section-title">Our Core Values</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="value-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-award fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Quality Excellence</h4>
                        <p class="small">We never compromise on quality. Every project is executed with meticulous attention to detail and adherence to the highest industry standards.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-shield-alt fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Integrity & Trust</h4>
                        <p class="small">Transparency and honesty are the foundations of our business. We build lasting relationships through ethical practices and open communication.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Customer Focus</h4>
                        <p class="small">Our clients are at the heart of everything we do. We listen, understand, and deliver solutions that align with their vision and requirements.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-lightbulb fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Innovation</h4>
                        <p class="small">We embrace new technologies and construction methods to deliver efficient, sustainable, and cost-effective solutions.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Timely Delivery</h4>
                        <p class="small">We understand the importance of deadlines. Our efficient project management ensures on-time completion without compromising quality.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="value-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-leaf fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Sustainability</h4>
                        <p class="small">We are committed to environmentally responsible construction practices that minimize impact and promote sustainable development.</p>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- Why Choose Us -->
    <section class="section-padding bg-light">
        <div class="container">
            <h2 class="section-title">Why Partner With Grand Jyothi Construction?</h2>
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <img src="/constructioninnagpur/assets/images/placeholder.jpeg" 
                         alt="Quality Construction" 
                         class="img-fluid rounded"
                         onerror="this.src='https://via.placeholder.com/600x400?text=Quality+Work'">
                </div>
                <div class="col-lg-6">
                    <div class="why-choose-points">
                        <div class="point-item d-flex mb-4">
                            <div class="point-icon text-primary me-3">
                                <i class="fas fa-award fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold">18+ Years of Excellence</h4>
                                <p class="small mb-0">Proven expertise in residential and commercial construction across Maharashtra.</p>
                            </div>
                        </div>
                        <div class="point-item d-flex mb-4">
                            <div class="point-icon text-primary me-3">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold">Quality Assurance</h4>
                                <p class="small mb-0">Rigorous quality control processes ensure superior craftsmanship and materials.</p>
                            </div>
                        </div>
                        <div class="point-item d-flex mb-4">
                            <div class="point-icon text-primary me-3">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold">Timely Completion</h4>
                                <p class="small mb-0">Efficient project management delivers projects on schedule, every time.</p>
                            </div>
                        </div>
                        <div class="point-item d-flex mb-4">
                            <div class="point-icon text-primary me-3">
                                <i class="fas fa-dollar-sign fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold">Transparent Pricing</h4>
                                <p class="small mb-0">Honest quotes with no hidden costs - exceptional value for your investment.</p>
                            </div>
                        </div>
                        <div class="point-item d-flex mb-4">
                            <div class="point-icon text-primary me-3">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold">Expert Team</h4>
                                <p class="small mb-0">Certified professionals dedicated to realizing your construction vision.</p>
                            </div>
                        </div>
                        <div class="point-item d-flex">
                            <div class="point-icon text-primary me-3">
                                <i class="fas fa-headset fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="h6 fw-bold">Dedicated Support</h4>
                                <p class="small mb-0">Comprehensive after-sales service and 10-year structural warranty.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Custom Styles (BuildDream Theme) -->
<style>
    :root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --white: #FFFFFF;
        --light-gray: #f8f9fa;
        --medium-gray: #e9ecef;
    }

    body {
        font-family: 'Roboto', sans-serif;
        color: var(--charcoal);
        background-color: var(--white);
        line-height: 1.6;
    }

    h1, h2, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
    }

    .btn-primary {
        background-color: var(--primary-yellow);
        border: 2px solid var(--primary-yellow);
        color: var(--charcoal);
        font-weight: 600;
        padding: 10px 25px;
        border-radius: 6px;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        font-size: 0.95rem;
    }

    .btn-primary:hover {
        background-color: #e89a1f;
        border-color: #e89a1f;
        color: var(--charcoal);
    }

    .hero-section {
        background: linear-gradient(rgba(26, 26, 26, 0.7), rgba(26, 26, 26, 0.7)),
                    url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
        background-size: cover;
        color: var(--white);
        padding: 120px 0;
        text-align: center;
    }

    .hero-section h1 {
        font-size: 3.5rem;
        margin-bottom: 20px;
    }

    .hero-section p {
        font-size: 1.2rem;
        max-width: 700px;
        margin: 0 auto;
    }

    .section-padding {
        padding: 80px 0;
    }

    .section-title {
        font-size: 2.2rem;
        margin-bottom: 50px;
        text-align: center;
        position: relative;
    }

    .section-title:after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background-color: var(--primary-yellow);
    }

    .company-overview {
        background-color: var(--light-gray);
    }

    .stats-container {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        margin-top: 50px;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
        flex: 1;
        min-width: 200px;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: var(--primary-yellow);
        margin-bottom: 10px;
    }

    .mission-vision-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 40px;
    }

    .mission-card, .vision-card {
        background-color: var(--white);
        border-radius: 10px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        text-align: center;
        transition: transform 0.3s ease;
    }

    .mission-card:hover, .vision-card:hover {
        transform: translateY(-10px);
    }

    .mission-icon, .vision-icon {
        font-size: 3.5rem;
        color: var(--primary-yellow);
        margin-bottom: 25px;
    }

    .value-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .value-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    }

    .point-item {
        transition: all 0.3s ease;
    }

    .point-item:hover {
        transform: translateX(5px);
    }

    .point-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cta-section {
        background-color: var(--charcoal);
        color: var(--white);
        padding: 80px 0;
        text-align: center;
    }

    .cta-section h2 {
        margin-bottom: 20px;
    }

    .cta-section p {
        max-width: 700px;
        margin: 0 auto 30px;
        font-size: 1.1rem;
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 80px 0;
        }
        .hero-section h1 {
            font-size: 2.5rem;
        }
        .section-padding {
            padding: 60px 0;
        }
        .stats-container {
            flex-direction: column;
        }
        .stat-item {
            margin-bottom: 30px;
        }
    }
</style>