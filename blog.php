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

// Fetch published articles
$sql = "SELECT * FROM blog_articles WHERE is_published = 1";
if ($category_filter) {
    $sql .= " AND category = ?";
    $articles = executeQuery($sql . " ORDER BY created_at DESC", [$category_filter])->fetchAll();
} else {
    $articles = executeQuery($sql . " ORDER BY created_at DESC")->fetchAll();
}

// Get all categories for filter
$categories = executeQuery("SELECT DISTINCT category FROM blog_articles WHERE is_published = 1 AND category IS NOT NULL AND category != '' ORDER BY category")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero">
    <div class="hero-content">
        <h1>Blog & News</h1>
        <p>Latest updates on construction, properties, and industry trends</p>
    </div>
</header>

<main class="container section">
    <!-- Category Filter -->
    <?php if (!empty($categories)): ?>
        <div style="margin-bottom: 2rem; text-align: center;">
            <a href="/constructioninnagpur/blog.php" 
               class="btn <?= empty($category_filter) ? 'btn-primary' : 'btn-secondary' ?>" 
               style="margin: 0.25rem;">
                All Articles
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="?category=<?= urlencode($cat['category']) ?>" 
                   class="btn <?= $category_filter === $cat['category'] ? 'btn-primary' : 'btn-secondary' ?>" 
                   style="margin: 0.25rem;">
                    <?= sanitizeOutput($cat['category']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <!-- Articles Grid -->
    <?php if (empty($articles)): ?>
        <div class="card" style="text-align: center; padding: 3rem;">
            <i data-feather="file-text" style="width: 64px; height: 64px; color: var(--text-gray); margin: 0 auto 1rem;"></i>
            <h2>No Articles Found</h2>
            <p>Check back soon for new updates!</p>
        </div>
    <?php else: ?>
        <div class="grid grid-3">
            <?php foreach ($articles as $article): ?>
                <article class="card blog-card">
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
                            <span><i data-feather="eye"></i> <?= sanitizeOutput($article['views']) ?> views</span>
                        </div>
                        
                        <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($article['slug']) ?>" 
                           class="btn btn-primary" 
                           style="width: 100%; margin-top: 1rem;">
                            Read More <i data-feather="arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
