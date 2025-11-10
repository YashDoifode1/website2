<?php
/**
 * Projects Page - Grand Jyothi Construction
 * Filterable Portfolio with Lightbox Modal
 * BuildDream Theme: Yellow + Charcoal
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Our Projects | Grand Jyothi Construction';

// Fetch all projects
$sql = "SELECT id, title, location, description, image, type, completed_on, size, duration 
        FROM projects 
        ORDER BY completed_on DESC, created_at DESC";
$stmt = executeQuery($sql);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>Our Completed Projects</h1>
        <p>Explore our portfolio of excellence in residential, commercial, and industrial construction</p>
    </div>
</section>

<main>
    <!-- Projects Filter & Grid -->
    <section class="section-padding">
        <div class="container">
            <p class="text-center mb-5 lead text-muted">
                Explore our portfolio of successfully delivered construction projects showcasing our expertise, quality craftsmanship, and attention to detail.
            </p>
<br>
            <!-- Filter Buttons -->
            <div class="filter-buttons d-flex justify-content-center flex-wrap gap-2 mb-5">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <button class="filter-btn" data-filter="residential">Residential</button>
                <button class="filter-btn" data-filter="commercial">Commercial</button>
                <button class="filter-btn" data-filter="renovation">Renovation</button>
                <button class="filter-btn" data-filter="industrial">Industrial</button>
            </div>
<br>
            <!-- Projects Grid -->
            <div class="projects-grid" id="projectsContainer">
                <?php if (empty($projects)): ?>
                    <div class="text-center py-5">
                        <p class="text-muted">No projects available at the moment. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $p): 
                        $category = strtolower($p['type'] ?? 'residential');
                        $image = !empty($p['image']) ? "/constructioninnagpur/assets/images/{$p['image']}" : "https://via.placeholder.com/600x400?text=" . urlencode($p['title']);
                        $date = !empty($p['completed_on']) ? date('F Y', strtotime($p['completed_on'])) : 'N/A';
                    ?>
                        <div class="project-card" 
                             data-category="<?= $category ?>" 
                             data-bs-toggle="modal" 
                             data-bs-target="#projectModal" 
                             data-project-id="<?= $p['id'] ?>">
                            <div class="project-image">
                                <img src="<?= $image ?>" 
                                     alt="<?= sanitizeOutput($p['title']) ?>"
                                     onerror="this.src='https://via.placeholder.com/600x400?text=<?= urlencode($p['title']) ?>'">
                                <div class="project-overlay">
                                    <div class="project-info">
                                        <div class="project-title"><?= sanitizeOutput($p['title']) ?></div>
                                        <div class="project-location">
                                            <i class="fas fa-map-marker-alt"></i> <?= sanitizeOutput($p['location']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="project-content">
                                <div class="project-date">
                                    <i class="far fa-calendar-alt"></i> <?= $date ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <!-- <div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectModalLabel">Project Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img id="lightboxImage" class="img-fluid rounded mb-4" src="" alt="">
                    <div class="lightbox-details">
                        <h3 id="lightboxTitle" class="h4 fw-bold text-primary"></h3>
                        <div class="lightbox-meta d-flex flex-wrap gap-3 mb-3 text-muted">
                            <div id="lightboxLocation">
                                <i class="fas fa-map-marker-alt"></i> 
                            </div>
                            <div id="lightboxDate">
                                <i class="far fa-calendar-alt"></i> 
                            </div>
                        </div>
                        <p id="lightboxDescription" class="text-muted"></p>

                        <div id="lightboxStats" class="project-stats d-none mt-4">
                            <div class="stat-item text-center">
                                <span id="lightboxSize" class="stat-value d-block fw-bold text-primary"></span>
                                <span class="stat-label d-block text-muted">Size</span>
                            </div>
                            <div class="stat-item text-center">
                                <span id="lightboxDuration" class="stat-value d-block fw-bold text-primary"></span>
                                <span class="stat-label d-block text-muted">Duration</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </div>

    <!-- CTA Section -->
    <!-- <section class="cta-section">
        <div class="container">
            <h2>Ready to Start Your Project?</h2>
            <p>Let’s build something extraordinary together</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Get Free Quote</a>
                <a href="/constructioninnagpur/services.php" class="btn btn-outline-light">View Services</a>
            </div>
        </div>
    </section> -->
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Project Data & Scripts -->
<script>
    const projectsData = {
        <?php foreach ($projects as $p): ?>
        <?= $p['id'] ?>: {
            title: <?= json_encode($p['title']) ?>,
            location: <?= json_encode($p['location']) ?>,
            date: <?= json_encode(!empty($p['completed_on']) ? date('F Y', strtotime($p['completed_on'])) : '') ?>,
            image: <?= json_encode(!empty($p['image']) ? "/constructioninnagpur/assets/images/{$p['image']}" : '') ?>,
            description: <?= json_encode($p['description'] ?? '') ?>,
            size: <?= json_encode($p['size'] ?? '') ?>,
            duration: <?= json_encode($p['duration'] ?? '') ?>
        },
        <?php endforeach; ?>
    };

    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const projectCards = document.querySelectorAll('.project-card');

        // Filter functionality
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                filterButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                const filter = this.dataset.filter;
                projectCards.forEach(card => {
                    const cat = card.dataset.category;
                    card.style.display = (filter === 'all' || cat === filter) ? 'block' : 'none';
                });
            });
        });

        // Lightbox modal population
        const modal = document.getElementById('projectModal');
        modal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            const id = btn.dataset.projectId;
            const p = projectsData[id] || {};

            document.getElementById('lightboxTitle').textContent = p.title || 'Project';
            document.getElementById('lightboxImage').src = p.image || 'https://via.placeholder.com/800x400';
            document.getElementById('lightboxImage').alt = p.title || '';
            document.getElementById('lightboxLocation').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${p.location || 'Location not specified'}`;
            document.getElementById('lightboxDate').innerHTML = `<i class="far fa-calendar-alt"></i> Completed: ${p.date || 'N/A'}`;
            document.getElementById('lightboxDescription').textContent = p.description || 'No description available.';

            const stats = document.getElementById('lightboxStats');
            if (p.size || p.duration) {
                document.getElementById('lightboxSize').textContent = p.size ? `${p.size} sq.ft` : '-';
                document.getElementById('lightboxDuration').textContent = p.duration ? `${p.duration} months` : '-';
                stats.classList.remove('d-none');
            } else {
                stats.classList.add('d-none');
            }
        });
    });
</script>

<!-- BuildDream Theme Styles -->
<style>
    :root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --white: #FFFFFF;
        --light-gray: #f8f9fa;
        --medium-gray: #e9ecef;
        --primary: var(--primary-yellow);
        --text-muted: #666;
        --text-dark: var(--charcoal);
    }

    body {
        font-family: 'Roboto', sans-serif;
        color: var(--text-dark);
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
                    url('https://images.unsplash.com/photo-1486325212027-8081e485255e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
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

    /* Projects Grid */
    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .project-card {
        background-color: var(--white);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .project-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .project-image {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .project-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .project-card:hover .project-image img {
        transform: scale(1.08);
    }

    .project-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent 50%, rgba(26, 26, 26, 0.9));
        display: flex;
        align-items: flex-end;
        padding: 25px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .project-card:hover .project-overlay {
        opacity: 1;
    }

    .project-info {
        color: white;
    }

    .project-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .project-location {
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .project-content {
        padding: 20px;
    }

    .project-date {
        font-size: 0.9rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .project-date i {
        color: var(--primary-yellow);
    }

    /* Filter Buttons */
    .filter-btn {
        background-color: var(--white);
        color: var(--charcoal);
        border: 2px solid #ddd;
        padding: 10px 22px;
        border-radius: 30px;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .filter-btn:hover,
    .filter-btn.active {
        background-color: var(--primary-yellow);
        color: var(--charcoal);
        border-color: var(--primary-yellow);
        box-shadow: 0 4px 12px rgba(249, 168, 38, 0.3);
    }

    /* Modal */
    .modal-content {
        border-radius: 12px;
        border: none;
        overflow: hidden;
    }

    .modal-header {
        background-color: var(--charcoal);
        color: var(--white);
        border-bottom: none;
    }

    .modal-title {
        font-weight: 600;
    }

    .btn-close {
        filter: invert(1);
        opacity: 0.8;
    }

    .btn-close:hover {
        opacity: 1;
    }

    .lightbox-details h3 {
        color: var(--charcoal);
    }

    .lightbox-meta i {
        color: var(--primary-yellow);
    }

    .project-stats {
        display: flex;
        justify-content: center;
        gap: 40px;
        padding: 20px 0;
        border-top: 1px solid #eee;
    }

    .stat-value {
        font-size: 1.3rem;
    }

    .stat-label {
        font-size: 0.9rem;
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
        .filter-btn {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        .project-stats {
            flex-direction: column;
            gap: 15px;
        }
    }
</style>