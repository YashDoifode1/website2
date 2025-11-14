<?php
/**
 * Projects Page - Grand Jyothi Construction
 * Filterable Grid: All Projects (Current, Future, Completed)
 * Links to project-info.php?id=X for full gallery + details
 * BuildDream Theme: Yellow + Charcoal
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Our Projects | Grand Jyothi Construction';

// Fetch ALL projects + first gallery image (for thumbnail)
$sql = "
    SELECT 
        p.id, p.title, p.location, p.type, p.status, p.completed_on, p.size, p.duration,
        pi.image_path AS thumbnail
    FROM projects p
    LEFT JOIN (
        SELECT project_id, image_path 
        FROM project_images 
        WHERE project_id IS NOT NULL 
        ORDER BY id ASC LIMIT 1
    ) pi ON p.id = pi.project_id
    ORDER BY 
        CASE 
            WHEN p.status = 'current' THEN 1
            WHEN p.status = 'future' THEN 2
            WHEN p.status = 'completed' THEN 3
        END,
        p.created_at DESC
";

$stmt = executeQuery($sql);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <nav aria-label="breadcrumb" class="breadcrumb-nav mb-3">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="/constructioninnagpur/">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Projects</li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold mb-4">Our Construction Projects</h1>
                <p class="lead mb-0">From vision to reality — Current, upcoming, and completed masterpieces</p>
            </div>
        </div>
    </div>
</section>

<main class="main-content">
    <!-- Projects Section -->
    <section class="section-padding">
        <div class="container">
            <!-- Page Intro -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <p class="text-muted mb-4">
                        Explore our full portfolio — ongoing constructions, upcoming developments, and successfully completed landmarks.
                    </p>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="filter-container mb-5">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="row g-4 align-items-center">
                                    <div class="col-md-6">
                                        <h5 class="mb-3 text-charcoal">Filter by Type</h5>
                                        <div class="filter-group">
                                            <button class="filter-btn active" data-filter-type="all">
                                                <span>All Projects</span>
                                            </button>
                                            <button class="filter-btn" data-filter-type="residential">
                                                <i class="fas fa-home me-2"></i>Residential
                                            </button>
                                            <button class="filter-btn" data-filter-type="commercial">
                                                <i class="fas fa-building me-2"></i>Commercial
                                            </button>
                                            <button class="filter-btn" data-filter-type="renovation">
                                                <i class="fas fa-tools me-2"></i>Renovation
                                            </button>
                                            <button class="filter-btn" data-filter-type="industrial">
                                                <i class="fas fa-industry me-2"></i>Industrial
                                            </button>
                                            <button class="filter-btn" data-filter-type="infrastructure">
                                                <i class="fas fa-road me-2"></i>Infrastructure
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5 class="mb-3 text-charcoal">Filter by Status</h5>
                                        <div class="filter-group">
                                            <button class="filter-btn-status active" data-filter-status="all">
                                                <span>All Status</span>
                                            </button>
                                            <button class="filter-btn-status" data-filter-status="current">
                                                <span class="status-indicator bg-success"></span>
                                                Current
                                            </button>
                                            <button class="filter-btn-status" data-filter-status="future">
                                                <span class="status-indicator bg-warning"></span>
                                                Future
                                            </button>
                                            <button class="filter-btn-status" data-filter-status="completed">
                                                <span class="status-indicator bg-primary"></span>
                                                Completed
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Active Filters Display -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="active-filters" id="activeFilters">
                                            <small class="text-muted">No active filters</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projects Grid -->
            <div class="projects-grid" id="projectsGrid">
                <?php if (empty($projects)): ?>
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Projects Available</h4>
                            <p class="text-muted mb-4">We're currently updating our project portfolio. Please check back soon.</p>
                            <a href="/constructioninnagpur/contact.php" class="btn btn-primary">Start Your Project</a>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($projects as $p): 
                        $type = strtolower($p['type'] ?? 'residential');
                        $status = $p['status'] ?? 'completed';
                        $thumb = !empty($p['thumbnail']) 
                            ? "/constructioninnagpur/assets/images/projects/{$p['thumbnail']}" 
                            : "https://via.placeholder.com/600x400/1A1A1A/F9A826?text=" . urlencode($p['title']);
                        $date = $p['completed_on'] ? date('F Y', strtotime($p['completed_on'])) : 'Planned';
                        $statusBadge = [
                            'current' => '<span class="badge bg-success">Current Project</span>',
                            'future' => '<span class="badge bg-warning text-dark">Future Project</span>',
                            'completed' => '<span class="badge bg-primary">Completed</span>'
                        ];
                    ?>
                        <div class="project-card" 
                             data-type="<?= htmlspecialchars($type) ?>" 
                             data-status="<?= htmlspecialchars($status) ?>">
                            <a href="project-info.php?id=<?= $p['id'] ?>" class="project-card-link">
                                <div class="project-image">
                                    <img src="<?= htmlspecialchars($thumb) ?>" 
                                         alt="<?= sanitizeOutput($p['title']) ?>"
                                         onerror="this.src='https://via.placeholder.com/600x400/1A1A1A/F9A826?text=<?= urlencode($p['title']) ?>'"
                                         loading="lazy">
                                    <div class="project-overlay">
                                        <div class="project-actions">
                                            <span class="view-project-btn">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </span>
                                        </div>
                                    </div>
                                    <div class="status-badge">
                                        <?= $statusBadge[$status] ?? '<span class="badge bg-secondary">Unknown</span>' ?>
                                    </div>
                                </div>
                                <div class="project-content">
                                    <div class="project-meta">
                                        <span class="project-type">
                                            <i class="fas fa-tag me-1"></i><?= ucfirst($type) ?>
                                        </span>
                                        <?php if ($p['size']): ?>
                                        <span class="project-size">
                                            <i class="fas fa-ruler-combined me-1"></i><?= sanitizeOutput($p['size']) ?> sq.ft
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                    <h4 class="project-title"><?= sanitizeOutput($p['title']) ?></h4>
                                    <div class="project-location">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        <?= sanitizeOutput($p['location']) ?>
                                    </div>
                                    <div class="project-footer">
                                        <div class="project-date">
                                            <i class="far fa-calendar-alt me-1"></i> <?= $date ?>
                                        </div>
                                        <div class="project-arrow">
                                            <i class="fas fa-arrow-right"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Results Counter -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="results-counter text-center">
                        <p class="text-muted" id="resultsCount">
                            Showing <span id="visibleCount"><?= count($projects) ?></span> of <?= count($projects) ?> projects
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <div class="cta-content">
                        <h2 class="display-5 fw-bold mb-4">Ready to Build Your Dream?</h2>
                        <p class="lead mb-4">Let's discuss your vision and create something extraordinary together</p>
                        <div class="cta-buttons">
                            <a href="/constructioninnagpur/contact.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-phone-alt me-2"></i>Get Free Consultation
                            </a>
                            <a href="/constructioninnagpur/services.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-tools me-2"></i>Our Services
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Fixed Filtering Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeButtons = document.querySelectorAll('.filter-btn');
    const statusButtons = document.querySelectorAll('.filter-btn-status');
    const cards = document.querySelectorAll('.project-card');
    const resultsCount = document.getElementById('visibleCount');
    const activeFilters = document.getElementById('activeFilters');
    const totalProjects = <?= count($projects) ?>;
    let currentTypeFilter = 'all';
    let currentStatusFilter = 'all';

    function updateActiveFiltersDisplay() {
        const activeFiltersArray = [];
        
        if (currentTypeFilter !== 'all') {
            activeFiltersArray.push(`Type: ${currentTypeFilter.charAt(0).toUpperCase() + currentTypeFilter.slice(1)}`);
        }
        
        if (currentStatusFilter !== 'all') {
            activeFiltersArray.push(`Status: ${currentStatusFilter.charAt(0).toUpperCase() + currentStatusFilter.slice(1)}`);
        }
        
        if (activeFiltersArray.length > 0) {
            activeFilters.innerHTML = `<strong>Active Filters:</strong> ${activeFiltersArray.join(' • ')} 
                <button class="btn-clear-filters" onclick="clearAllFilters()">Clear All</button>`;
        } else {
            activeFilters.innerHTML = '<small class="text-muted">No active filters</small>';
        }
    }

    function clearAllFilters() {
        currentTypeFilter = 'all';
        currentStatusFilter = 'all';
        
        typeButtons.forEach(btn => btn.classList.remove('active'));
        statusButtons.forEach(btn => btn.classList.remove('active'));
        
        document.querySelector('.filter-btn[data-filter-type="all"]').classList.add('active');
        document.querySelector('.filter-btn-status[data-filter-status="all"]').classList.add('active');
        
        filterProjects();
    }

    function filterProjects() {
        let visibleCount = 0;
        
        cards.forEach(card => {
            const type = card.getAttribute('data-type');
            const status = card.getAttribute('data-status');
            
            const typeMatch = currentTypeFilter === 'all' || type === currentTypeFilter;
            const statusMatch = currentStatusFilter === 'all' || status === currentStatusFilter;
            
            if (typeMatch && statusMatch) {
                card.style.display = 'block';
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                // Staggered animation
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, visibleCount * 50);
                
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update results counter
        resultsCount.textContent = visibleCount;
        
        // Show empty state if no results
        const grid = document.getElementById('projectsGrid');
        if (visibleCount === 0 && cards.length > 0) {
            const emptyStateHTML = `
                <div class="empty-state-container" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <div class="empty-state">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Projects Found</h4>
                        <p class="text-muted mb-4">Try adjusting your filters to see more results.</p>
                        <button class="btn btn-primary" onclick="clearAllFilters()">Reset Filters</button>
                    </div>
                </div>
            `;
            
            // Check if empty state already exists
            if (!document.querySelector('.empty-state-container')) {
                grid.insertAdjacentHTML('beforeend', emptyStateHTML);
            }
        } else {
            // Remove empty state if it exists
            const emptyState = document.querySelector('.empty-state-container');
            if (emptyState) {
                emptyState.remove();
            }
        }
        
        updateActiveFiltersDisplay();
    }

    // Type filter event listeners
    typeButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            typeButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentTypeFilter = btn.getAttribute('data-filter-type');
            filterProjects();
        });
    });

    // Status filter event listeners
    statusButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            statusButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentStatusFilter = btn.getAttribute('data-filter-status');
            filterProjects();
        });
    });

    // Make clearAllFilters available globally
    window.clearAllFilters = clearAllFilters;

    // Initial filter
    filterProjects();
});
</script>

<!-- Updated CSS for Filter Fixes -->
<style>
    :root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --white: #FFFFFF;
        --gray-100: #f8f9fa;
        --gray-200: #e9ecef;
        --gray-600: #6c757d;
        --gray-800: #343a40;
        --border-radius: 12px;
        --box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        --transition: all 0.3s ease;
    }

    body { 
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; 
        color: var(--charcoal); 
        background: var(--white); 
        line-height: 1.6;
    }

    h1, h2, h3, h4, h5, h6 { 
        font-family: 'Poppins', sans-serif; 
        font-weight: 600;
        line-height: 1.3;
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, rgba(26,26,26,0.85) 0%, rgba(26,26,26,0.75) 100%),
                    url('https://images.unsplash.com/photo-1486325212027-8081e485255e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') center/cover;
        color: white; 
        padding: 100px 0 80px;
        position: relative;
    }

    .hero-section h1 {
        font-size: 3rem;
        margin-bottom: 1.5rem;
        background: linear-gradient(135deg, var(--primary-yellow) 0%, #FFC107 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .breadcrumb-nav .breadcrumb {
        background: transparent;
        padding: 0;
    }

    .breadcrumb-nav .breadcrumb-item a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
    }

    .breadcrumb-nav .breadcrumb-item.active {
        color: var(--primary-yellow);
    }

    /* Main Content */
    .section-padding {
        padding: 80px 0;
    }

    /* Filter Container */
    .filter-container .card {
        border-radius: var(--border-radius);
        background: var(--white);
    }

    .filter-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-btn, .filter-btn-status {
        background: var(--gray-100);
        border: 2px solid transparent;
        border-radius: 30px;
        padding: 10px 18px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: var(--transition);
        cursor: pointer;
        display: flex;
        align-items: center;
        color: var(--gray-800);
    }

    .filter-btn:hover, .filter-btn-status:hover {
        background: var(--primary-yellow);
        color: var(--charcoal);
        transform: translateY(-2px);
    }

    .filter-btn.active, .filter-btn-status.active {
        background: var(--primary-yellow);
        color: var(--charcoal);
        border-color: var(--primary-yellow);
        box-shadow: 0 4px 12px rgba(249, 168, 38, 0.3);
    }

    .status-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    /* Active Filters */
    .active-filters {
        padding: 12px 0;
        border-top: 1px solid var(--gray-200);
        margin-top: 16px;
    }

    .btn-clear-filters {
        background: var(--gray-200);
        border: none;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 0.8rem;
        margin-left: 12px;
        cursor: pointer;
        transition: var(--transition);
    }

    .btn-clear-filters:hover {
        background: var(--primary-yellow);
        color: var(--charcoal);
    }

    /* Projects Grid - FIXED */
    .projects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }

    .project-card {
        background: var(--white);
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        opacity: 1;
        transform: translateY(0);
    }

    .project-card.animate-in {
        animation: fadeInUp 0.6s ease forwards;
    }

    .project-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .project-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
    }

    .project-image {
        position: relative;
        height: 240px;
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
        background: linear-gradient(transparent 40%, rgba(26,26,26,0.9));
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding: 25px;
        opacity: 0;
        transition: var(--transition);
    }

    .project-card:hover .project-overlay {
        opacity: 1;
    }

    .view-project-btn {
        background: var(--primary-yellow);
        color: var(--charcoal);
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: var(--transition);
    }

    .status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
    }

    .badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 30px;
        font-weight: 600;
    }

    .project-content {
        padding: 24px;
    }

    .project-meta {
        display: flex;
        gap: 12px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .project-type, .project-size {
        font-size: 0.8rem;
        color: var(--gray-600);
        background: var(--gray-100);
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
    }

    .project-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--charcoal);
        line-height: 1.4;
    }

    .project-location {
        color: var(--gray-600);
        font-size: 0.9rem;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
    }

    .project-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid var(--gray-200);
    }

    .project-date {
        color: var(--gray-600);
        font-size: 0.85rem;
        display: flex;
        align-items: center;
    }

    .project-arrow {
        color: var(--primary-yellow);
        transition: var(--transition);
    }

    .project-card:hover .project-arrow {
        transform: translateX(4px);
    }

    /* Empty State */
    .empty-state {
        padding: 60px 20px;
    }

    /* Results Counter */
    .results-counter {
        font-size: 0.9rem;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, var(--charcoal) 0%, #2d2d2d 100%);
        color: white;
        padding: 80px 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23F9A826" opacity="0.03"><polygon points="0,0 100,0 50,100"/></svg>');
        background-size: 150px;
    }

    .cta-content h2 {
        color: white;
        margin-bottom: 1.5rem;
    }

    .btn-primary {
        background: var(--primary-yellow);
        border: none;
        color: var(--charcoal);
        font-weight: 600;
        padding: 12px 30px;
        border-radius: 30px;
        transition: var(--transition);
    }

    .btn-primary:hover {
        background: #e89a1f;
        color: var(--charcoal);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(249, 168, 38, 0.3);
    }

    .btn-outline-light {
        border: 2px solid rgba(255,255,255,0.3);
        color: white;
        font-weight: 500;
        padding: 12px 30px;
        border-radius: 30px;
        transition: var(--transition);
    }

    .btn-outline-light:hover {
        background: rgba(255,255,255,0.1);
        border-color: white;
        color: white;
        transform: translateY(-2px);
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-section {
            padding: 80px 0 60px;
        }
        
        .hero-section h1 {
            font-size: 2.25rem;
        }
        
        .projects-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .filter-group {
            justify-content: center;
        }
        
        .filter-btn, .filter-btn-status {
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        
        .cta-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .cta-buttons .btn {
            margin: 0 !important;
        }
    }

    @media (max-width: 576px) {
        .section-padding {
            padding: 60px 0;
        }
        
        .project-image {
            height: 200px;
        }
        
        .project-content {
            padding: 20px;
        }
    }
</style>