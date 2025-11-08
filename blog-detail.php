<?php
/**
 * Blog Detail Page
 * 
 * Display single blog article
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: /constructioninnagpur/blog.php');
    exit;
}

// Fetch article
$stmt = executeQuery("SELECT * FROM blog_articles WHERE slug = ? AND is_published = 1", [$slug]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: /constructioninnagpur/blog.php');
    exit;
}

// Increment view count
executeQuery("UPDATE blog_articles SET views = views + 1 WHERE id = ?", [$article['id']]);

// Fetch related articles (same category)
$related = [];
if ($article['category']) {
    $related = executeQuery(
        "SELECT * FROM blog_articles WHERE category = ? AND id != ? AND is_published = 1 ORDER BY created_at DESC LIMIT 3",
        [$article['category'], $article['id']]
    )->fetchAll();
}

$page_title = $article['title'];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="/constructioninnagpur/index.php">Home</a>
        <span class="separator">→</span>
        <a href="/constructioninnagpur/blog.php">Blog</a>
        <span class="separator">→</span>
        <span><?= sanitizeOutput($article['title']) ?></span>
    </div>
</div>

<main class="container section">
    <div class="grid grid-blog">
        <!-- Main Article -->
        <article class="blog-detail">
            <?php if ($article['featured_image']): ?>
                <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($article['featured_image']) ?>" 
                     alt="<?= sanitizeOutput($article['title']) ?>"
                     class="blog-detail-image"
                     style="background: var(--bg-light);"
                     onerror="this.style.display='none'">
            <?php endif; ?>
            
            <div class="blog-detail-header">
                <?php if ($article['category']): ?>
                    <span class="blog-category"><?= sanitizeOutput($article['category']) ?></span>
                <?php endif; ?>
                
                <h1><?= sanitizeOutput($article['title']) ?></h1>
                
                <div class="blog-meta" style="margin-top: 1rem;">
                    <span><i data-feather="calendar"></i> <?= date('F d, Y', strtotime($article['created_at'])) ?></span>
                    <span><i data-feather="user"></i> <?= sanitizeOutput($article['author']) ?></span>
                    <span><i data-feather="eye"></i> <?= sanitizeOutput($article['views']) ?> views</span>
                </div>
            </div>
            
            <div class="blog-content">
                <?= nl2br(sanitizeOutput($article['content'])) ?>
            </div>
            
            <?php if ($article['tags']): ?>
                <div class="blog-tags">
                    <strong>Tags:</strong>
                    <?php 
                    $tags = explode(',', $article['tags']);
                    foreach ($tags as $tag): 
                    ?>
                        <span class="tag"><?= sanitizeOutput(trim($tag)) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--border-color);">
                <a href="/constructioninnagpur/blog.php" class="btn btn-secondary">
                    <i data-feather="arrow-left"></i> Back to Blog
                </a>
            </div>
        </article>
        
        <!-- Sidebar -->
        <aside class="blog-sidebar">
            <!-- Categories -->
            <div class="card sidebar-card">
                <div class="sidebar-card-header">
                    <i data-feather="folder"></i>
                    <h3>Browse by Category</h3>
                </div>
                <div class="category-list">
                    <a href="/constructioninnagpur/blog.php" class="category-item <?= empty($article['category']) ? 'active' : '' ?>">
                        <span class="category-name">All Articles</span>
                        <span class="category-count">
                            <?php
                            $total = executeQuery("SELECT COUNT(*) as count FROM blog_articles WHERE is_published = 1")->fetch();
                            echo $total['count'];
                            ?>
                        </span>
                    </a>
                    <?php
                    $cats = executeQuery("SELECT category, COUNT(*) as count FROM blog_articles WHERE is_published = 1 AND category IS NOT NULL AND category != '' GROUP BY category ORDER BY category")->fetchAll();
                    foreach ($cats as $cat):
                    ?>
                        <a href="/constructioninnagpur/blog.php?category=<?= urlencode($cat['category']) ?>" 
                           class="category-item <?= $article['category'] === $cat['category'] ? 'active' : '' ?>">
                            <span class="category-name"><?= sanitizeOutput($cat['category']) ?></span>
                            <span class="category-count"><?= $cat['count'] ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Related Articles -->
            <?php if (!empty($related)): ?>
                <div class="card sidebar-card">
                    <div class="sidebar-card-header">
                        <i data-feather="book-open"></i>
                        <h3>Related Articles</h3>
                    </div>
                    <div class="related-articles">
                        <?php foreach ($related as $rel): ?>
                            <a href="/constructioninnagpur/blog-detail.php?slug=<?= sanitizeOutput($rel['slug']) ?>" class="related-article-item">
                                <h4><?= sanitizeOutput($rel['title']) ?></h4>
                                <div class="related-meta">
                                    <span><i data-feather="calendar"></i> <?= date('M d, Y', strtotime($rel['created_at'])) ?></span>
                                    <span><i data-feather="eye"></i> <?= sanitizeOutput($rel['views']) ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Contact CTA -->
            <div class="card sidebar-cta">
                <i data-feather="phone-call" style="width: 48px; height: 48px; color: white; margin-bottom: 1rem;"></i>
                <h3>Need Expert Advice?</h3>
                <p>Get professional consultation for your construction project</p>
                <a href="/constructioninnagpur/contact.php" class="btn btn-primary" style="background: white; color: var(--primary-blue); width: 100%;">
                    <i data-feather="message-circle"></i> Contact Us
                </a>
            </div>
        </aside>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
