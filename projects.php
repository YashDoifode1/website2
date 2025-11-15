<?php
/**
 * Projects Page - Grand Jyothi Construction
 * Filterable Grid + Mini 3-Image Slider per Project
 * Uses project_images only (NO image column in projects)
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

// Define base paths
$base_path = '/constructioninnagpur';
$assets_path = $base_path . '/assets/images';
$placeholder_main = 'https://via.placeholder.com/600x400/1A1A1A/F9A826?text=No+Image';
$placeholder_sidebar = 'https://via.placeholder.com/70x70/eeeeee/999999?text=NA';

$page_title = 'Our Projects | Grand Jyothi Construction';

// ---------------------------------------------------------------------
// Fetch all projects with first 3 gallery images
// ---------------------------------------------------------------------
$sql = "
    SELECT 
        p.id, p.title, p.location, p.type, p.status, p.completed_on, p.size, p.duration
    FROM projects p
    ORDER BY 
        CASE WHEN p.status = 'current' THEN 1
             WHEN p.status = 'future' THEN 2
             WHEN p.status = 'completed' THEN 3
        END, p.created_at DESC
";
$stmt = executeQuery($sql);
$all_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch up to 3 images for each project
$projects = [];
foreach ($all_projects as $p) {
    $imgs = executeQuery(
        "SELECT image_path FROM project_images WHERE project_id = ? ORDER BY `order` ASC, id ASC LIMIT 3",
        [$p['id']]
    )->fetchAll(PDO::FETCH_COLUMN);

    $p['gallery'] = $imgs;
    $projects[] = $p;
}

// Project types with counts
$types = executeQuery("
    SELECT type, COUNT(*) as count 
    FROM projects 
    WHERE type IS NOT NULL AND type != ''
    GROUP BY type 
    ORDER BY type
")->fetchAll();

$total_projects = count($projects);
$popular_projects = array_slice($projects, 0, 3);

require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
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

        .btn-custom-primary {
            background-color: var(--primary-yellow) !important;
            border-color: var(--primary-yellow) !important;
            color: var(--charcoal) !important;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-custom-primary:hover {
            background-color: #e89a1f !important;
            border-color: #e89a1f !important;
            color: var(--charcoal) !important;
        }

        .projects-banner {
            height: 500px;
            background: linear-gradient(rgba(26, 26, 26, 0.6), rgba(26, 26, 26, 0.6)),
                        url('https://images.unsplash.com/photo-1486325212027-8081e485255e?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') center center / cover no-repeat;
            display: flex;
            align-items: flex-end;
            padding: 60px 0;
            color: var(--white);
            position: relative;
        }

        .projects-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(249,168,38,0.1) 0%, transparent 70%);
        }

        .banner-content {
            max-width: 800px;
            position: relative;
            z-index: 2;
        }

        .banner-title {
            font-size: 3rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .banner-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 20px;
        }

        .breadcrumb-item a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--primary-yellow);
        }

        .projects-content-section {
            padding: 80px 0;
        }

        .filter-bar {
            background: var(--light-gray);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 40px;
        }

        .filter-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter-btn {
            background: var(--white);
            border: 2px solid var(--medium-gray);
            color: var(--charcoal);
            padding: 8px 16px;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-block;
            text-align: center;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: var(--primary-yellow);
            border-color: var(--primary-yellow);
            color: var(--charcoal);
        }

        .active-filters {
            font-size: 0.9rem;
            color: #666;
        }

        .btn-clear-filters {
            background: transparent;
            border: none;
            color: var(--primary-yellow);
            font-weight: 600;
            margin-left: 10px;
            cursor: pointer;
            text-decoration: underline;
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        .project-card {
            background: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
        }

        .project-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.12);
        }

        .project-image {
            height: 220px;
            position: relative;
            overflow: hidden;
        }

        .image-slider {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .slider-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            transition: opacity 0.4s ease;
        }

        .slider-dots {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 6px;
            z-index: 3;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: background 0.3s;
        }

        .dot.active {
            background: #fff;
        }

        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 4;
        }

        .project-content {
            padding: 20px;
        }

        .project-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .meta-tag {
            background: var(--light-gray);
            color: var(--charcoal);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
        }

        .project-title {
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: var(--charcoal);
        }

        .project-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .project-title a:hover {
            color: var(--primary-yellow);
        }

        .project-location {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .project-date {
            font-size: 0.85rem;
            color: #888;
            display: flex;
            align-items: center;
        }

        .sidebar {
            background-color: var(--light-gray);
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
        }

        .sidebar-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-yellow);
            display: inline-block;
        }

        .search-box {
            position: relative;
            margin-bottom: 30px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .search-box button {
            position: absolute;
            right: 5px;
            top: 5px;
            background: var(--primary-yellow);
            border: none;
            color: var(--charcoal);
            padding: 7px 15px;
            border-radius: 5px;
            font-weight: 600;
        }

        .category-list {
            list-style: none;
            padding: 0;
        }

        .category-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .category-list li:last-child {
            border-bottom: none;
        }

        .category-list a {
            color: var(--charcoal);
            text-decoration: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: color 0.3s ease;
        }

        .category-list a:hover,
        .category-list a.active {
            color: var(--primary-yellow);
            font-weight: 600;
        }

        .category-count {
            background: var(--charcoal);
            color: var(--white);
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
        }

        .popular-project {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .popular-project:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .popular-project-image {
            width: 70px;
            height: 70px;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .popular-project-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .popular-project-title a {
            color: var(--charcoal);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .popular-project-title a:hover {
            color: var(--primary-yellow);
        }

        .cta-section {
            background: linear-gradient(135deg, var(--charcoal) 0%, #2d2d2d 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
        }

        .cta-section h2 {
            color: white;
            margin-bottom: 1.5rem;
        }

        .btn-outline-light {
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
        }

        .btn-outline-light:hover {
            background: rgba(255,255,255,0.1);
            border-color: white;
        }

        @media (max-width: 768px) {
            .projects-banner {
                height: 400px;
                padding: 40px 0;
            }
            .banner-title {
                font-size: 2.2rem;
            }
            .projects-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<!-- Hero Banner -->
<section class="projects-banner">
    <div class="container">
        <div class="banner-content">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= $base_path ?>/">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Projects</li>
                </ol>
            </nav>
            <h1 class="banner-title">Our Construction Projects</h1>
            <p class="banner-subtitle">From vision to reality — Current, upcoming, and completed masterpieces</p>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="projects-content-section">
    <div class="container">
        <div class="row">
            <!-- Main Grid -->
            <div class="col-lg-8">
                <!-- Filter Bar -->
                <div class="filter-bar">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Filter by Type</h6>
                            <div class="filter-group">
                                <button class="filter-btn active" data-filter="all">All Projects</button>
                                <?php 
                                $allTypes = array_unique(array_filter(array_column($projects, 'type')));
                                foreach ($allTypes as $type): 
                                ?>
                                    <button class="filter-btn" data-filter="<?= strtolower($type) ?>">
                                        <?= ucfirst($type) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Filter by Status</h6>
                            <div class="filter-group">
                                <button class="filter-btn-status active" data-status="all">All Status</button>
                                <button class="filter-btn-status" data-status="current">Current</button>
                                <button class="filter-btn-status" data-status="future">Future</button>
                                <button class="filter-btn-status" data-status="completed">Completed</button>
                            </div>
                        </div>
                    </div>
                    <div class="active-filters mt-3" id="activeFilters">
                        <small class="text-muted">No active filters</small>
                    </div>
                </div>

                <!-- Projects Grid -->
                <div class="projects-grid" id="projectsGrid">
                    <?php if (empty($projects)): ?>
                        <div class="text-center py-5">
                            <h4 class="text-muted">No Projects Available</h4>
                            <p class="text-muted">We're updating our portfolio. Check back soon!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($projects as $p): 
                            $type = strtolower($p['type'] ?? 'residential');
                            $status = $p['status'] ?? 'completed';
                            $date = $p['completed_on'] ? date('F Y', strtotime($p['completed_on'])) : 'Planned';
                            $images = $p['gallery'];
                        ?>
                            <div class="project-card" 
                                 data-type="<?= $type ?>" 
                                 data-status="<?= $status ?>"
                                 data-project-id="<?= $p['id'] ?>">
                                <a href="project-info.php?id=<?= $p['id'] ?>" class="text-decoration-none">
                                    <div class="project-image">
                                        <div class="image-slider">
                                            <?php if (empty($images)): ?>
                                                <img src="<?= $placeholder_main ?>" alt="No Image" class="slider-img">
                                            <?php else: ?>
                                                <?php foreach ($images as $idx => $img_path): 
                                                    $src = $assets_path . '/' . $img_path;
                                                    $display = $idx === 0 ? 'block' : 'none';
                                                ?>
                                                    <img src="<?= $src ?>" 
                                                         alt="<?= sanitizeOutput($p['title']) ?> - Image <?= $idx + 1 ?>" 
                                                         class="slider-img"
                                                         style="display:<?= $display ?>;"
                                                         onerror="this.src='<?= $placeholder_main ?>'">
                                                <?php endforeach; ?>
                                            <?php endif; ?>

                                            <?php if (count($images) > 1): ?>
                                                <div class="slider-dots">
                                                    <?php for ($i = 0; $i < count($images); $i++): ?>
                                                        <span class="dot <?= $i === 0 ? 'active' : '' ?>"
                                                              onclick="showProjectSlide(<?= $p['id'] ?>, <?= $i ?>)"></span>
                                                    <?php endfor; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div class="status-badge">
                                            <span class="badge <?= $status === 'current' ? 'bg-success' : ($status === 'future' ? 'bg-warning text-dark' : 'bg-primary') ?>">
                                                <?= ucfirst($status) ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="project-content">
                                        <div class="project-meta">
                                            <span class="meta-tag"><?= ucfirst($type) ?></span>
                                            <?php if ($p['size']): ?>
                                                <span class="meta-tag"><?= sanitizeOutput($p['size']) ?> sq.ft</span>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="project-title">
                                            <?= sanitizeOutput($p['title']) ?>
                                        </h4>
                                        <div class="project-location">
                                            <?= sanitizeOutput($p['location']) ?>
                                        </div>
                                        <div class="project-date">
                                            <?= $date ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted" id="resultsCount">
                        Showing <span id="visibleCount"><?= count($projects) ?></span> of <?= count($projects) ?> projects
                    </p>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Search -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Search Projects</h3>
                    <form action="" method="get" class="search-box">
                        <input type="text" name="search" placeholder="Search projects..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                        <button type="submit">Search</button>
                    </form>
                </div>

                <!-- Project Types -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Project Types</h3>
                    <ul class="category-list">
                        <li><a href="?" class="<?= empty($_GET['type']) ? 'active' : '' ?>"><span>All Projects</span> <span class="category-count"><?= $total_projects ?></span></a></li>
                        <?php foreach ($types as $t): ?>
                            <li>
                                <a href="?type=<?= urlencode($t['type']) ?>" 
                                   class="<?= ($_GET['type'] ?? '') === $t['type'] ? 'active' : '' ?>">
                                    <span><?= ucfirst(sanitizeOutput($t['type'])) ?></span>
                                    <span class="category-count"><?= $t['count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Popular Projects -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Popular Projects</h3>
                    <?php foreach ($popular_projects as $p): 
                        $thumb = !empty($p['gallery']) ? $assets_path . '/' . $p['gallery'][0] : $placeholder_sidebar;
                    ?>
                        <div class="popular-project">
                            <div class="popular-project-image">
                                <img src="<?= $thumb ?>" alt="<?= sanitizeOutput($p['title']) ?>" onerror="this.src='<?= $placeholder_sidebar ?>'">
                            </div>
                            <div>
                                <div class="popular-project-title">
                                    <a href="project-info.php?id=<?= $p['id'] ?>"><?= sanitizeOutput($p['title']) ?></a>
                                </div>
                                <div class="text-muted small"><?= sanitizeOutput($p['location']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
                <h2 class="display-5 fw-bold mb-4">Ready to Build Your Dream?</h2>
                <p class="lead mb-4">Let's discuss your vision and create something extraordinary together</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= $base_path ?>/contact.php" class="btn btn-custom-primary btn-lg">
                        Get Free Consultation
                    </a>
                    <a href="<?= $base_path ?>/services.php" class="btn btn-outline-light btn-lg">
                        Our Services
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeBtns = document.querySelectorAll('.filter-btn');
    const statusBtns = document.querySelectorAll('.filter-btn-status');
    const cards = document.querySelectorAll('.project-card');
    const visibleCount = document.getElementById('visibleCount');
    const activeFilters = document.getElementById('activeFilters');

    let filterType = 'all';
    let filterStatus = 'all';

    function updateFilters() {
        let count = 0;
        const active = [];

        cards.forEach(card => {
            const type = card.dataset.type;
            const status = card.dataset.status;
            const matchType = filterType === 'all' || type === filterType;
            const matchStatus = filterStatus === 'all' || status === filterStatus;

            if (matchType && matchStatus) {
                card.style.display = 'block';
                count++;
            } else {
                card.style.display = 'none';
            }
        });

        visibleCount.textContent = count;

        if (filterType !== 'all') active.push(`Type: ${filterType.charAt(0).toUpperCase() + filterType.slice(1)}`);
        if (filterStatus !== 'all') active.push(`Status: ${filterStatus.charAt(0).toUpperCase() + filterStatus.slice(1)}`);

        activeFilters.innerHTML = active.length 
            ? `<strong>Active Filters:</strong> ${active.join(' • ')} <button class="btn-clear-filters" onclick="resetFilters()">Clear</button>`
            : '<small class="text-muted">No active filters</small>';
    }

    window.resetFilters = () => {
        filterType = 'all';
        filterStatus = 'all';
        typeBtns.forEach(b => b.classList.remove('active'));
        statusBtns.forEach(b => b.classList.remove('active'));
        document.querySelector('.filter-btn[data-filter="all"]').classList.add('active');
        document.querySelector('.filter-btn-status[data-status="all"]').classList.add('active');
        updateFilters();
    };

    typeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            typeBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterType = btn.dataset.filter;
            updateFilters();
        });
    });

    statusBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            statusBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterStatus = btn.dataset.status;
            updateFilters();
        });
    });

    updateFilters();
});

// Slider Function
function showProjectSlide(projectId, index) {
    const card = document.querySelector(`.project-card[data-project-id="${projectId}"]`);
    const imgs = card.querySelectorAll('.slider-img');
    const dots = card.querySelectorAll('.dot');

    imgs.forEach((img, i) => {
        img.style.display = i === index ? 'block' : 'none';
    });

    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
    });
}
</script>

</body>
</html>