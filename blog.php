<?php
/**
 * blog.php – Blog & Insights
 * Fully aligned, responsive, no DB errors
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Blog & Insights | Grand Jyothi Construction';

// ---------- 1. Filters ----------
$category_filter = trim($_GET['category'] ?? '');
$search_term     = trim($_GET['search'] ?? '');
$page            = max(1, (int)($_GET['page'] ?? 1));
$per_page        = 6;
$offset          = ($page - 1) * $per_page;

// ---------- 2. Build query ----------
$sql    = "SELECT id, title, excerpt, featured_image, category, author, created_at 
           FROM blog_articles 
           WHERE 1=1";
$params = [];

if ($category_filter !== '') {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
}
if ($search_term !== '') {
    $sql .= " AND (title LIKE ? OR excerpt LIKE ? OR content LIKE ?)";
    $pattern = "%$search_term%";
    $params = array_merge($params, [$pattern, $pattern, $pattern]);
}

$count_sql = "SELECT COUNT(*) FROM blog_articles WHERE 1=1" . substr($sql, strpos($sql, 'WHERE') + 5, strpos($sql, 'FROM') - strpos($sql, 'WHERE') - 5);
if (!empty($params)) {
    $count_stmt = executeQuery($count_sql, array_slice($params, 0, count($params) - (strpos($sql, 'LIKE') !== false ? 3 : 1)));
} else {
    $count_stmt = executeQuery($count_sql);
}
$total_articles = (int)$count_stmt->fetchColumn();
$total_pages = max(1, ceil($total_articles / $per_page));

$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
$params[] = $per_page;
$params[] = $offset;

$articles = executeQuery($sql, $params)->fetchAll();

// ---------- 3. Categories ----------
$categories = executeQuery("
    SELECT category, COUNT(*) as count 
    FROM blog_articles 
    WHERE category IS NOT NULL AND category != '' 
    GROUP BY category 
    ORDER BY category
")->fetchAll();

// ---------- 4. Popular posts ----------
$popular_posts = executeQuery("
    SELECT id, title, featured_image, created_at 
    FROM blog_articles 
    ORDER BY created_at DESC 
    LIMIT 3
")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitizeOutput($page_title) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    <style>
        :root{
            --primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;
            --light-gray:#f8f9fa;--medium-gray:#e9ecef;
        }
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);line-height:1.6;}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}

        /* ==== HERO ==== */
        .blog-banner{
            height:500px;background:linear-gradient(rgba(26,26,26,.7),rgba(26,26,26,.7)),
            url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
            center/cover no-repeat;display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;
        }
        .blog-banner::before{content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.15) 0%,transparent 70%);}
        .banner-title{font-size:3rem;margin:0;line-height:1.2;}
        .banner-subtitle{font-size:1.2rem;opacity:.9;margin-bottom:1.5rem;}

        /* ==== BREADCRUMB ==== */
        .breadcrumb{background:transparent;padding:0;margin-bottom:1rem;}
        .breadcrumb-item a{color:rgba(255,255,255,.8);text-decoration:none;}
        .breadcrumb-item.active{color:var(--primary-yellow);}

        /* ==== SEARCH ==== */
        .hero-search{max-width:500px;margin:0 auto;position:relative;}
        .hero-search input{width:100%;padding:14px 50px 14px 20px;border-radius:50px;border:none;font-size:1rem;}
        .hero-search button{position:absolute;right:8px;top:8px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);width:40px;height:40px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;font-size:1.1rem;}

        /* ==== BLOG SECTION ==== */
        .blog-section{padding:80px 0;background:var(--light-gray);}
        .section-title{font-size:1.8rem;margin-bottom:30px;padding-bottom:10px;
            border-bottom:3px solid var(--primary-yellow);display:inline-block;position:relative;}
        .section-title::after{content:'';position:absolute;bottom:-12px;left:0;
            width:60px;height:4px;background:var(--primary-yellow);border-radius:2px;}

        /* ==== GRID ==== */
        .blog-grid{
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
            gap:30px;
            align-items:stretch;
        }
        .blog-card{
            background:var(--white);border-radius:12px;overflow:hidden;
            box-shadow:0 6px 20px rgba(0,0,0,.06);transition:all .3s;
            display:flex;flex-direction:column;height:100%;
        }
        .blog-card:hover{transform:translateY(-8px);box-shadow:0 15px 30px rgba(0,0,0,.12);}
        .blog-image{height:200px;overflow:hidden;position:relative;}
        .blog-image img{width:100%;height:100%;object-fit:cover;transition:transform .5s;}
        .blog-card:hover .blog-image img{transform:scale(1.06);}
        .blog-category{
            position:absolute;top:12px;left:12px;background:var(--primary-yellow);
            color:var(--charcoal);padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600;
        }
        .blog-content{padding:22px;flex:1;display:flex;flex-direction:column;}
        .blog-title{font-size:1.25rem;margin:0 0 12px;line-height:1.3;}
        .blog-title a{color:inherit;text-decoration:none;transition:color .3s;}
        .blog-card:hover .blog-title a{color:var(--primary-yellow);}
        .blog-excerpt{color:#555;font-size:.94rem;margin-bottom:15px;flex:1;}
        .blog-meta{display:flex;justify-content:space-between;font-size:.85rem;color:#777;margin-bottom:12px;}
        .blog-author,.blog-date{display:flex;align-items:center;gap:4px;}
        .blog-author i,.blog-date i{color:var(--primary-yellow);}

        /* ==== SIDEBAR ==== */
        .sidebar{background:var(--white);border-radius:12px;padding:28px;
            box-shadow:0 6px 20px rgba(0,0,0,.05);margin-bottom:30px;}
        .sidebar-title{font-size:1.2rem;margin-bottom:18px;padding-bottom:8px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;}
        .search-box{position:relative;margin-bottom:25px;}
        .search-box input{width:100%;padding:12px 45px 12px 16px;border-radius:50px;border:1px solid #ddd;font-size:.95rem;}
        .search-box button{position:absolute;right:6px;top:6px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);width:36px;height:36px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;font-size:1rem;}
        .category-list{list-style:none;padding:0;margin:0;}
        .category-list a{display:flex;justify-content:space-between;align-items:center;
            padding:10px 0;color:var(--charcoal);text-decoration:none;border-bottom:1px solid #eee;font-size:.95rem;}
        .category-list a:hover,.category-list a.active{color:var(--primary-yellow);font-weight:600;}
        .category-count{background:var(--charcoal);color:var(--white);padding:2px 8px;
            border-radius:10px;font-size:.75rem;}
        .popular-post{display:flex;gap:12px;margin-bottom:18px;padding-bottom:18px;
            border-bottom:1px solid #eee;}
        .popular-post:last-child{margin-bottom:0;padding-bottom:0;border:none;}
        .popular-post-image{width:70px;height:70px;border-radius:8px;overflow:hidden;flex-shrink:0;background:#eee;}
        .popular-post-image img{width:100%;height:100%;object-fit:cover;}
        .popular-post-title a{color:var(--charcoal);font-weight:500;text-decoration:none;font-size:.95rem;line-height:1.3;}
        .popular-post-title a:hover{color:var(--primary-yellow);}
        .popular-post-meta{font-size:.8rem;color:#888;margin-top:4px;}

        /* ==== PAGINATION ==== */
        .pagination{justify-content:center;margin-top:50px;}
        .page-link{color:var(--charcoal);border:1px solid #ddd;padding:10px 16px;border-radius:6px;}
        .page-link:hover{background:var(--light-gray);border-color:#ddd;}
        .page-item.active .page-link{background:var(--primary-yellow);border-color:var(--primary-yellow);color:var(--charcoal);}

        /* ==== NO POSTS ==== */
        .no-posts{text-align:center;padding:60px 0;}
        .no-posts-icon{font-size:4rem;color:#ddd;margin-bottom:20px;}

        /* ==== FLOATING BUTTONS ==== */
        .floating-buttons{position:fixed;bottom:30px;right:30px;z-index:1000;display:flex;flex-direction:column;gap:12px;}
        .floating-btn{width:56px;height:56px;border-radius:50%;display:flex;
            align-items:center;justify-content:center;color:var(--white);
            font-size:1.4rem;box-shadow:0 6px 20px rgba(0,0,0,.2);transition:all .3s;}
        .floating-btn:hover{transform:translateY(-4px);box-shadow:0 10px 25px rgba(0,0,0,.3);}
        .whatsapp-btn{background:#25D366;}
        .call-btn{background:var(--primary-yellow);color:var(--charcoal);}

        /* ==== CTA ==== */
        .cta-section{background:linear-gradient(135deg,var(--charcoal) 0%,#2d2d2d 100%);
            color:var(--white);padding:80px 0;text-align:center;}
        .cta-section h2{color:var(--white);margin-bottom:1.5rem;}

        /* ==== RESPONSIVE ==== */
        @media (max-width:992px){
            .blog-banner{height:400px;padding:40px 0;}
            .banner-title{font-size:2.4rem;}
            .blog-section .row{flex-direction:column-reverse;}
            .blog-grid{grid-template-columns:1fr;}
        }
        @media (max-width:576px){
            .banner-title{font-size:2rem;}
            .floating-buttons{bottom:20px;right:20px;gap:10px;}
            .floating-btn{width:50px;height:50px;font-size:1.2rem;}
        }
    </style>
</head>
<body>

<!-- ====================== HERO ====================== -->
<section class="blog-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/constructioninnagpur/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Blog</li>
            </ol>
        </nav>
        <h1 class="banner-title">Our Blog & Insights</h1>
        <p class="banner-subtitle">Expert tips, trends, and construction advice for your dream home.</p>
        <form action="" method="get" class="hero-search">
            <input type="text" name="search" placeholder="Search articles..." value="<?= sanitizeOutput($search_term) ?>">
            <?php if ($category_filter): ?>
                <input type="hidden" name="category" value="<?= sanitizeOutput($category_filter) ?>">
            <?php endif; ?>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</section>

<!-- ====================== MAIN + SIDEBAR ====================== -->
<main class="blog-section">
    <div class="container">
        <div class="row g-5">

            <!-- ==== MAIN: Blog Grid ==== -->
            <div class="col-lg-9 order-lg-2">

                <div class="blog-grid" id="blogPosts">
                    <?php if (empty($articles)): ?>
                        <div class="no-posts">
                            <div class="no-posts-icon"><i class="fas fa-file-alt"></i></div>
                            <h3>No Articles Found</h3>
                            <p>Try adjusting your search or filter criteria.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                            <article class="blog-card">
                                <?php if ($article['featured_image']): ?>
                                    <div class="blog-image">
                                        <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($article['featured_image']) ?>"
                                             alt="<?= sanitizeOutput($article['title']) ?>"
                                             loading="lazy">
                                        <?php if ($article['category']): ?>
                                            <div class="blog-category"><?= sanitizeOutput($article['category']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="blog-content">
                                    <h3 class="blog-title">
                                        <a href="/constructioninnagpur/blog-detail.php?id=<?= (int)$article['id'] ?>">
                                            <?= sanitizeOutput($article['title']) ?>
                                        </a>
                                    </h3>
                                    <?php if ($article['excerpt']): ?>
                                        <p class="blog-excerpt"><?= sanitizeOutput($article['excerpt']) ?></p>
                                    <?php endif; ?>
                                    <div class="blog-meta">
                                        <div class="blog-author">
                                            <i class="fas fa-user"></i> <?= sanitizeOutput($article['author'] ?? 'Admin') ?>
                                        </div>
                                        <div class="blog-date">
                                            <i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($article['created_at'])) ?>
                                        </div>
                                    </div>
                                    <a href="/constructioninnagpur/blog-detail.php?id=<?= (int)$article['id'] ?>" 
                                       class="btn btn-primary btn-sm">Read More</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Blog pagination">
                    <ul class="pagination">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search_term) ?>&category=<?= urlencode($category_filter) ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search_term) ?>&category=<?= urlencode($category_filter) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search_term) ?>&category=<?= urlencode($category_filter) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>

            </div>

            <!-- ==== SIDEBAR ==== -->
            <aside class="col-lg-3 order-lg-1">
                <div class="sticky-top" style="top:90px;">

                    <!-- SEARCH -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Search Blog</h3>
                        <form action="" method="get" class="search-box">
                            <input type="text" name="search" placeholder="Search articles..." value="<?= sanitizeOutput($search_term) ?>">
                            <?php if ($category_filter): ?>
                                <input type="hidden" name="category" value="<?= sanitizeOutput($category_filter) ?>">
                            <?php endif; ?>
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- CATEGORIES -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Categories</h3>
                        <ul class="category-list">
                            <li><a href="/constructioninnagpur/blog.php" class="<?= empty($category_filter) ? 'active' : '' ?>">
                                <span>All Articles</span>
                                <span class="category-count"><?= $total_articles ?></span>
                            </a></li>
                            <?php foreach ($categories as $cat): ?>
                                <li><a href="?category=<?= urlencode($cat['category']) ?>&search=<?= urlencode($search_term) ?>"
                                       class="<?= $category_filter === $cat['category'] ? 'active' : '' ?>">
                                    <span><?= sanitizeOutput($cat['category']) ?></span>
                                    <span class="category-count"><?= $cat['count'] ?></span>
                                </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- POPULAR POSTS -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Popular Posts</h3>
                        <?php foreach ($popular_posts as $post): ?>
                            <div class="popular-post">
                                <?php if ($post['featured_image']): ?>
                                    <div class="popular-post-image">
                                        <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($post['featured_image']) ?>" 
                                             alt="<?= sanitizeOutput($post['title']) ?>">
                                    </div>
                                <?php endif; ?>
                                <div class="popular-post-content">
                                    <div class="popular-post-title">
                                        <a href="/constructioninnagpur/blog-detail.php?id=<?= (int)$post['id'] ?>">
                                            <?= sanitizeOutput($post['title']) ?>
                                        </a>
                                    </div>
                                    <div class="popular-post-meta">
                                        <?= date('d M Y', strtotime($post['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                </div>
            </aside>

        </div>
    </div>
</main>

<!-- ====================== FLOATING BUTTONS ====================== -->
<div class="floating-buttons">
    <a href="https://wa.me/919876543210" target="_blank" class="floating-btn whatsapp-btn" title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <a href="tel:+919876543210" class="floating-btn call-btn" title="Call Us">
        <i class="fas fa-phone"></i>
    </a>
</div>

<!-- ====================== CTA ====================== -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-4">Stay Updated with Construction Tips</h2>
                <p class="lead mb-4">Subscribe to our blog for the latest insights, trends, and expert advice.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="/constructioninnagpur/contact.php" class="btn btn-primary btn-lg">
                        Get Free Consultation
                    </a>
                    <a href="/constructioninnagpur/packages.php" class="btn btn-outline-light btn-lg">
                        View Packages
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

</body>
</html>