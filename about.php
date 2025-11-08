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
        <h1>About Grand Jyothi Construction</h1>
        <p>Building Excellence Since 2005</p>
    </div>
</header>

<main class="container">
    <!-- Company Overview -->
    <section>
        <div class="grid">
            <div>
                <h2>Our Story</h2>
                <p>
                    Founded in 2005, Grand Jyothi Construction has grown from a small local contractor to one of 
                    Nagpur's most trusted construction companies. Our journey has been marked by a commitment to 
                    quality, innovation, and customer satisfaction.
                </p>
                <p>
                    Over the years, we have successfully completed hundreds of residential, commercial, and 
                    industrial projects across Maharashtra. Our team of experienced professionals brings together 
                    expertise in architecture, engineering, and project management to deliver exceptional results.
                </p>
                <p>
                    We believe that every project is unique and deserves personalized attention. From initial 
                    consultation to final handover, we work closely with our clients to ensure their vision 
                    becomes reality.
                </p>
            </div>
            <div>
                <img src="https://via.placeholder.com/600x400?text=Grand+Jyothi+Construction" 
                     alt="Grand Jyothi Construction Office" 
                     style="width: 100%; border-radius: 8px;">
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section>
        <div class="grid">
            <article class="card">
                <h3><i data-feather="target"></i> Our Mission</h3>
                <p>
                    To deliver world-class construction services that exceed client expectations through 
                    innovation, quality craftsmanship, and sustainable practices. We strive to build lasting 
                    relationships based on trust, transparency, and excellence.
                </p>
            </article>
            
            <article class="card">
                <h3><i data-feather="eye"></i> Our Vision</h3>
                <p>
                    To be the most trusted and preferred construction partner in Central India, known for 
                    our commitment to quality, innovation, and customer satisfaction. We aim to set new 
                    standards in the construction industry.
                </p>
            </article>
        </div>
    </section>

    <!-- Core Values -->
    <section>
        <h2>Our Core Values</h2>
        <div class="grid">
            <article class="card">
                <i data-feather="award" class="card-icon"></i>
                <h4>Quality Excellence</h4>
                <p>
                    We never compromise on quality. Every project is executed with meticulous attention 
                    to detail and adherence to the highest industry standards.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="shield" class="card-icon"></i>
                <h4>Integrity & Trust</h4>
                <p>
                    Transparency and honesty are the foundations of our business. We build lasting 
                    relationships through ethical practices and open communication.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="users" class="card-icon"></i>
                <h4>Customer Focus</h4>
                <p>
                    Our clients are at the heart of everything we do. We listen, understand, and deliver 
                    solutions that align with their vision and requirements.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="zap" class="card-icon"></i>
                <h4>Innovation</h4>
                <p>
                    We embrace new technologies and construction methods to deliver efficient, sustainable, 
                    and cost-effective solutions.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="clock" class="card-icon"></i>
                <h4>Timely Delivery</h4>
                <p>
                    We understand the importance of deadlines. Our efficient project management ensures 
                    on-time completion without compromising quality.
                </p>
            </article>
            
            <article class="card">
                <i data-feather="leaf" class="card-icon"></i>
                <h4>Sustainability</h4>
                <p>
                    We are committed to environmentally responsible construction practices that minimize 
                    impact and promote sustainable development.
                </p>
            </article>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section>
        <h2>Why Choose Grand Jyothi Construction?</h2>
        <div class="grid">
            <div>
                <ul>
                    <li><strong>18+ Years of Experience</strong> in the construction industry</li>
                    <li><strong>500+ Completed Projects</strong> across residential and commercial sectors</li>
                    <li><strong>Expert Team</strong> of architects, engineers, and project managers</li>
                    <li><strong>Quality Assurance</strong> at every stage of construction</li>
                </ul>
            </div>
            <div>
                <ul>
                    <li><strong>Transparent Pricing</strong> with no hidden costs</li>
                    <li><strong>Timely Completion</strong> with efficient project management</li>
                    <li><strong>After-Sales Support</strong> and warranty services</li>
                    <li><strong>Licensed & Insured</strong> for your peace of mind</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section style="text-align: center; padding: 3rem 0;">
        <h2>Let's Build Something Amazing Together</h2>
        <p>Contact us today to discuss your construction project.</p>
        <a href="/constructioninnagpur/contact.php" role="button">Get in Touch</a>
        <a href="/constructioninnagpur/projects.php" role="button" class="secondary">View Our Work</a>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
