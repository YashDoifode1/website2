<?php
/**
 * Blog Listing Page - Modern Design
 * 
 * Display all published blog articles with search, filters, and sidebar
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Blog & Insights | BuildDream Construction';

// Get filters
$category_filter = $_GET['category'] ?? '';
$search_term = $_GET['search'] ?? '';

// Build SQL query
$sql = "SELECT * FROM blog_articles WHERE is_published = 1";
$params = [];

if ($category_filter) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
}

if ($search_term) {
    $sql .= " AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ? OR tags LIKE ?)";
    $search_pattern = "%$search_term%";
    $params = array_merge($params, array_fill(0, 4, $search_pattern));
}

$sql .= " ORDER BY created_at DESC";

$articles = executeQuery($sql, $params)->fetchAll();

// Get categories with counts
$category_sql = "
    SELECT category, COUNT(*) as count 
    FROM blog_articles 
    WHERE is_published = 1 AND category IS NOT NULL AND category != '' 
    GROUP BY category 
    ORDER BY category
";
$categories = executeQuery($category_sql)->fetchAll();

// Get popular posts (latest 3)
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

        .btn-primary {
            background-color: var(--primary-yellow);
            border-color: var(--primary-yellow);
            color: var(--charcoal);
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #e89a1f;
            border-color: #e89a1f;
            color: var(--charcoal);
        }

        .navbar {
            background-color: var(--charcoal);
            padding: 15px 0;
        }

        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-yellow) !important;
        }

        .nav-link {
            color: var(--white) !important;
            font-weight: 500;
            margin: 0 10px;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-yellow) !important;
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
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .hero-section p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto 30px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 1rem;
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

        .blog-section {
            padding: 80px 0;
        }

        .blog-card {
            background-color: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            margin-bottom: 30px;
        }

        .blog-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .blog-card:hover .blog-title {
            color: var(--primary-yellow);
        }

        .blog-image {
            height: 220px;
            overflow: hidden;
            position: relative;
        }

        .blog-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .blog-card:hover .blog-image img {
            transform: scale(1.05);
        }

        .blog-category {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: var(--primary-yellow);
            color: var(--charcoal);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .blog-content {
            padding: 25px;
        }

        .blog-title {
            font-size: 1.3rem;
            margin-bottom: 15px;
            transition: color 0.3s ease;
        }

        .blog-title a {
            color: inherit;
            text-decoration: none;
        }

        .blog-excerpt {
            color: #666;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .blog-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #888;
            margin-bottom: 20px;
        }

        .blog-author, .blog-date {
            display: flex;
            align-items: center;
        }

        .blog-author i, .blog-date i {
            margin-right: 5px;
            color: var(--primary-yellow);
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

        .category-list {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 0;
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

        .category-list a:hover {
            color: var(--primary-yellow);
        }

        .category-count {
            background-color: var(--charcoal);
            color: var(--white);
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
        }

        .popular-post {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .popular-post:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .popular-post-image {
            width: 70px;
            height: 70px;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .popular-post-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .popular-post-content {
            flex: 1;
        }

        .popular-post-title {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .popular-post-title a {
            color: var(--charcoal);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .popular-post-title a:hover {
            color: var(--primary-yellow);
        }

        .popular-post-meta {
            font-size: 0.8rem;
            color: #888;
        }

        .no-posts {
            text-align: center;
            padding: 60px 0;
        }

        .no-posts-icon {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .pagination {
            justify-content: center;
            margin-top: 50px;
        }

        .page-link {
            color: var(--charcoal);
            border: 1px solid #ddd;
            padding: 10px 15px;
        }

        .page-link:hover {
            color: var(--charcoal);
            background-color: var(--light-gray);
            border-color: #ddd;
        }

        .page-item.active .page-link {
            background-color: var(--primary-yellow);
            border-color: var(--primary-yellow);
            color: var(--charcoal);
        }

        .lazy-load {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }

        .lazy-load.loaded {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 80px 0;
            }
            .hero-section h1 {
                font-size: 2.5rem;
            }
            .blog-section {
                padding: 50px 0;
            }
        }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1>Our Blog & Insights</h1>
            <p>Expert advice, construction tips, and industry insights to help you build your dream home with confidence.</p>
            <form action="" method="get" class="search-box position-relative d-inline-block" style="max-width: 500px; width: 100%;">
                <input type="text" name="search" placeholder="Search articles..." value="<?= sanitizeOutput($search_term) ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="blog-section">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-9">
                    <div class="row" id="blogPosts">
                        <?php if (empty($articles)): ?>
                            <div class="col-12">
                                <div class="no-posts">
                                    <div class="no-posts-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <h3>No Articles Found</h3>
                                    <p>Try adjusting your search or filter criteria.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($articles as $article): ?>
                                <div class="col-md-6 col-lg-4 lazy-load">
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
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Pagination (Placeholder - implement with LIMIT/OFFSET in real app) -->
                    <nav aria-label="Blog pagination">
                        <ul class="pagination">
                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-3">
                    <!-- Search -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Search Blog</h3>
                        <form action="" method="get" class="search-box position-relative">
                            <input type="text" name="search" placeholder="Search articles..." value="<?= sanitizeOutput($search_term) ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Categories</h3>
                        <ul class="category-list">
                            <li>
                                <a href="/constructioninnagpur/blog.php">
                                    <span>All Articles</span>
                                    <span class="category-count"><?= count($articles) ?></span>
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="?category=<?= urlencode($cat['category']) ?>" 
                                       class="<?= $category_filter === $cat['category'] ? 'text-primary fw-bold' : '' ?>">
                                        <span><?= sanitizeOutput($cat['category']) ?></span>
                                        <span class="category-count"><?= $cat['count'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Popular Posts -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Popular Posts</h3>
                        <ul class="popular-posts">
                            <?php foreach ($popular_posts as $post): ?>
                                <li class="popular-post">
                                    <?php if ($post['featured_image']): ?>
                                        <div class="popular-post-image">
                                            <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($post['featured_image']) ?>" 
                                                 alt="<?= sanitizeOutput($post['title']) ?>">
                                        </div>
                                    <?php endif; ?>
                                    <div class="popular-post-content">
                                        <h4 class="popular-post-title">
                                            <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($post['slug']) ?>">
                                                <?= sanitizeOutput($post['title']) ?>
                                            </a>
                                        </h4>
                                        <div class="popular-post-meta">
                                            <span><?= date('d M Y', strtotime($post['created_at'])) ?></span>
                                        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
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