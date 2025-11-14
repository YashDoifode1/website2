<?php
/**
 * Admin Project Gallery Management
 * Allows adding, listing, and deleting images for each project
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';

requireAdmin();

$page_title = 'Manage Project Gallery';
$success_message = '';
$error_message = '';

$project_id = $_GET['project_id'] ?? null;
if (!$project_id) {
    header('Location: project.php');
    exit;
}

// Fetch project info
$stmt = executeQuery("SELECT title FROM projects WHERE id = ?", [$project_id]);
$project = $stmt->fetch();
if (!$project) {
    die("Invalid project ID.");
}

// Handle image delete
if (isset($_GET['delete_image'])) {
    $image_id = $_GET['delete_image'];
    try {
        $stmt = executeQuery("SELECT image_path FROM project_images WHERE id = ?", [$image_id]);
        $img = $stmt->fetch();
        if ($img) {
            deleteUploadedFile($img['image_path']);
            executeQuery("DELETE FROM project_images WHERE id = ?", [$image_id]);
            $success_message = 'Image deleted successfully.';
        }
    } catch (PDOException $e) {
        error_log('Delete Image Error: ' . $e->getMessage());
        $error_message = 'Error deleting image.';
    }
}

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $caption = trim($_POST['caption'] ?? '');
    $uploadResult = uploadImage($_FILES['image']);

    if ($uploadResult['success']) {
        try {
            executeQuery(
                "INSERT INTO project_images (project_id, image_path, caption) VALUES (?, ?, ?)",
                [$project_id, $uploadResult['filename'], $caption]
            );
            $success_message = 'Image added successfully!';
        } catch (PDOException $e) {
            error_log('Add Image Error: ' . $e->getMessage());
            $error_message = 'Error adding image.';
        }
    } else {
        $error_message = 'Upload failed: ' . $uploadResult['error'];
    }
}

// Fetch all gallery images
$images = executeQuery("SELECT * FROM project_images WHERE project_id = ? ORDER BY created_at DESC", [$project_id])->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Project Gallery</h1>
    <p>Manage gallery images for <strong><?= sanitizeOutput($project['title']) ?></strong></p>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success"><?= sanitizeOutput($success_message) ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error"><?= sanitizeOutput($error_message) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Add New Image</h2>
        <a href="project.php" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back to Projects</a>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Upload Image *</label>
                <input type="file" name="image" class="form-input" accept="image/*" required>
                <p class="form-help">Allowed: JPG, PNG, WebP (max 5MB)</p>
            </div>

            <div class="form-group">
                <label class="form-label">Caption (optional)</label>
                <input type="text" name="caption" class="form-input" placeholder="e.g., Site during construction">
            </div>
        </div>

        <div class="btn-group mt-3">
            <button type="submit" class="btn btn-primary"><i data-feather="upload"></i> Upload Image</button>
        </div>
    </form>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h2 class="card-title">Existing Gallery Images</h2>
    </div>

    <?php if (empty($images)): ?>
        <p>No images uploaded yet.</p>
    <?php else: ?>
        <div class="gallery-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
            <?php foreach ($images as $img): ?>
                <div class="gallery-item" style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.5rem; background: #fff;">
                    <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($img['image_path']) ?>" 
                         alt="<?= sanitizeOutput($img['caption'] ?? 'Project image') ?>" 
                         style="width: 100%; border-radius: 6px; object-fit: cover; aspect-ratio: 1/1;">
                    <p style="font-size: 0.875rem; color: #475569; margin: 0.5rem 0;">
                        <?= sanitizeOutput($img['caption'] ?? 'No caption') ?>
                    </p>
                    <a href="?project_id=<?= $project_id ?>&delete_image=<?= $img['id'] ?>" 
                       class="btn btn-danger btn-sm w-full" 
                       onclick="return confirm('Delete this image?');">Delete</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
