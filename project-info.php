<?php
/**
 * Project Info Page - Grand Jyothi Construction
 * Professional Single Project View with Gallery, Sidebar & Related Projects
 * BuildDream Theme: Yellow + Charcoal
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: projects.php");
    exit;
}

$project_id = (int)$_GET['id'];

// Fetch main project
$sql = "SELECT id, title, location, description, type, status, completed_on, size, duration, created_at
        FROM projects WHERE id = ?";
$stmt = executeQuery($sql, [$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    header("Location: projects.php");
    exit;
}

// Fetch gallery images
$imgStmt = executeQuery("SELECT image_path, caption FROM project_images WHERE project_id = ? ORDER BY id ASC", [$project_id]);
$images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch 3 related projects (same type, exclude current)
$relatedStmt = executeQuery("
    SELECT id, title, location, 
           (SELECT image_path FROM project_images WHERE project_id = p.id LIMIT 1) as thumb
    FROM projects p 
    WHERE type = ? AND id != ? AND status = 'completed'
    ORDER BY completed_on DESC LIMIT 3
", [$project['type'], $project_id]);
$related = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

$page_title = sanitizeOutput($project['title']) . " | Grand Jyothi Construction";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="container py-4">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="/constructioninnagpur/">Home</a></li>
        <li class="breadcrumb-item"><a href="/constructioninnagpur/projects.php">Projects</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= sanitizeOutput($project['title']) ?></li>
    </ol>
</nav>

<!-- Hero Header -->
<section class="project-hero">
    <div class="container">
        <div class="text-center">
            <h1 class="display-5 fw-bold mb-3"><?= sanitizeOutput($project['title']) ?></h1>
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-3">
                <p class="lead mb-0">
                    <i class="fas fa-map-marker-alt me-1"></i> <?= sanitizeOutput($project['location']) ?>
                </p>
                <span class="badge <?= $project['status'] === 'completed' ? 'bg-success' : ($project['status'] === 'current' ? 'bg-warning text-dark' : 'bg-info') ?> px-3 py-2">
                    <?= ucfirst($project['status']) ?> Project
                </span>
            </div>
        </div>
    </div>
</section>

<main class="section-padding bg-light">
    <div class="container">
        <div class="row g-5">
            <!-- Gallery Column -->
            <div class="col-lg-8">
                <div class="project-gallery">
                    <!-- Main Image -->
                    <?php if (!empty($images)): ?>
                        <div class="main-image mb-4 rounded-4 overflow-hidden shadow-lg position-relative">
                            <img id="mainProjectImage" 
                                 src="/constructioninnagpur/assets/images/projects/<?= htmlspecialchars($images[0]['image_path']) ?>"
                                 alt="<?= sanitizeOutput($project['title']) ?>"
                                 class="img-fluid w-100"
                                 style="height: 520px; object-fit: cover; cursor: zoom-in;">
                            <div class="image-zoom-indicator position-absolute top-0 end-0 m-3 bg-dark bg-opacity-50 text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-expand"></i>
                            </div>
                        </div>

                        <!-- Thumbnails -->
                        <?php if (count($images) > 1): ?>
                            <div class="thumbnails row g-3">
                                <?php foreach ($images as $index => $img): ?>
                                    <div class="col-3">
                                        <div class="thumbnail-wrapper position-relative">
                                            <img src="/assets/images/projects/<?= htmlspecialchars($img['image_path']) ?>"
                                                 alt="<?= htmlspecialchars($img['caption'] ?? '') ?>"
                                                 class="img-thumbnail thumb-img rounded-3 w-100"
                                                 style="height: 100px; object-fit: cover; cursor: pointer; border: <?= $index === 0 ? '3px solid var(--primary-yellow)' : '2px solid #e9ecef' ?>;"
                                                 onclick="changeMainImage(this.src, this)">
                                            <?php if ($img['caption']): ?>
                                                <div class="thumbnail-caption position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white text-center py-1 small">
                                                    <?= htmlspecialchars($img['caption']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="main-image mb-4 rounded-4 overflow-hidden shadow-lg position-relative">
                            <img src="https://via.placeholder.com/1200x600/1A1A1A/F9A826?text=<?= urlencode($project['title']) ?>"
                                 alt="No image" class="img-fluid w-100" style="height: 520px; object-fit: cover;">
                            <div class="placeholder-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-25">
                                <div class="text-center text-white">
                                    <i class="fas fa-image fa-3x mb-3"></i>
                                    <p class="mb-0">No project images available</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <div class="project-sidebar bg-white rounded-4 shadow-sm p-4 position-sticky" style="top: 100px;">
                    <h3 class="h4 fw-bold mb-4 text-charcoal border-bottom pb-3">Project Details</h3>
                    
                    <div class="info-item d-flex align-items-start mb-4">
                        <div class="icon me-3 text-yellow bg-yellow-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <strong class="d-block mb-1">Project Type</strong>
                            <p class="mb-0 text-capitalize text-muted"><?= ucfirst($project['type']) ?></p>
                        </div>
                    </div>

                    <div class="info-item d-flex align-items-start mb-4">
                        <div class="icon me-3 text-yellow bg-yellow-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <strong class="d-block mb-1">Status</strong>
                            <p class="mb-0">
                                <span class="badge <?= $project['status'] === 'completed' ? 'bg-success' : ($project['status'] === 'current' ? 'bg-warning text-dark' : 'bg-info') ?> px-3 py-2 rounded-pill">
                                    <?= ucfirst($project['status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <?php if ($project['size']): ?>
                    <div class="info-item d-flex align-items-start mb-4">
                        <div class="icon me-3 text-yellow bg-yellow-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-ruler-combined"></i>
                        </div>
                        <div>
                            <strong class="d-block mb-1">Project Size</strong>
                            <p class="mb-0 fw-bold text-muted"><?= sanitizeOutput($project['size']) ?> sq.ft</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($project['duration']): ?>
                    <div class="info-item d-flex align-items-start mb-4">
                        <div class="icon me-3 text-yellow bg-yellow-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="far fa-clock"></i>
                        </div>
                        <div>
                            <strong class="d-block mb-1">Duration</strong>
                            <p class="mb-0 text-muted"><?= sanitizeOutput($project['duration']) ?> months</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($project['completed_on']): ?>
                    <div class="info-item d-flex align-items-start mb-4">
                        <div class="icon me-3 text-yellow bg-yellow-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="far fa-calendar-check"></i>
                        </div>
                        <div>
                            <strong class="d-block mb-1">Completed</strong>
                            <p class="mb-0 text-muted"><?= date('F Y', strtotime($project['completed_on'])) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="info-item d-flex align-items-start mb-4">
                        <div class="icon me-3 text-yellow bg-yellow-soft rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <div>
                            <strong class="d-block mb-1">Started</strong>
                            <p class="mb-0 text-muted"><?= date('F Y', strtotime($project['created_at'])) ?></p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-grid gap-3">
                        <a href="/constructioninnagpur/contact.php" class="btn btn-primary btn-lg py-3 fw-semibold">
                            <i class="fas fa-phone-alt me-2"></i> Get Similar Quote
                        </a>
                        <a href="/constructioninnagpur/projects.php" class="btn btn-outline-dark py-3">
                            <i class="fas fa-arrow-left me-2"></i> Back to Projects
                        </a>
                    </div>

                    <hr class="my-4">

                    <div class="share-buttons">
                        <strong class="d-block mb-3">Share Project</strong>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="https://wa.me/?text=Check%20out%20this%20amazing%20project%20by%20Grand%20Jyothi:%20<?= urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"
                               target="_blank" class="btn btn-success btn-sm rounded-pill px-3">
                                <i class="fab fa-whatsapp me-1"></i> WhatsApp
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"
                               target="_blank" class="btn btn-primary btn-sm rounded-pill px-3">
                                <i class="fab fa-facebook-f me-1"></i> Facebook
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>"
                               target="_blank" class="btn btn-linkedin btn-sm rounded-pill px-3">
                                <i class="fab fa-linkedin-in me-1"></i> LinkedIn
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="bg-white rounded-4 shadow-sm p-5">
                    <h3 class="h4 fw-bold mb-4 border-bottom pb-3">Project Overview</h3>
                    <div class="project-description lead text-muted" style="line-height: 1.8;">
                        <?= nl2br(sanitizeOutput($project['description'] ?: 'A premium construction project showcasing excellence in design, quality, and timely delivery.')) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Projects -->
        <?php if (!empty($related)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="h4 fw-bold mb-4 border-bottom pb-3">Similar Projects</h3>
                <div class="row g-4">
                    <?php foreach ($related as $r): 
                        $rThumb = $r['thumb'] ? "/constructioninnagpur/assets/images/projects/{$r['thumb']}" : "https://via.placeholder.com/400x300";
                    ?>
                        <div class="col-md-4">
                            <a href="project-info.php?id=<?= $r['id'] ?>" class="text-decoration-none">
                                <div class="card h-100 shadow-sm hover-card border-0 overflow-hidden">
                                    <div class="card-img-wrapper position-relative overflow-hidden">
                                        <img src="<?= $rThumb ?>" class="card-img-top" alt="<?= sanitizeOutput($r['title']) ?>" style="height: 220px; object-fit: cover;">
                                        <div class="card-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-0 transition-all">
                                            <span class="text-white fw-semibold">View Details</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="card-title text-dark fw-bold"><?= sanitizeOutput($r['title']) ?></h6>
                                        <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt me-1"></i> <?= sanitizeOutput($r['location']) ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Lightbox + Gallery Script -->
<script>
function changeMainImage(src, element) {
    document.getElementById('mainProjectImage').src = src;
    document.querySelectorAll('.thumb-img').forEach(img => {
        img.style.border = '2px solid #e9ecef';
    });
    element.style.border = '3px solid #F9A826';
}

// Lightbox
document.getElementById('mainProjectImage')?.addEventListener('click', function() {
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox-overlay';
    lightbox.innerHTML = `
        <div class="lightbox-content">
            <img src="${this.src}" class="lightbox-img">
            <span class="lightbox-close">×</span>
        </div>
    `;
    document.body.appendChild(lightbox);
    lightbox.querySelector('.lightbox-close').onclick = () => lightbox.remove();
    lightbox.onclick = (e) => { if (e.target === lightbox) lightbox.remove(); };
});

// Add hover effects to cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.hover-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const overlay = this.querySelector('.card-overlay');
            if (overlay) {
                overlay.classList.remove('bg-opacity-0');
                overlay.classList.add('bg-opacity-70');
            }
        });
        card.addEventListener('mouseleave', function() {
            const overlay = this.querySelector('.card-overlay');
            if (overlay) {
                overlay.classList.remove('bg-opacity-70');
                overlay.classList.add('bg-opacity-0');
            }
        });
    });
});
</script>

<!-- Professional Styles -->
<style>
    :root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --text-yellow: #F9A826;
        --gray-light: #f8f9fa;
        --yellow-soft: rgba(249,168,38,0.1);
    }

    .project-hero {
        background: linear-gradient(135deg, var(--charcoal) 0%, #2d2d2d 100%);
        color: white;
        padding: 80px 0 60px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
    }
    
    .project-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="%23F9A826" opacity="0.03"><polygon points="0,0 100,0 50,100"/></svg>');
        background-size: 150px;
    }

    .project-sidebar {
        border-left: 4px solid var(--primary-yellow);
    }

    .bg-yellow-soft {
        background-color: var(--yellow-soft);
    }

    .hover-card {
        transition: all 0.4s ease;
        border-radius: 12px;
    }
    .hover-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15) !important;
    }

    .card-overlay {
        transition: all 0.3s ease;
        opacity: 0;
    }
    .hover-card:hover .card-overlay {
        opacity: 1;
    }

    .thumb-img {
        transition: all 0.3s ease;
    }
    .thumb-img:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .thumbnail-wrapper {
        overflow: hidden;
        border-radius: 12px;
    }

    .thumbnail-caption {
        font-size: 0.75rem;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .thumbnail-wrapper:hover .thumbnail-caption {
        opacity: 1;
    }

    .image-zoom-indicator {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .main-image:hover .image-zoom-indicator {
        opacity: 1;
    }

    .lightbox-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.95);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        cursor: zoom-out;
        animation: fadeIn 0.3s ease;
    }
    .lightbox-img {
        max-width: 95vw;
        max-height: 95vh;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.8);
        animation: zoomIn 0.3s ease;
    }
    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 3rem;
        color: white;
        cursor: pointer;
        font-weight: 300;
        transition: transform 0.2s ease;
    }
    .lightbox-close:hover {
        transform: scale(1.2);
    }

    .btn-linkedin {
        background: #0077b5 !important;
    }

    .project-description {
        font-size: 1.1rem;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes zoomIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    @media (max-width: 768px) {
        .project-hero { padding: 60px 0 40px; }
        .main-image img { height: 350px !important; }
        .thumbnails { justify-content: center; }
        .project-sidebar { position: static !important; }
    }
</style>