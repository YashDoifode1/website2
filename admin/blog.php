<?php
/**
 * Admin Blog Management
 * 
 * CRUD operations for blog articles
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';

requireAdmin();

$page_title = 'Manage Blog';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$article_id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $article_id) {
    try {
        // Get article to delete featured image
        $stmt = executeQuery("SELECT featured_image FROM blog_articles WHERE id = ?", [$article_id]);
        $article = $stmt->fetch();
        
        if ($article) {
            deleteUploadedFile($article['featured_image']);
        }
        
        executeQuery("DELETE FROM blog_articles WHERE id = ?", [$article_id]);
        $success_message = 'Article deleted successfully!';
        $action = 'list';
    } catch (PDOException $e) {
        error_log('Delete Article Error: ' . $e->getMessage());
        $error_message = 'Error deleting article.';
    }
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    $author = trim($_POST['author'] ?? 'Admin');
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    
    // Keep existing featured image for edit
    $featured_image = 'blog-default.jpg';
    if ($action === 'edit' && $article_id) {
        $stmt = executeQuery("SELECT featured_image FROM blog_articles WHERE id = ?", [$article_id]);
        $existing = $stmt->fetch();
        $featured_image = $existing['featured_image'] ?? 'blog-default.jpg';
    }
    
    // Auto-generate slug if empty
    if (empty($slug) && !empty($title)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }
    
    if (empty($title) || empty($content)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Handle featured image upload
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImage($_FILES['featured_image']);
            
            if ($uploadResult['success']) {
                // Delete old image if editing
                if ($action === 'edit' && $featured_image !== 'blog-default.jpg') {
                    deleteUploadedFile($featured_image);
                }
                $featured_image = $uploadResult['filename'];
            } else {
                $error_message = 'Image upload failed: ' . $uploadResult['error'];
            }
        }
        
        if (empty($error_message)) {
            try {
                if ($action === 'edit' && $article_id) {
                    $sql = "UPDATE blog_articles SET title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, category = ?, tags = ?, author = ?, is_published = ? WHERE id = ?";
                    executeQuery($sql, [$title, $slug, $excerpt, $content, $featured_image, $category, $tags, $author, $is_published, $article_id]);
                    $success_message = 'Article updated successfully!';
                } else {
                    $sql = "INSERT INTO blog_articles (title, slug, excerpt, content, featured_image, category, tags, author, is_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    executeQuery($sql, [$title, $slug, $excerpt, $content, $featured_image, $category, $tags, $author, $is_published]);
                    $success_message = 'Article added successfully!';
                }
                $action = 'list';
            } catch (PDOException $e) {
                error_log('Save Article Error: ' . $e->getMessage());
                $error_message = 'Error saving article. Slug might already exist.';
            }
        }
    }
}

// Fetch article for editing
$article = null;
if ($action === 'edit' && $article_id) {
    $stmt = executeQuery("SELECT * FROM blog_articles WHERE id = ?", [$article_id]);
    $article = $stmt->fetch();
    if (!$article) {
        $error_message = 'Article not found.';
        $action = 'list';
    }
}

// Fetch all articles for listing
$articles = [];
if ($action === 'list') {
    $articles = executeQuery("SELECT * FROM blog_articles ORDER BY created_at DESC")->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Blog</h1>
    <p>Create and manage blog articles about construction, properties, and updates</p>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success">
        <i data-feather="check-circle"></i>
        <?= sanitizeOutput($success_message) ?>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error">
        <i data-feather="alert-circle"></i>
        <?= sanitizeOutput($error_message) ?>
    </div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">All Articles (<?= count($articles) ?>)</h2>
            <a href="?action=add" class="btn btn-primary">
                <i data-feather="plus"></i> Add New Article
            </a>
        </div>
        
        <?php if (empty($articles)): ?>
            <p>No articles found. <a href="?action=add">Add your first article</a>!</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $art): ?>
                            <tr>
                                <td><strong><?= sanitizeOutput($art['title']) ?></strong></td>
                                <td><?= sanitizeOutput($art['category']) ?></td>
                                <td><?= sanitizeOutput($art['author']) ?></td>
                                <td><?= sanitizeOutput($art['views']) ?></td>
                                <td>
                                    <?php if ($art['is_published']): ?>
                                        <span style="color: var(--admin-success); font-weight: 600;">Published</span>
                                    <?php else: ?>
                                        <span style="color: var(--admin-text-gray);">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($art['created_at'])) ?></td>
                                <td class="table-actions">
                                    <a href="?action=edit&id=<?= $art['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="?action=delete&id=<?= $art['id'] ?>" class="btn-delete" onclick="return confirm('Delete this article?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    
<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?= $action === 'edit' ? 'Edit Article' : 'Add New Article' ?></h2>
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
        </div>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title" class="form-label">Article Title *</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       class="form-input"
                       value="<?= $article ? sanitizeOutput($article['title']) : '' ?>"
                       placeholder="e.g., Top 10 Construction Trends in 2024"
                       required>
            </div>
            
            <div class="form-group">
                <label for="slug" class="form-label">URL Slug</label>
                <input type="text" 
                       id="slug" 
                       name="slug" 
                       class="form-input"
                       value="<?= $article ? sanitizeOutput($article['slug']) : '' ?>"
                       placeholder="auto-generated-from-title">
                <p class="form-help">Leave empty to auto-generate from title</p>
            </div>
            
            <div class="form-group">
                <label for="excerpt" class="form-label">Excerpt (Short Description)</label>
                <textarea id="excerpt" 
                          name="excerpt" 
                          class="form-textarea"
                          rows="3"
                          placeholder="Brief summary of the article..."><?= $article ? sanitizeOutput($article['excerpt']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="content" class="form-label">Article Content *</label>
                <textarea id="content" 
                          name="content" 
                          class="form-textarea"
                          rows="15"
                          placeholder="Write your article content here..."
                          required><?= $article ? sanitizeOutput($article['content']) : '' ?></textarea>
                <p class="form-help">Use **text** for bold, line breaks for paragraphs</p>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="category" class="form-label">Category</label>
                    <input type="text" 
                           id="category" 
                           name="category" 
                           class="form-input"
                           value="<?= $article ? sanitizeOutput($article['category']) : '' ?>"
                           placeholder="e.g., Construction Tips">
                </div>
                
                <div class="form-group">
                    <label for="author" class="form-label">Author</label>
                    <input type="text" 
                           id="author" 
                           name="author" 
                           class="form-input"
                           value="<?= $article ? sanitizeOutput($article['author']) : 'Admin' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="tags" class="form-label">Tags (comma-separated)</label>
                <input type="text" 
                       id="tags" 
                       name="tags" 
                       class="form-input"
                       value="<?= $article ? sanitizeOutput($article['tags']) : '' ?>"
                       placeholder="construction, tips, 2024, technology">
            </div>
            
            <div class="form-group">
                <label for="featured_image" class="form-label">Featured Image</label>
                <input type="file" 
                       id="featured_image" 
                       name="featured_image" 
                       class="form-input"
                       accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                       onchange="previewBlogImage(this)">
                <p class="form-help">Upload JPG, PNG, GIF or WebP (Max: 5MB, Recommended: 1200x630px)</p>
                
                <?php if ($article && $article['featured_image'] && $article['featured_image'] !== 'blog-default.jpg'): ?>
                    <div class="current-image" style="margin-top: 1rem;">
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.5rem;">Current Image:</p>
                        <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($article['featured_image']) ?>" 
                             alt="Current" 
                             style="max-width: 300px; border-radius: 8px; border: 2px solid #e2e8f0;">
                    </div>
                <?php endif; ?>
                
                <div id="blogImagePreview" style="margin-top: 1rem; display: none;">
                    <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.5rem;">New Image Preview:</p>
                    <img id="blogPreview" src="" alt="Preview" style="max-width: 300px; border-radius: 8px; border: 2px solid #3b82f6;">
                </div>
            </div>
            
            <script>
            function previewBlogImage(input) {
                const preview = document.getElementById('blogPreview');
                const previewContainer = document.getElementById('blogImagePreview');
                
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        previewContainer.style.display = 'block';
                    };
                    
                    reader.readAsDataURL(input.files[0]);
                } else {
                    previewContainer.style.display = 'none';
                }
            }
            </script>
            
            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" 
                           name="is_published" 
                           <?= ($article && $article['is_published']) || !$article ? 'checked' : '' ?>
                           style="width: auto;">
                    <span>Publish this article</span>
                </label>
            </div>
            
            <div class="btn-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i> <?= $action === 'edit' ? 'Update Article' : 'Add Article' ?>
                </button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
