<?php
/**
 * Admin Services Management (With Image Uploads)
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$page_title = 'Manage Services';
$success_message = '';
$error_message = '';

$upload_dir = __DIR__ . '/../uploads/services/';
$upload_url = '/uploads/services/';

$action = $_GET['action'] ?? 'list';
$service_id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $service_id) {
    try {
        $stmt = executeQuery("SELECT cover_image, icon_image FROM services WHERE id = ?", [$service_id]);
        $img = $stmt->fetch();
        if ($img) {
            if (!empty($img['cover_image']) && file_exists(__DIR__ . '/..' . $img['cover_image'])) {
                unlink(__DIR__ . '/..' . $img['cover_image']);
            }
            if (!empty($img['icon_image']) && file_exists(__DIR__ . '/..' . $img['icon_image'])) {
                unlink(__DIR__ . '/..' . $img['icon_image']);
            }
        }

        executeQuery("DELETE FROM services WHERE id = ?", [$service_id]);
        $success_message = 'Service deleted successfully!';
        $action = 'list';
    } catch (PDOException $e) {
        error_log('Delete Service Error: ' . $e->getMessage());
        $error_message = 'Error deleting service.';
    }
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon        = trim($_POST['icon'] ?? 'tool');
    $slug        = trim($_POST['slug'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $author      = trim($_POST['author'] ?? 'Admin');

    // Image paths
    $cover_image = $service['cover_image'] ?? '';
    $icon_image  = $service['icon_image'] ?? '';

    // Auto-generate slug
    if (empty($slug) && !empty($title)) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
    }

    // Validate upload folder
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Handle Cover Image Upload
    if (!empty($_FILES['cover_image']['name'])) {
        $cover_name = time() . '_' . basename($_FILES['cover_image']['name']);
        $cover_target = $upload_dir . $cover_name;
        $cover_ext = strtolower(pathinfo($cover_target, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($cover_ext, $allowed_types) && $_FILES['cover_image']['size'] <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_target)) {
                $cover_image = $upload_url . $cover_name;
            } else {
                $error_message = 'Error uploading cover image.';
            }
        } else {
            $error_message = 'Invalid cover image file type or size (max 2MB).';
        }
    }

    // Handle Icon Image Upload
    if (!empty($_FILES['icon_image']['name'])) {
        $icon_name = time() . '_' . basename($_FILES['icon_image']['name']);
        $icon_target = $upload_dir . $icon_name;
        $icon_ext = strtolower(pathinfo($icon_target, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'svg', 'webp'];

        if (in_array($icon_ext, $allowed_types) && $_FILES['icon_image']['size'] <= 1 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['icon_image']['tmp_name'], $icon_target)) {
                $icon_image = $upload_url . $icon_name;
            } else {
                $error_message = 'Error uploading icon image.';
            }
        } else {
            $error_message = 'Invalid icon image file type or size (max 1MB).';
        }
    }

    if (empty($title) || empty($description)) {
        $error_message = 'Please fill in all required fields.';
    } elseif (empty($error_message)) {
        try {
            if ($action === 'edit' && $service_id) {
                $sql = "UPDATE services 
                        SET title = ?, description = ?, icon = ?, slug = ?, category = ?, author = ?, 
                            cover_image = ?, icon_image = ?
                        WHERE id = ?";
                executeQuery($sql, [$title, $description, $icon, $slug, $category, $author, $cover_image, $icon_image, $service_id]);
                $success_message = 'Service updated successfully!';
            } else {
                $sql = "INSERT INTO services (title, description, icon, slug, category, author, cover_image, icon_image, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                executeQuery($sql, [$title, $description, $icon, $slug, $category, $author, $cover_image, $icon_image]);
                $success_message = 'Service added successfully!';
            }
            $action = 'list';
        } catch (PDOException $e) {
            error_log('Save Service Error: ' . $e->getMessage());
            $error_message = 'Error saving service.';
        }
    }
}

// Fetch service for editing
$service = null;
if ($action === 'edit' && $service_id) {
    $stmt = executeQuery("SELECT * FROM services WHERE id = ?", [$service_id]);
    $service = $stmt->fetch();
    if (!$service) {
        $error_message = 'Service not found.';
        $action = 'list';
    }
}

// Fetch all services
$services = [];
if ($action === 'list') {
    $services = executeQuery("SELECT * FROM services ORDER BY created_at DESC")->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Services</h1>
    <p>Create and manage construction services offered</p>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success"><i data-feather="check-circle"></i> <?= sanitizeOutput($success_message) ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error"><i data-feather="alert-circle"></i> <?= sanitizeOutput($error_message) ?></div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <div class="card">
        <div class="card-header">
            <h2>All Services</h2>
            <a href="?action=add" class="btn btn-primary"><i data-feather="plus"></i> Add New Service</a>
        </div>

        <?php if (empty($services)): ?>
            <p>No services found. <a href="?action=add">Add your first service</a>!</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $svc): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($svc['icon_image'])): ?>
                                        <img src="<?= sanitizeOutput($svc['icon_image']) ?>" alt="" width="40">
                                    <?php else: ?>
                                        <i data-feather="<?= sanitizeOutput($svc['icon']) ?>"></i>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= sanitizeOutput($svc['title']) ?></strong></td>
                                <td><?= sanitizeOutput($svc['slug']) ?></td>
                                <td><?= sanitizeOutput($svc['category'] ?? '-') ?></td>
                                <td><?= sanitizeOutput($svc['author'] ?? 'Admin') ?></td>
                                <td><?= date('M d, Y', strtotime($svc['created_at'])) ?></td>
                                <td class="table-actions">
                                    <a href="?action=edit&id=<?= $svc['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="?action=delete&id=<?= $svc['id'] ?>" class="btn-delete" onclick="return confirm('Delete this service?')">Delete</a>
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
            <h2><?= $action === 'edit' ? 'Edit Service' : 'Add New Service' ?></h2>
            <a href="?action=list" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back</a>
        </div>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>Service Title *</label>
                <input type="text" name="title" class="form-input" value="<?= $service['title'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Slug (auto if blank)</label>
                <input type="text" name="slug" class="form-input" value="<?= $service['slug'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" rows="6" class="form-textarea" required><?= $service['description'] ?? '' ?></textarea>
            </div>

            <div class="form-group">
                <label>Cover Image</label>
                <?php if (!empty($service['cover_image'])): ?>
                    <img src="<?= sanitizeOutput($service['cover_image']) ?>" alt="cover" width="120"><br>
                <?php endif; ?>
                <input type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp">
                <small>Max 2MB. Allowed: jpg, jpeg, png, webp</small>
            </div>

            <div class="form-group">
                <label>Icon Image (optional)</label>
                <?php if (!empty($service['icon_image'])): ?>
                    <img src="<?= sanitizeOutput($service['icon_image']) ?>" alt="icon" width="60"><br>
                <?php endif; ?>
                <input type="file" name="icon_image" accept=".jpg,.jpeg,.png,.svg,.webp">
                <small>Max 1MB. Allowed: jpg, png, svg, webp</small>
            </div>

            <div class="form-group">
                <label>Icon Name (Feather icon fallback)</label>
                <input type="text" name="icon" class="form-input" value="<?= $service['icon'] ?? 'tool' ?>">
            </div>

            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" class="form-input" value="<?= $service['category'] ?? '' ?>">
            </div>

            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" class="form-input" value="<?= $service['author'] ?? 'Admin' ?>">
            </div>

            <div class="btn-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary"><i data-feather="save"></i> Save Service</button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
