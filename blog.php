<?php
/**
 * Blog Listing Page
 * 
 * Display all published blog articles
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Blog & News';

// Get category filter
$category_filter = $_GET['category'] ?? '';
// Get search term
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

// Get all categories for filter
$categories = executeQuery("SELECT DISTINCT category FROM blog_articles WHERE is_published = 1 AND category IS NOT NULL AND category != '' ORDER BY category")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<style>/* Input Group Wrapper */
.input-group {
    display: flex;
    align-items: center;
    width: 100%;
    max-width: 420px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 12px;
    overflow: hidden;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}

/* Hover + Focus Effect */
.input-group:focus-within {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
}

/* Input Field */
.input-group input[type="text"] {
    flex: 1;
    padding: 12px 14px;
    border: none;
    font-size: 0.95rem;
    outline: none;
    background: #fff;
}

/* Button */
.input-group .btn.btn-primary {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 12px 16px;
    background: #0d6efd;
    border: none;
    color: #fff;
    font-weight: 500;
    cursor: pointer;
    outline: none;
    border-radius: 0;
    transition: background 0.2s ease;
}

/* Button Hover */
.input-group .btn.btn-primary:hover {
    background: #0b5ed7;
}

/* Icon */
.input-group i {
    height: 18px;
    width: 18px;
}
</style>
<!-- Hero Section -->
<header class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Blog & News</h1>
            <p class="lead">Latest updates on construction, properties, and industry trends</p>
            <form action="/constructioninnagpur/blog.php" method="get" class="search-form">
                <input type="text" name="search" placeholder="Search articles..." value="<?= sanitizeOutput($search_term) ?>">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="search"></i>
                </button>
            </form>
        </div>
    </div>
</header>

<main class="container">
    <section class="section">
        <!-- Category Filter -->
        <?php if (!empty($categories)): ?>
            <div class="category-filters">
                <a href="/constructioninnagpur/blog.php" 
                   class="filter-btn <?= empty($category_filter) ? 'active' : '' ?>">
                    All Articles
                </a>
                <?php foreach ($categories as $cat): ?>
                    <a href="?category=<?= urlencode($cat['category']) ?>" 
                       class="filter-btn <?= $category_filter === $cat['category'] ? 'active' : '' ?>">
                        <?= sanitizeOutput($cat['category']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Articles Grid -->
        <?php if (empty($articles)): ?>
            <article class="card text-center">
                <div class="no-results">
                    <i data-feather="file-text"></i>
                    <h3>No Articles Found</h3>
                    <p>Try adjusting your search or filter criteria</p>
                </div>
            </article>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($articles as $article): ?>
                    <article class="blog-card">
                        <?php if ($article['featured_image']): ?>
                            <div class="blog-image-wrapper">
                                <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($article['featured_image']) ?>" 
                                     alt="<?= sanitizeOutput($article['title']) ?>"
                                     class="blog-featured-image"
                                     loading="lazy"
                                     onerror="this.parentElement.style.display='none'">
                            </div>
                        <?php endif; ?>
                        
                        <div class="blog-card-content">
                            <?php if ($article['category']): ?>
                                <span class="blog-category"><?= sanitizeOutput($article['category']) ?></span>
                            <?php endif; ?>
                            
                            <h3 class="blog-title">
                                <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($article['slug']) ?>">
                                    <?= sanitizeOutput($article['title']) ?>
                                </a>
                            </h3>
                            
                            <?php if ($article['excerpt']): ?>
                                <p class="blog-excerpt"><?= sanitizeOutput($article['excerpt']) ?></p>
                            <?php endif; ?>
                            
                            <div class="blog-meta">
                                <span><i data-feather="calendar"></i> <?= date('M d, Y', strtotime($article['created_at'])) ?></span>
                                <span><i data-feather="user"></i> <?= sanitizeOutput($article['author']) ?></span>
                            </div>
                            
                            <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($article['slug']) ?>" class="read-more">
                                Read More <i data-feather="arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
