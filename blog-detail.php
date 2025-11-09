<?php
/**
 * Blog Detail Page
 * 
 * Display a single blog article
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /constructioninnagpur/blog.php');
    exit;
}

// Fetch the article
$sql = "SELECT * FROM blog_articles WHERE slug = ? AND is_published = 1";
$article = executeQuery($sql, [$slug])->fetch();

if (empty($article)) {
    header('HTTP/1.0 404 Not Found');
    echo "Article not found";
    exit;
}

$page_title = $article['title'];

// Increment view count
$update_sql = "UPDATE blog_articles SET views = views + 1 WHERE id = ?";
executeQuery($update_sql, [$article['id']]);

// Fetch related articles (by category)
$related_sql = "SELECT * FROM blog_articles WHERE category = ? AND id != ? AND is_published = 1 ORDER BY created_at DESC LIMIT 3";
$related_articles = executeQuery($related_sql, [$article['category'], $article['id']])->fetchAll();

// Fetch all categories
$categories = executeQuery("SELECT DISTINCT category FROM blog_articles WHERE is_published = 1 AND category IS NOT NULL AND category != '' ORDER BY category")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero">
    <div class="container">
        <div class="hero-content">
            <h1><?= sanitizeOutput($article['title']) ?></h1>
            <p class="lead"><?= sanitizeOutput($article['excerpt']) ?></p>
        </div>
    </div>
</header>

<main class="container">
    <div class="grid grid-3-7">
        <!-- Main Content -->
        <article class="blog-detail-content">
            <!-- Article Meta -->
            <div class="blog-meta">
                <div class="author-info">
                    <span><i data-feather="calendar"></i> <?= date('M d, Y', strtotime($article['created_at'])) ?></span>
                    <span><i data-feather="user"></i> <?= sanitizeOutput($article['author']) ?></span>
                    <span><i data-feather="folder"></i> 
                        <a href="/constructioninnagpur/blog.php?category=<?= urlencode($article['category']) ?>" class="category-link">
                            <?= sanitizeOutput($article['category']) ?>
                        </a>
                    </span>
                    <span><i data-feather="eye"></i> <?= sanitizeOutput($article['views'] + 1) ?> views</span>
                </div>
            </div>
            
            <!-- Featured Image -->
            <?php if ($article['featured_image']): ?>
                <div class="blog-featured-image">
                    <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($article['featured_image']) ?>" 
                         alt="<?= sanitizeOutput($article['title']) ?>"
                         loading="lazy">
                </div>
            <?php endif; ?>
            
            <!-- Article Content -->
            <div class="blog-content">
                <?= $article['content'] ?>
            </div>
            
            <!-- Tags -->
            <?php if ($article['tags']): ?>
                <div class="blog-tags">
                    <h4>Tags:</h4>
                    <?php 
                    $tags = explode(',', $article['tags']);
                    foreach ($tags as $tag): 
                        $tag = trim($tag);
                        if (!empty($tag)):
                    ?>
                        <a href="/constructioninnagpur/blog.php?search=<?= urlencode($tag) ?>" class="tag">
                            <?= sanitizeOutput($tag) ?>
                        </a>
                    <?php endif; endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Related Articles -->
            <?php if (!empty($related_articles)): ?>
                <div class="related-articles">
                    <h3>Related Articles</h3>
                    <div class="grid grid-3">
                        <?php foreach ($related_articles as $related): ?>
                            <article class="blog-card">
                                <?php if ($related['featured_image']): ?>
                                    <div class="blog-image-wrapper">
                                        <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($related['featured_image']) ?>" 
                                             alt="<?= sanitizeOutput($related['title']) ?>"
                                             class="blog-featured-image"
                                             loading="lazy"
                                             onerror="this.parentElement.style.display='none'">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="blog-card-content">
                                    <span class="blog-category"><?= sanitizeOutput($related['category']) ?></span>
                                    
                                    <h4 class="blog-title">
                                        <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($related['slug']) ?>">
                                            <?= sanitizeOutput($related['title']) ?>
                                        </a>
                                    </h4>
                                    
                                    <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($related['slug']) ?>" class="read-more">
                                        Read More <i data-feather="arrow-right"></i>
                                    </a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </article>
        
        <!-- Sidebar -->
        <aside class="blog-sidebar">
            <!-- Search Widget -->
            <div class="sidebar-widget">
                <h4>Search</h4>
                <form action="/constructioninnagpur/blog.php" method="get">
                    <div class="input-group">
                        <input type="text" name="search" placeholder="Search blog...">
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Categories Widget -->
            <div class="sidebar-widget">
                <h4>Categories</h4>
                <ul class="categories-list">
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="/constructioninnagpur/blog.php?category=<?= urlencode($cat['category']) ?>">
                                <?= sanitizeOutput($cat['category']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Popular Posts Widget -->
            <div class="sidebar-widget">
                <h4>Popular Posts</h4>
                <?php
                $popular_sql = "SELECT * FROM blog_articles WHERE is_published = 1 ORDER BY views DESC LIMIT 3";
                $popular_articles = executeQuery($popular_sql)->fetchAll();
                ?>
                <?php if (!empty($popular_articles)): ?>
                    <ul class="popular-posts">
                        <?php foreach ($popular_articles as $popular): ?>
                            <li>
                                <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($popular['slug']) ?>" class="popular-post">
                                    <div class="popular-post-image">
                                        <?php if ($popular['featured_image']): ?>
                                            <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($popular['featured_image']) ?>" 
                                                 alt="<?= sanitizeOutput($popular['title']) ?>"
                                                 loading="lazy">
                                        <?php else: ?>
                                            <div class="image-placeholder"></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="popular-post-content">
                                        <h5><?= sanitizeOutput($popular['title']) ?></h5>
                                        <span><?= date('M d, Y', strtotime($popular['created_at'])) ?></span>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
