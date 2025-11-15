<?php
/**
 * Project Info Page – Grand Jyothi Construction
 * Modern design 100% aligned with blog‑detail.php
 * Uses project_images only (NO image column in projects)
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

/* ---------- Helper ---------- */
function currentUrl(): string {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/* ---------- Validate ID ---------- */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: projects.php');
    exit;
}
$project_id = (int)$_GET['id'];

/* ---------- Main Project ---------- */
$sql = "SELECT id, title, location, description, type, status,
               completed_on, size, duration, created_at
        FROM projects WHERE id = ?";
$stmt = executeQuery($sql, [$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$project) {
    header('Location: projects.php');
    exit;
}

/* ---------- Gallery (ordered) ---------- */
$imgStmt = executeQuery(
    "SELECT image_path, caption FROM project_images WHERE project_id = ? ORDER BY `order` ASC, id ASC",
    [$project_id]
);
$images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- Paths ---------- */
$base_path = '/constructioninnagpur';
$assets_path = $base_path . '/assets/images';
$placeholder_main = 'https://via.placeholder.com/1200x520/1A1A1A/F9A826?text=No+Image';
$placeholder_thumb = 'https://via.placeholder.com/100/1A1A1A/F9A826?text=NA';

/* ---------- Related (same type) ---------- */
$related_projects = executeQuery(
    "SELECT p.id, p.title, p.location,
            (SELECT image_path FROM project_images WHERE project_id = p.id ORDER BY `order` ASC, id ASC LIMIT 1) AS thumb
     FROM projects p
     WHERE p.type = ? AND p.id != ? AND p.status = 'completed'
     ORDER BY p.completed_on DESC LIMIT 3",
    [$project['type'], $project_id]
)->fetchAll();

/* ---------- Prev / Next (same type) ---------- */
$prev_project = executeQuery(
    "SELECT id, title FROM projects
     WHERE completed_on < ? AND type = ?
     ORDER BY completed_on DESC LIMIT 1",
    [$project['completed_on'] ?? $project['created_at'], $project['type']]
)->fetch();

$next_project = executeQuery(
    "SELECT id, title FROM projects
     WHERE completed_on > ? AND type = ?
     ORDER BY completed_on ASC LIMIT 1",
    [$project['completed_on'] ?? $project['created_at'], $project['type']]
)->fetch();

/* ---------- Sidebar data ---------- */
/* Types + counts */
$types = executeQuery(
    "SELECT type, COUNT(*) AS count
     FROM projects
     WHERE type IS NOT NULL AND type <> ''
     GROUP BY type
     ORDER BY type"
)->fetchAll();

/* Total projects */
$total_projects = executeQuery("SELECT COUNT(*) FROM projects")->fetchColumn();

/* Popular projects (latest) */
$popular_projects = executeQuery(
    "SELECT p.id, p.title, p.location,
            (SELECT image_path FROM project_images WHERE project_id = p.id ORDER BY `order` ASC, id ASC LIMIT 1) AS thumb
     FROM projects p
     ORDER BY p.created_at DESC LIMIT 3"
)->fetchAll();

/* ---------- Page title ---------- */
$page_title = sanitizeOutput($project['title']) . ' | Grand Jyothi Construction';
require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>

    <!-- Bootstrap 5 + Font Awesome + Google Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;
            --light-gray:#f8f9fa;--medium-gray:#e9ecef;
        }
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);line-height:1.6;}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}

        /* ==== BUTTONS ==== */
        .btn-primary{background:var(--primary-yellow);border-color:var(--primary-yellow);color:var(--charcoal);font-weight:600;padding:10px 25px;border-radius:8px;}
        .btn-primary:hover{background:#e89a1f;border-color:#e89a1f;color:var(--charcoal);}

        /* ==== HERO ==== */
        .project-banner{
            height:500px;
            background:linear-gradient(rgba(26,26,26,.6),rgba(26,26,26,.6)),
                url('<?= !empty($images) ? $assets_path . '/' . $images[0]['image_path'] : $placeholder_main ?>') center/cover no-repeat;
            display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;
        }
        .project-banner::before{content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.1) 0%,transparent 70%);}
        .project-title{font-size:3rem;margin-bottom:20px;line-height:1.2;}
        .project-meta{display:flex;flex-wrap:wrap;gap:15px;align-items:center;}
        .badge-type,.badge-status{background:var(--primary-yellow);color:var(--charcoal);padding:5px 15px;border-radius:20px;font-size:.9rem;font-weight:600;}
        .badge-status{
            background:<?= $project['status']==='completed'?'#28a745':($project['status']==='current'?'#ffc107':'#17a2b8') ?>;
            color:<?= $project['status']==='current'?'var(--charcoal)':'var(--white)' ?>;
        }

        /* ==== CONTENT ==== */
        .project-content-section{padding:80px 0;}
        .project-content{font-size:1.1rem;line-height:1.8;}
        .project-content h2{font-size:1.8rem;margin:40px 0 20px;padding-bottom:10px;border-bottom:2px solid var(--primary-yellow);}
        .project-content img{max-width:100%;border-radius:8px;margin:30px 0;box-shadow:0 5px 15px rgba(0,0,0,.1);}

        /* ==== GALLERY ==== */
        .main-gallery-image{height:520px;object-fit:cover;border-radius:12px;cursor:zoom-in;transition:.4s;width:100%;}
        .main-gallery-image:hover{transform:scale(1.02);}
        .thumb-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:12px;margin-top:20px;}
        .thumb-img{height:100px;object-fit:cover;border-radius:8px;cursor:pointer;border:3px solid transparent;transition:.3s;}
        .thumb-img.active,.thumb-img:hover{border-color:var(--primary-yellow);transform:scale(1.05);box-shadow:0 5px 15px rgba(0,0,0,.2);}

        /* ==== SIDEBAR ==== */
        .sidebar{background:var(--light-gray);border-radius:10px;padding:30px;margin-bottom:30px;}
        .sidebar-title{font-size:1.2rem;margin-bottom:20px;padding-bottom:10px;border-bottom:2px solid var(--primary-yellow);display:inline-block;}
        .search-box{position:relative;margin-bottom:30px;}
        .search-box input{width:100%;padding:12px 15px;border-radius:5px;border:1px solid #ddd;}
        .search-box button{position:absolute;right:5px;top:5px;background:var(--primary-yellow);border:none;color:var(--charcoal);padding:7px 15px;border-radius:5px;font-weight:600;}
        .category-list{list-style:none;padding:0;}
        .category-list li{padding:10px 0;border-bottom:1px solid #eee;}
        .category-list li:last-child{border:none;}
        .category-list a{display:flex;justify-content:space-between;align-items:center;color:var(--charcoal);text-decoration:none;transition:.3s;}
        .category-list a:hover,.category-list a.active{color:var(--primary-yellow);font-weight:600;}
        .category-count{background:var(--charcoal);color:var(--white);padding:3px 8px;border-radius:10px;font-size:.8rem;}
        .popular-post{display:flex;margin-bottom:15px;padding-bottom:15px;border-bottom:1px solid #eee;}
        .popular-post:last-child{margin-bottom:0;padding-bottom:0;border:none;}
        .popular-post-image{width:70px;height:70px;border-radius:5px;overflow:hidden;margin-right:15px;flex-shrink:0;}
        .popular-post-image img{width:100%;height:100%;object-fit:cover;}
        .popular-post-title{font-size:.9rem;margin-bottom:5px;}
        .popular-post-title a{color:var(--charcoal);text-decoration:none;transition:.3s;}
        .popular-post-title a:hover{color:var(--primary-yellow);}

        /* ==== SHARE ==== */
        .social-share{display:flex;align-items:center;gap:10px;margin:40px 0;}
        .social-icon{width:40px;height:40px;border-radius:50%;background:var(--light-gray);color:var(--charcoal);
            display:flex;align-items:center;justify-content:center;transition:.3s;}
        .social-icon:hover{transform:translateY(-3px);}
        .whatsapp:hover{background:#25d366;color:#fff;}
        .facebook:hover{background:#3b5998;color:#fff;}
        .linkedin:hover{background:#0077b5;color:#fff;}

        /* ==== NAVIGATION ==== */
        .project-navigation{display:flex;justify-content:space-between;padding:40px 0;border-top:1px solid #eee;border-bottom:1px solid #eee;margin:40px 0;}
        .nav-project{max-width:45%;}
        .nav-project a{display:flex;align-items:center;text-decoration:none;color:var(--charcoal);transition:.3s;}
        .nav-project a:hover{color:var(--primary-yellow);}
        .nav-icon{font-size:1.5rem;margin:0 15px;}

        /* ==== RELATED ==== */
        .related-project-card{background:var(--white);border-radius:10px;overflow:hidden;box-shadow:0 5px 15px rgba(0,0,0,.05);transition:.3s;height:100%;}
        .related-project-card:hover{transform:translateY(-5px);box-shadow:0 10px 25px rgba(0,0,0,.1);}
        .related-project-image{height:200px;overflow:hidden;}
        .related-project-image img{width:100%;height:100%;object-fit:cover;transition:.5s;}
        .related-project-card:hover .related-project-image img{transform:scale(1.05);}
        .related-project-content{padding:20px;}
        .related-project-title a{color:var(--charcoal);text-decoration:none;transition:.3s;}
        .related-project-title a:hover{color:var(--primary-yellow);}

        /* ==== LIGHTBOX ==== */
        .lightbox-overlay{position:fixed;inset:0;background:rgba(0,0,0,.95);display:flex;justify-content:center;align-items:center;z-index:9999;cursor:zoom-out;animation:fadeIn .3s;}
        .lightbox-img{max-width:95vw;max-height:95vh;border-radius:12px;box-shadow:0 20px 50px rgba(0,0,0,.8);animation:zoomIn .3s;}
        .lightbox-close{position:absolute;top:20px;right:30px;font-size:3rem;color:#fff;cursor:pointer;font-weight:300;}
        .lightbox-close:hover{transform:scale(1.2);}
        @keyframes fadeIn{from{opacity:0}to{opacity:1}}
        @keyframes zoomIn{from{transform:scale(.9);opacity:0}to{transform:scale(1);opacity:1}}

        /* ==== RESPONSIVE ==== */
        @media (max-width:768px){
            .project-banner{height:400px;padding:40px 0;}
            .project-title{font-size:2.2rem;}
            .main-gallery-image{height:350px;}
            .project-navigation{flex-direction:column;}
            .nav-project{max-width:100%;margin-bottom:20px;}
            .nav-project.next a{flex-direction:row;}
        }
    </style>
</head>
<body>

<!-- ====================== HERO ====================== -->
<section class="project-banner">
    <div class="container">
        <div class="project-banner-content position-relative z-2">
            <h1 class="project-title"><?= sanitizeOutput($project['title']) ?></h1>
            <div class="project-meta">
                <div class="badge-type"><?= ucfirst(sanitizeOutput($project['type'])) ?></div>
                <div class="badge-status"><?= ucfirst($project['status']) ?> Project</div>
                <div class="meta-item"><i class="fas fa-map-marker-alt"></i> <?= sanitizeOutput($project['location']) ?></div>
            </div>
        </div>
    </div>
</section>

<!-- ====================== MAIN CONTENT ====================== -->
<section class="project-content-section">
    <div class="container">
        <div class="row">

            <!-- ==== LEFT COLUMN ==== -->
            <div class="col-lg-8">

                <!-- Gallery -->
                <div class="mb-5">
                    <?php if (!empty($images)): ?>
                        <img id="mainGalleryImage"
                             src="<?= $assets_path . '/' . $images[0]['image_path'] ?>"
                             alt="<?= sanitizeOutput($project['title']) ?>"
                             class="main-gallery-image"
                             onerror="this.src='<?= $placeholder_main ?>'">
                        <?php if (count($images) > 1): ?>
                            <div class="thumb-grid mt-4">
                                <?php foreach ($images as $i => $img): ?>
                                    <img src="<?= $assets_path . '/' . $img['image_path'] ?>"
                                         alt="<?= htmlspecialchars($img['caption'] ?? '') ?>"
                                         class="thumb-img <?= $i===0?'active':'' ?>"
                                         onclick="changeMainImage(this.src,this)"
                                         onerror="this.src='<?= $placeholder_thumb ?>'">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="<?= $placeholder_main ?>" class="main-gallery-image" alt="No image">
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div class="project-content mb-5">
                    <?= nl2br(sanitizeOutput($project['description'] ?: 'A premium construction project showcasing excellence in design, quality, and timely delivery.')) ?>
                </div>

                <!-- ==== SHARE ==== -->
                <div class="social-share">
                    <span class="fw-bold me-2">Share:</span>
                    <a href="https://wa.me/?text=Check%20out%20this%20project:%20<?= urlencode(currentUrl()) ?>"
                       target="_blank" class="social-icon whatsapp"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(currentUrl()) ?>"
                       target="_blank" class="social-icon facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(currentUrl()) ?>"
                       target="_blank" class="social-icon linkedin"><i class="fab fa-linkedin-in"></i></a>
                </div>

                <!-- ==== PREV / NEXT ==== -->
                <div class="project-navigation">
                    <?php if ($prev_project): ?>
                        <div class="nav-project prev">
                            <a href="project-info.php?id=<?= $prev_project['id'] ?>">
                                <div class="nav-icon"><i class="fas fa-arrow-left"></i></div>
                                <div>
                                    <div class="text-muted small">Previous Project</div>
                                    <div class="nav-project-title"><?= sanitizeOutput($prev_project['title']) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php else: ?><div></div><?php endif; ?>

                    <?php if ($next_project): ?>
                        <div class="nav-project next">
                            <a href="project-info.php?id=<?= $next_project['id'] ?>">
                                <div class="nav-icon"><i class="fas fa-arrow-right"></i></div>
                                <div>
                                    <div class="text-muted small">Next Project</div>
                                    <div class="nav-project-title"><?= sanitizeOutput($next_project['title']) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ==== RELATED PROJECTS ==== -->
                <?php if (!empty($related_projects)): ?>
                    <div class="related-projects mt-5">
                        <h3 class="section-title mb-4">Similar Projects</h3>
                        <div class="row g-4">
                            <?php foreach ($related_projects as $r):
                                $thumb = $r['thumb']
                                    ? $assets_path . '/' . $r['thumb']
                                    : $placeholder_main;
                            ?>
                                <div class="col-md-4">
                                    <a href="project-info.php?id=<?= $r['id'] ?>" class="text-decoration-none">
                                        <div class="related-project-card">
                                            <div class="related-project-image">
                                                <img src="<?= $thumb ?>" alt="<?= sanitizeOutput($r['title']) ?>" onerror="this.src='<?= $placeholder_main ?>'">
                                            </div>
                                            <div class="related-project-content">
                                                <h4 class="related-project-title">
                                                    <?= sanitizeOutput($r['title']) ?>
                                                </h4>
                                                <p class="text-muted small mb-0"><i class="fas fa-map-marker-alt me-1"></i> <?= sanitizeOutput($r['location']) ?></p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- ==== RIGHT SIDEBAR ==== -->
            <div class="col-lg-4">

                <!-- 1. SEARCH -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Search Projects</h3>
                    <form action="<?= $base_path ?>/projects.php" method="get" class="search-box">
                        <input type="text" name="search" placeholder="Search projects..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- 2. PROJECT DETAILS -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Project Details</h3>

                    <?php if ($project['size']): ?>
                        <div class="d-flex align-items-start mb-3">
                            <div class="detail-icon me-3"><i class="fas fa-ruler-combined"></i></div>
                            <div>
                                <div class="detail-label">Size</div>
                                <div class="detail-value fw-bold"><?= sanitizeOutput($project['size']) ?> sq.ft</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($project['duration']): ?>
                        <div class="d-flex align-items-start mb-3">
                            <div class="detail-icon me-3"><i class="far fa-clock"></i></div>
                            <div>
                                <div class="detail-label">Duration</div>
                                <div class="detail-value"><?= sanitizeOutput($project['duration']) ?> months</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($project['completed_on']): ?>
                        <div class="d-flex align-items-start mb-3">
                            <div class="detail-icon me-3"><i class="far fa-calendar-check"></i></div>
                            <div>
                                <div class="detail-label">Completed</div>
                                <div class="detail-value"><?= date('F Y', strtotime($project['completed_on'])) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="d-flex align-items-start mb-3">
                        <div class="detail-icon me-3"><i class="fas fa-hard-hat"></i></div>
                        <div>
                            <div class="detail-label">Started</div>
                            <div class="detail-value"><?= date('F Y', strtotime($project['created_at'])) ?></div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-grid gap-2">
                        <a href="<?= $base_path ?>/contact.php" class="btn btn-primary">
                            <i class="fas fa-phone-alt me-2"></i> Get Quote
                        </a>
                        <a href="<?= $base_path ?>/projects.php" class="btn btn-outline-dark">
                            <i class="fas fa-arrow-left me-2"></i> All Projects
                        </a>
                    </div>
                </div>

                <!-- 3. PROJECT TYPES -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Project Types</h3>
                    <ul class="category-list">
                        <li>
                            <a href="<?= $base_path ?>/projects.php" class="<?= empty($_GET['type']) ? 'active' : '' ?>">
                                <span>All Projects</span>
                                <span class="category-count"><?= $total_projects ?></span>
                            </a>
                        </li>
                        <?php foreach ($types as $t): ?>
                            <li>
                                <a href="<?= $base_path ?>/projects.php?type=<?= urlencode($t['type']) ?>"
                                   class="<?= ($_GET['type'] ?? '') === $t['type'] ? 'active' : '' ?>">
                                    <span><?= ucfirst(sanitizeOutput($t['type'])) ?></span>
                                    <span class="category-count"><?= $t['count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- 4. POPULAR PROJECTS -->
                <div class="sidebar">
                    <h3 class="sidebar-title">Popular Projects</h3>
                    <ul class="p-0 m-0">
                        <?php foreach ($popular_projects as $p):
                            $thumb = $p['thumb']
                                ? $assets_path . '/' . $p['thumb']
                                : $placeholder_thumb;
                        ?>
                            <li class="popular-post">
                                <div class="popular-post-image">
                                    <img src="<?= $thumb ?>" alt="<?= sanitizeOutput($p['title']) ?>" class="rounded" onerror="this.src='<?= $placeholder_thumb ?>'">
                                </div>
                                <div class="popular-post-content">
                                    <h4 class="popular-post-title">
                                        <a href="project-info.php?id=<?= $p['id'] ?>">
                                            <?= sanitizeOutput($p['title']) ?>
                                        </a>
                                    </h4>
                                    <div class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i> <?= sanitizeOutput($p['location']) ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
/* Gallery */
function changeMainImage(src, el){
    const main = document.getElementById('mainGalleryImage');
    main.src = src;
    document.querySelectorAll('.thumb-img').forEach(i=>i.classList.remove('active'));
    el.classList.add('active');
}

/* Lightbox */
document.getElementById('mainGalleryImage')?.addEventListener('click',function(){
    const lb=document.createElement('div');
    lb.className='lightbox-overlay';
    lb.innerHTML=`
        <div class="position-relative">
            <img src="${this.src}" class="lightbox-img">
            <span class="lightbox-close">×</span>
        </div>`;
    document.body.appendChild(lb);
    lb.querySelector('.lightbox-close').onclick=()=>lb.remove();
    lb.onclick=e=>{if(e.target===lb) lb.remove();}
});
</script>

</body>
</html>