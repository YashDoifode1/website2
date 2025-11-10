<?php
/**
 * Team Page - Grand Jyothi Construction
 * BuildDream Theme: Modern, Professional, Yellow + Charcoal
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Our Team | Grand Jyothi Construction';

// Fetch team members
$sql = "SELECT name, role, photo, bio, expertise, linkedin, email 
        FROM team 
        ORDER BY created_at ASC";
$stmt = executeQuery($sql);
$team_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>Meet Our Expert Team</h1>
        <p>Passionate professionals dedicated to transforming your construction vision into reality</p>
    </div>
</section>

<main>
    <!-- Team Grid -->
    <section class="section-padding">
        <div class="container">
            <h2 class="section-title">Our Leadership & Experts</h2>
            <p class="text-center mb-5 lead text-muted">
                Our team of experienced professionals is dedicated to delivering exceptional results. 
                With diverse expertise in architecture, engineering, and project management, we work 
                together to bring your vision to life.
            </p>

            <?php if (empty($team_members)): ?>
                <div class="text-center py-5">
                    <p class="text-muted">Team information will be available soon.</p>
                </div>
            <?php else: ?>
                <div class="team-grid">
                    <?php foreach ($team_members as $member): 
                        $photo = !empty($member['photo']) ? "/constructioninnagpur/assets/images/{$member['photo']}" : "https://via.placeholder.com/300x300?text=" . urlencode($member['name']);
                        $expertise_list = !empty($member['expertise']) ? array_map('trim', explode(',', $member['expertise'])) : [];
                    ?>
                    <div class="team-member">
                        <div class="member-image">
                            <img src="<?= $photo ?>" 
                                 alt="<?= sanitizeOutput($member['name']) ?>" 
                                 onerror="this.src='https://via.placeholder.com/300x300?text=<?= urlencode($member['name']) ?>'">
                        </div>
                        <div class="member-info">
                            <h4 class="member-name"><?= sanitizeOutput($member['name']) ?></h4>
                            <div class="member-role"><?= sanitizeOutput($member['role']) ?></div>
                            
                            <?php if ($member['bio']): ?>
                                <p class="member-bio"><?= sanitizeOutput($member['bio']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($expertise_list)): ?>
                                <div class="expertise-tags">
                                    <?php foreach ($expertise_list as $tag): ?>
                                        <span class="expertise-tag"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="social-links mt-3">
                                <?php if ($member['linkedin']): ?>
                                    <a href="<?= sanitizeOutput($member['linkedin']) ?>" class="social-link" target="_blank">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($member['email']): ?>
                                    <a href="mailto:<?= sanitizeOutput($member['email']) ?>" class="social-link">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Team Values -->
    <section class="section-padding bg-light">
        <div class="container">
            <h2 class="section-title">What Makes Our Team Special</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="value-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-award fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Expertise & Experience</h4>
                        <p class="small">Our team brings decades of combined experience in construction, architecture, and project management.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="value-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Collaborative Approach</h4>
                        <p class="small">We believe in teamwork and collaboration. Our integrated approach ensures seamless coordination.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="value-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-heart fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Client-Focused</h4>
                        <p class="small">Your satisfaction is our priority. We listen, guide, and exceed expectations.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="value-card p-4 bg-white rounded shadow-sm h-100 text-center">
                        <div class="value-icon text-warning mb-3">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold">Continuous Learning</h4>
                        <p class="small">We stay updated with the latest technologies, materials, and best practices.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Statistics -->
    <section class="section-padding">
        <div class="container">
            <h2 class="section-title">Our Team By The Numbers</h2>
            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-number">50+</div>
                    <div class="stat-label">Team Members</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">15+</div>
                    <div class="stat-label">Years Average Experience</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">25+</div>
                    <div class="stat-label">Professional Certifications</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Quality Commitment</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Join Our Team -->
    <!-- <section class="section-padding bg-light">
        <div class="container text-center">
            <h2 class="section-title">Join Our Growing Team</h2>
            <p class="lead mb-4">
                We're always looking for talented individuals who share our passion for excellence in construction.
            </p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Get in Touch</a>
                <a href="/constructioninnagpur/careers.php" class="btn btn-outline-dark">View Open Positions</a>
            </div>
        </div>
    </section>

  
    <section class="cta-section">
        <div class="container">
            <h2>Let’s Build Something Amazing Together</h2>
            <p>Ready to start your construction project? Our expert team is here to help.</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Contact Us</a>
                <a href="/constructioninnagpur/projects.php" class="btn btn-outline-light">View Our Work</a>
            </div>
        </div>
    </section> -->
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- BuildDream Theme Styles -->
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
        border-color: var(--primary-yellow);
        color: var(--charcoal);
        font-weight: 600;
        padding: 10px 25px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #e89a1f;
        border-color: #e89a1f;
        color: var(--charcoal);
    }

    .btn-outline-dark {
        border-color: var(--charcoal);
        color: var(--charcoal);
    }

    .btn-outline-dark:hover {
        background-color: var(--charcoal);
        color: var(--white);
    }

    .btn-outline-light {
        border-color: var(--white);
        color: var(--white);
    }

    .btn-outline-light:hover {
        background-color: var(--white);
        color: var(--charcoal);
    }

    .hero-section {
            background: linear-gradient(rgba(26, 26, 26, 0.7), rgba(26, 26, 26, 0.7)),
                        url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
            background-size: cover;
            color: var(--white);
            padding: 100px 0;
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

    /* Team Grid */
    .team-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .team-member {
        background-color: var(--white);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }

    .team-member:hover {
        transform: translateY(-10px);
    }

    .member-image {
        height: 300px;
        overflow: hidden;
    }

    .member-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .team-member:hover .member-image img {
        transform: scale(1.05);
    }

    .member-info {
        padding: 25px;
        text-align: center;
    }

    .member-name {
        font-size: 1.3rem;
        margin-bottom: 5px;
    }

    .member-role {
        color: var(--primary-yellow);
        font-weight: 600;
        margin-bottom: 15px;
    }

    .member-bio {
        color: #666;
        font-size: 0.95rem;
        margin-bottom: 15px;
    }

    .expertise-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
        margin-bottom: 15px;
    }

    .expertise-tag {
        background-color: var(--light-gray);
        color: var(--charcoal);
        font-size: 0.8rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
    }

    .social-links {
        display: flex;
        justify-content: center;
        gap: 12px;
    }

    .social-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background-color: var(--light-gray);
        color: var(--charcoal);
        border-radius: 50%;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background-color: var(--primary-yellow);
        color: var(--charcoal);
    }

    /* Stats */
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

    .stat-label {
        font-size: 1.1rem;
        color: var(--charcoal);
    }

    /* Value Cards */
    .value-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .value-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
    }

    /* CTA */
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