<?php
/**
 * Blog Detail Page - Modern Design (FINAL)
 * 
 * Fully updated with sidebar fixes, category counts, active state, and all features.
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

// Helper: Get current URL for social sharing
if (!function_exists('currentUrl')) {
    function currentUrl() {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
            . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
}

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /constructioninnagpur/blog.php');
    exit;
}

// Fetch the article with related count
$sql = "SELECT * FROM blog_articles WHERE slug = ? AND is_published = 1";
$article = executeQuery($sql, [$slug])->fetch();

if (!$article) {
    header('HTTP/1.0 404 Not Found');
    echo "<h1>404 - Article Not Found</h1>";
    exit;
}

$page_title = $article['title'] . ' | BuildDream Construction';

// Increment view count
executeQuery("UPDATE blog_articles SET views = views + 1 WHERE id = ?", [$article['id']]);
$article['views']++;

// Calculate reading time (~200 words per minute)
$word_count = str_word_count(strip_tags($article['content']));
$reading_time = max(1, ceil($word_count / 200));

// Related articles (same category)
$related_articles = executeQuery("
    SELECT title, slug, featured_image, created_at 
    FROM blog_articles 
    WHERE category = ? AND id != ? AND is_published = 1 
    ORDER BY created_at DESC 
    LIMIT 3
", [$article['category'], $article['id']])->fetchAll();

// Previous / Next posts (same category)
$prev_post = executeQuery("
    SELECT title, slug 
    FROM blog_articles 
    WHERE created_at < ? AND category = ? AND is_published = 1 
    ORDER BY created_at DESC 
    LIMIT 1
", [$article['created_at'], $article['category']])->fetch();

$next_post = executeQuery("
    SELECT title, slug 
    FROM blog_articles 
    WHERE created_at > ? AND category = ? AND is_published = 1 
    ORDER BY created_at ASC 
    LIMIT 1
", [$article['created_at'], $article['category']])->fetch();

// Categories with counts
$categories = executeQuery("
    SELECT category, COUNT(*) as count 
    FROM blog_articles 
    WHERE is_published = 1 AND category IS NOT NULL AND category != '' 
    GROUP BY category 
    ORDER BY category
")->fetchAll();

// Total published articles (for "All Articles")
$total_articles = executeQuery("SELECT COUNT(*) FROM blog_articles WHERE is_published = 1")->fetchColumn();

// Popular posts (by views)
$popular_posts = executeQuery("
    SELECT title, slug, featured_image, created_at 
    FROM blog_articles 
    WHERE is_published = 1 
    ORDER BY views DESC, created_at DESC 
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

        .blog-banner {
            height: 500px;
            background: linear-gradient(rgba(26, 26, 26, 0.4), rgba(26, 26, 26, 0.4)),
                        url('<?= $article['featured_image'] ? '/constructioninnagpur/assets/images/' . sanitizeOutput($article['featured_image']) : 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80' ?>') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: flex-end;
            padding: 60px 0;
            color: var(--white);
            position: relative;
        }

        .blog-banner-content {
            max-width: 800px;
        }

        .blog-title {
            font-size: 3rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .blog-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .blog-category {
            background-color: var(--primary-yellow);
            color: var(--charcoal);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .blog-author, .blog-date, .blog-views, .blog-reading-time {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .blog-author i, .blog-date i, .blog-views i, .blog-reading-time i {
            margin-right: 5px;
            color: var(--primary-yellow);
        }

        .blog-content-section {
            padding: 80px 0;
        }

        .blog-content {
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .blog-content h2 {
            font-size: 1.8rem;
            margin-top: 40px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-yellow);
        }

        .blog-content h3 {
            font-size: 1.5rem;
            margin-top: 30px;
            margin-bottom: 15px;
        }

        .blog-content p {
            margin-bottom: 20px;
        }

        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 30px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .blog-content ul, .blog-content ol {
            margin-bottom: 20px;
            padding-left: 20px;
        }

        .blog-content li {
            margin-bottom: 10px;
        }

        .blog-content blockquote {
            border-left: 4px solid var(--primary-yellow);
            padding: 20px;
            margin: 30px 0;
            font-style: italic;
            color: #666;
            background: #f9f9f9;
            border-radius: 8px;
        }

        .tags-section {
            padding: 30px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            margin: 40px 0;
        }

        .tag-badge {
            display: inline-block;
            background-color: var(--light-gray);
            color: var(--charcoal);
            padding: 8px 15px;
            border-radius: 20px;
            margin-right: 10px;
            margin-bottom: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .tag-badge:hover {
            background-color: var(--primary-yellow);
            color: var(--charcoal);
        }

        .social-share {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
        }

        .social-share span {
            font-weight: 600;
        }

        .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--light-gray);
            color: var(--charcoal);
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            transform: translateY(-3px);
        }

        .facebook:hover { background-color: #3b5998; color: white; }
        .twitter:hover { background-color: #1da1f2; color: white; }
        .linkedin:hover { background-color: #0077b5; color: white; }

        .blog-navigation {
            display: flex;
            justify-content: space-between;
            padding: 40px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            margin: 40px 0;
        }

        .nav-post {
            max-width: 45%;
        }

        .nav-post a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--charcoal);
            transition: color 0.3s ease;
        }

        .nav-post a:hover {
            color: var(--primary-yellow);
        }

        .nav-post.prev a { text-align: left; }
        .nav-post.next a { text-align: right; flex-direction: row-reverse; }

        .nav-icon {
            font-size: 1.5rem;
            margin: 0 15px;
        }

        .nav-post-title {
            font-weight: 600;
        }

        .section-title {
            font-size: 1.8rem;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-yellow);
            display: inline-block;
        }

        .related-post-card {
            background-color: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            height: 100%;
            margin-bottom: 30px;
        }

        .related-post-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .related-post-image {
            height: 200px;
            overflow: hidden;
        }

        .related-post-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .related-post-card:hover .related-post-image img {
            transform: scale(1.05);
        }

        .related-post-content {
            padding: 20px;
        }

        .related-post-title {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .related-post-title a {
            color: var(--charcoal);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .related-post-title a:hover {
            color: var(--primary-yellow);
        }

        .related-post-meta {
            font-size: 0.8rem;
            color: #888;
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
            list-style-type: none;
            padding-left: 0;
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
        .category-list a.text-primary {
            color: var(--primary-yellow) !important;
            font-weight: 600;
        }

        .category-count {
            background-color: var(--charcoal);
            color: var(--white);
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.8rem;
        }

        .popular-posts {
            list-style-type: none;
            padding-left: 0;
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

        .comments-section {
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid #eee;
        }

        .comment-form {
            background-color: var(--light-gray);
            border-radius: 10px;
            padding: 30px;
            margin-top: 30px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-yellow);
            box-shadow: 0 0 0 0.25rem rgba(249, 168, 38, 0.25);
        }

        @media (max-width: 768px) {
            .blog-banner {
                height: 400px;
                padding: 40px 0;
            }
            .blog-title {
                font-size: 2.2rem;
            }
            .blog-navigation {
                flex-direction: column;
            }
            .nav-post {
                max-width: 100%;
                margin-bottom: 20px;
            }
            .nav-post.next a {
                flex-direction: row;
            }
        }
    </style>
</head>
<body>

    <!-- Blog Banner -->
    <section class="blog-banner">
        <div class="container">
            <div class="blog-banner-content">
                <h1 class="blog-title"><?= sanitizeOutput($article['title']) ?></h1>
                <div class="blog-meta">
                    <div class="blog-category"><?= sanitizeOutput($article['category']) ?></div>
                    <div class="blog-author">
                        <i class="fas fa-user"></i> <?= sanitizeOutput($article['author']) ?>
                    </div>
                    <div class="blog-date">
                        <i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($article['created_at'])) ?>
                    </div>
                    <div class="blog-views">
                        <i class="fas fa-eye"></i> <?= number_format($article['views']) ?> Views
                    </div>
                    <div class="blog-reading-time">
                        <i class="fas fa-clock"></i> <?= $reading_time ?> min read
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Content -->
    <section class="blog-content-section">
        <div class="container">
            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <div class="blog-content">
                        <?= $article['content'] ?>
                    </div>

                    <!-- Tags -->
                    <?php if ($article['tags']): ?>
                        <div class="tags-section">
                            <strong>Tags: </strong>
                            <?php 
                            $tags = array_filter(array_map('trim', explode(',', $article['tags'])));
                            foreach ($tags as $tag): 
                            ?>
                                <a href="/constructioninnagpur/blog.php?search=<?= urlencode($tag) ?>" class="tag-badge">
                                    #<?= sanitizeOutput($tag) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Social Share -->
                    <div class="social-share">
                        <span>Share:</span>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(currentUrl()) ?>" 
                           target="_blank" class="social-icon facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(currentUrl()) ?>&text=<?= urlencode($article['title']) ?>" 
                           target="_blank" class="social-icon twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(currentUrl()) ?>" 
                           target="_blank" class="social-icon linkedin">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>

                    <!-- Prev/Next -->
                    <div class="blog-navigation">
                        <?php if ($prev_post): ?>
                            <div class="nav-post prev">
                                <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($prev_post['slug']) ?>">
                                    <div class="nav-icon"><i class="fas fa-arrow-left"></i></div>
                                    <div>
                                        <div class="text-muted">Previous</div>
                                        <div class="nav-post-title"><?= sanitizeOutput($prev_post['title']) ?></div>
                                    </div>
                                </a>
                            </div>
                        <?php else: ?><div></div><?php endif; ?>

                        <?php if ($next_post): ?>
                            <div class="nav-post next">
                                <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($next_post['slug']) ?>">
                                    <div class="nav-icon"><i class="fas fa-arrow-right"></i></div>
                                    <div>
                                        <div class="text-muted">Next</div>
                                        <div class="nav-post-title"><?= sanitizeOutput($next_post['title']) ?></div>
                                    </div>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Related Posts -->
                    <?php if (!empty($related_articles)): ?>
                        <div class="related-posts">
                            <h3 class="section-title">Related Articles</h3>
                            <div class="row">
                                <?php foreach ($related_articles as $related): ?>
                                    <div class="col-md-4">
                                        <div class="related-post-card">
                                            <?php if ($related['featured_image']): ?>
                                                <div class="related-post-image">
                                                    <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($related['featured_image']) ?>" 
                                                         alt="<?= sanitizeOutput($related['title']) ?>">
                                                </div>
                                            <?php endif; ?>
                                            <div class="related-post-content">
                                                <h4 class="related-post-title">
                                                    <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($related['slug']) ?>">
                                                        <?= sanitizeOutput($related['title']) ?>
                                                    </a>
                                                </h4>
                                                <div class="related-post-meta">
                                                    <span><?= date('d M Y', strtotime($related['created_at'])) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Comment Form -->
                    <div class="comments-section">
                        <h3 class="section-title">Leave a Comment</h3>
                        <div class="comment-form">
                            <form action="/constructioninnagpur/submit-comment.php" method="post">
                                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name *</label>
                                            <input type="text" class="form-control" id="name" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email *</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Comment *</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="5" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Post Comment</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Search -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Search Blog</h3>
                        <form action="/constructioninnagpur/blog.php" method="get" class="search-box position-relative">
                            <input type="text" name="search" placeholder="Search articles..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Categories</h3>
                        <ul class="category-list">
                            <li>
                                <a href="/constructioninnagpur/blog.php" class="<?= empty($_GET['category']) ? 'text-primary fw-bold' : '' ?>">
                                    <span>All Articles</span>
                                    <span class="category-count"><?= $total_articles ?></span>
                                </a>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="/constructioninnagpur/blog.php?category=<?= urlencode($cat['category']) ?>"
                                       class="<?= ($_GET['category'] ?? '') === $cat['category'] ? 'text-primary fw-bold' : '' ?>">
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
                                                 alt="<?= sanitizeOutput($post['title']) ?>" loading="lazy">
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
</body>
</html>