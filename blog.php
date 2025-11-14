<?php
/**
 * Blog Listing Page – Grand Jyothi Construction
 * 100% aligned with the rest of the site
 */

declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Blog & Insights | Grand Jyothi Construction';

// ---------- 1. Filters ----------
$category_filter = $_GET['category'] ?? '';
$search_term     = $_GET['search'] ?? '';

// ---------- 2. Build query ----------
$sql    = "SELECT * FROM blog_articles WHERE is_published = 1";
$params = [];

if ($category_filter) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
}
if ($search_term) {
    $sql .= " AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ? OR tags LIKE ?)";
    $pattern = "%$search_term%";
    $params = array_merge($params, array_fill(0, 4, $pattern));
}
$sql .= " ORDER BY created_at DESC";

$articles = executeQuery($sql, $params)->fetchAll();

// ---------- 3. Categories ----------
$category_sql = "
    SELECT category, COUNT(*) as count 
    FROM blog_articles 
    WHERE is_published = 1 AND category IS NOT NULL AND category != '' 
    GROUP BY category 
    ORDER BY category
";
$categories = executeQuery($category_sql)->fetchAll();

// ---------- 4. Popular posts ----------
$popular_posts = executeQuery("
    SELECT title, slug, featured_image, created_at 
    FROM blog_articles 
    WHERE is_published = 1 
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
    <title><?= $page_title ?></title>

    <!-- Bootstrap + Icons + Fonts -->
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
        .btn-primary{background:var(--primary-yellow);border-color:var(--primary-yellow);
            color:var(--charcoal);font-weight:600;padding:12px 30px;border-radius:8px;}
        .btn-primary:hover{background:#e89a1f;border-color:#e89a1f;color:var(--charcoal);}
        .btn-outline-light{border:2px solid rgba(255,255,255,.3);color:var(--white);
            padding:12px 30px;border-radius:30px;}
        .btn-outline-light:hover{background:rgba(255,255,255,.1);border-color:var(--white);}

        /* ==== HERO ==== */
        .blog-banner{height:500px;background:linear-gradient(rgba(26,26,26,.6),rgba(26,26,26,.6)),
            url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
            center/cover no-repeat;display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;}
        .blog-banner::before{content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.1) 0%,transparent 70%);}
        .banner-title{font-size:3rem;margin-bottom:20px;line-height:1.2;}
        .banner-subtitle{font-size:1.2rem;opacity:.9;}

        /* ==== BREADCRUMB ==== */
        .breadcrumb{background:transparent;padding:0;margin-bottom:20px;}
        .breadcrumb-item a{color:rgba(255,255,255,.8);text-decoration:none;}
        .breadcrumb-item.active{color:var(--primary-yellow);}

        /* ==== SEARCH IN HERO ==== */
        .hero-search{max-width:500px;margin:0 auto;position:relative;}
        .hero-search input{width:100%;padding:14px 50px 14px 20px;border-radius:50px;border:none;font-size:1rem;}
        .hero-search button{position:absolute;right:8px;top:8px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);width:40px;height:40px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;font-size:1.1rem;}

        /* ==== CONTENT ==== */
        .blog-section{padding:80px 0;}
        .section-title{font-size:1.8rem;margin-bottom:30px;padding-bottom:15px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;position:relative;}
        .section-title::after{content:'';position:absolute;bottom:-15px;left:0;
            width:80px;height:4px;background:var(--primary-yellow);}

        /* ==== BLOG GRID ==== */
        .blog-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:30px;}
        .blog-card{background:var(--white);border-radius:10px;overflow:hidden;
            box-shadow:0 5px 15px rgba(0,0,0,.05);transition:all .3s;height:100%;display:flex;flex-direction:column;}
        .blog-card:hover{transform:translateY(-10px);box-shadow:0 15px 30px rgba(0,0,0,.1);}
        .blog-image{height:220px;overflow:hidden;position:relative;}
        .blog-image img{width:100%;height:100%;object-fit:cover;transition:transform .5s;}
        .blog-card:hover .blog-image img{transform:scale(1.05);}
        .blog-category{position:absolute;top:15px;left:15px;background:var(--primary-yellow);
            color:var(--charcoal);padding:5px 15px;border-radius:20px;font-size:.8rem;font-weight:600;}
        .blog-content{padding:25px;flex:1;display:flex;flex-direction:column;}
        .blog-title{font-size:1.3rem;margin-bottom:15px;transition:color .3s;}
        .blog-title a{color:inherit;text-decoration:none;}
        .blog-card:hover .blog-title a{color:var(--primary-yellow);}
        .blog-excerpt{color:#666;margin-bottom:20px;font-size:.95rem;flex:1;}
        .blog-meta{display:flex;justify-content:space-between;align-items:center;
            font-size:.9rem;color:#888;margin-bottom:20px;}
        .blog-author,.blog-date{display:flex;align-items:center;}
        .blog-author i,.blog-date i{margin-right:5px;color:var(--primary-yellow);}

        /* ==== SIDEBAR ==== */
        .sidebar{background:var(--light-gray);border-radius:10px;padding:30px;margin-bottom:30px;}
        .sidebar-title{font-size:1.2rem;margin-bottom:20px;padding-bottom:10px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;}
        .search-box{position:relative;margin-bottom:30px;}
        .search-box input{width:100%;padding:12px 40px 12px 15px;border-radius:50px;border:1px solid #ddd;}
        .search-box button{position:absolute;right:8px;top:8px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);width:36px;height:36px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;}
        .category-list{list-style:none;padding:0;}
        .category-list a{display:flex;justify-content:space-between;align-items:center;
            padding:10px 0;color:var(--charcoal);text-decoration:none;border-bottom:1px solid #eee;}
        .category-list a:hover,.category-list a.active{color:var(--primary-yellow);font-weight:600;}
        .category-count{background:var(--charcoal);color:var(--white);padding:3px 8px;
            border-radius:10px;font-size:.8rem;}
        .popular-post{display:flex;gap:12px;margin-bottom:15px;padding-bottom:15px;
            border-bottom:1px solid #eee;}
        .popular-post:last-child{margin-bottom:0;padding-bottom:0;border:none;}
        .popular-post-image{width:70px;height:70px;border-radius:8px;overflow:hidden;flex-shrink:0;}
        .popular-post-image img{width:100%;height:100%;object-fit:cover;}
        .popular-post-title a{color:var(--charcoal);font-weight:500;text-decoration:none;}
        .popular-post-title a:hover{color:var(--primary-yellow);}
        .popular-post-meta{font-size:.8rem;color:#888;}

        /* ==== PAGINATION ==== */
        .pagination{justify-content:center;margin-top:50px;}
        .page-link{color:var(--charcoal);border:1px solid #ddd;padding:10px 15px;}
        .page-link:hover{color:var(--charcoal);background:var(--light-gray);border-color:#ddd;}
        .page-item.active .page-link{background:var(--primary-yellow);border-color:var(--primary-yellow);color:var(--charcoal);}

        /* ==== NO POSTS ==== */
        .no-posts{text-align:center;padding:60px 0;}
        .no-posts-icon{font-size:4rem;color:#ddd;margin-bottom:20px;}

        /* ==== FLOATING BUTTONS ==== */
        .floating-buttons{position:fixed;bottom:30px;right:30px;z-index:1000;}
        .floating-btn{width:60px;height:60px;border-radius:50%;display:flex;
            align-items:center;justify-content:center;color:var(--white);
            font-size:1.5rem;margin-bottom:15px;box-shadow:0 5px 15px rgba(0,0,0,.2);
            transition:all .3s;}
        .floating-btn:hover{transform:translateY(-5px);}
        .whatsapp-btn{background:#25D366;}
        .call-btn{background:var(--primary-yellow);color:var(--charcoal);}

        /* ==== CTA ==== */
        .cta-section{background:linear-gradient(135deg,var(--charcoal) 0%,#2d2d2d 100%);
            color:var(--white);padding:80px 0;text-align:center;}
        .cta-section h2{color:var(--white);margin-bottom:1.5rem;}

        /* ==== LAZY LOAD ==== */
        .lazy-load{opacity:0;transform:translateY(20px);transition:opacity .5s,transform .5s;}
        .lazy-load.loaded{opacity:1;transform:translateY(0);}

        /* ==== RESPONSIVE ==== */
        @media (max-width:992px){
            .blog-banner{height:400px;padding:40px 0;}
            .banner-title{font-size:2.2rem;}
            .blog-section .row{flex-direction:column-reverse;}
        }
        @media (max-width:576px){
            .floating-buttons{bottom:20px;right:20px;}
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
        </div>
        <h1 class="banner-title">Our Blog & Insights</h1>
        <p class="banner-subtitle">Expert advice, construction tips, and industry insights to help you build your dream home with confidence.</p>
        <form action="" method="get" class="hero-search">
            <input type="text" name="search" placeholder="Search articles..." value="<?= sanitizeOutput($search_term) ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</section>

<!-- ====================== MAIN + ASIDE ====================== -->
<main class="blog-section bg-light">
    <div class="container">
        <div class="row g-5">

            <!-- ==== MAIN: Blog Grid ==== -->
            <div class="col-lg-9">

                <div class="blog-grid" id="blogPosts">
                    <?php if (empty($articles)): ?>
                        <div class="no-posts">
                            <div class="no-posts-icon"><i class="fas fa-file-alt"></i></div>
                            <h3>No Articles Found</h3>
                            <p>Try adjusting your search or filter criteria.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                            <article class="blog-card lazy-load">
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
                                        <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($article['slug']) ?>">
                                            <?= sanitizeOutput($article['title']) ?>
                                        </a>
                                    </h3>
                                    <?php if ($article['excerpt']): ?>
                                        <p class="blog-excerpt"><?= sanitizeOutput($article['excerpt']) ?></p>
                                    <?php endif; ?>
                                    <div class="blog-meta">
                                        <div class="blog-author">
                                            <i class="fas fa-user"></i> <?= sanitizeOutput($article['author']) ?>
                                        </div>
                                        <div class="blog-date">
                                            <i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($article['created_at'])) ?>
                                        </div>
                                    </div>
                                    <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($article['slug']) ?>" 
                                       class="btn btn-primary">Read More</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination (implement LIMIT/OFFSET in real app) -->
                <nav aria-label="Blog pagination">
                    <ul class="pagination">
                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                    </ul>
                </nav>

            </div>

            <!-- ==== ASIDE: Sidebar ==== -->
            <aside class="col-lg-3">
                <div class="sticky-top" style="top:2rem;">

                    <!-- SEARCH -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Search Blog</h3>
                        <form action="" method="get" class="search-box">
                            <input type="text" name="search" placeholder="Search articles..." value="<?= sanitizeOutput($search_term) ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- CATEGORIES -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Categories</h3>
                        <ul class="category-list">
                            <li><a href="/constructioninnagpur/blog.php" class="<?= empty($category_filter) ? 'active' : '' ?>">
                                <span>All Articles</span>
                                <span class="category-count"><?= count($articles) ?></span>
                            </a></li>
                            <?php foreach ($categories as $cat): ?>
                                <li><a href="?category=<?= urlencode($cat['category']) ?>"
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
                                        <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($post['slug']) ?>">
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

<script>
    // Lazy loading animation
    document.addEventListener('DOMContentLoaded', function() {
        const lazyElements = document.querySelectorAll('.lazy-load');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('loaded');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        lazyElements.forEach(el => observer.observe(el));
    });
</script>

</body>
</html>