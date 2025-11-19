<?php
/**
 * Admin Services Management (With Image Uploads + Live Previews)
 * ------------------------------------------------
 * Works with the exact `services` table you posted:
 *   id, title, description, icon, created_at, slug,
 *   author, category, cover_image (default: placeholder.jpg),
 *   icon_image (new column – optional)
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config.php';

requireAdmin();

$page_title = 'Manage Services';
$success_message = '';
$error_message   = '';

$upload_dir = __DIR__ . '/../uploads/services/';
$upload_url = '/uploads/services/';

$action     = $_GET['action'] ?? 'list';
$service_id = $_GET['id'] ?? null;

/* -----------------------------------------------------------------
 * 1. DELETE SERVICE
 * ----------------------------------------------------------------- */
if ($action === 'delete' && $service_id) {
    try {
        $stmt = executeQuery(
            "SELECT cover_image, icon_image FROM services WHERE id = ?",
            [$service_id]
        );
        $svc = $stmt->fetch();

        if ($svc) {
            foreach (['cover_image', 'icon_image'] as $field) {
                $path = $svc[$field] ?? '';
                if ($path && file_exists(__DIR__ . '/../' . $path)) {
                    @unlink(__DIR__ . '/../' . $path);
                }
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

/* -----------------------------------------------------------------
 * 2. ADD / EDIT FORM SUBMISSION
 * ----------------------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon        = trim($_POST['icon'] ?? 'tool');
    $slug        = trim($_POST['slug'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $author      = trim($_POST['author'] ?? 'Admin');

    if (empty($slug) && !empty($title)) {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title));
    }

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0775, true);
    }

    $cover_image = $icon_image = '';
    if ($action === 'edit' && $service_id) {
        $stmt = executeQuery("SELECT cover_image, icon_image FROM services WHERE id = ?", [$service_id]);
        $svc  = $stmt->fetch();
        $cover_image = $svc['cover_image'] ?? 'placeholder.jpg';
        $icon_image  = $svc['icon_image'] ?? '';
    } else {
        $cover_image = 'placeholder.jpg';
    }

    // COVER IMAGE UPLOAD
    if (!empty($_FILES['cover_image']['name'])) {
        $ext   = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        $name  = time() . '_' . preg_replace('/[^a-z0-9\.\-_]/i', '', $_FILES['cover_image']['name']);
        $target = $upload_dir . $name;

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed) && $_FILES['cover_image']['size'] <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $target)) {
                $cover_image = $upload_url . $name;
            } else {
                $error_message = 'Failed to move cover image.';
            }
        } else {
            $error_message = 'Cover image: invalid type or size > 2 MB.';
        }
    }

    // ICON IMAGE UPLOAD
    if (!empty($_FILES['icon_image']['name'])) {
        $ext   = strtolower(pathinfo($_FILES['icon_image']['name'], PATHINFO_EXTENSION));
        $name  = time() . '_' . preg_replace('/[^a-z0-9\.\-_]/i', '', $_FILES['icon_image']['name']);
        $target = $upload_dir . $name;

        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        if (in_array($ext, $allowed) && $_FILES['icon_image']['size'] <= 1 * 1024 * 1024) {
            if (move_uploaded_file($_FILES['icon_image']['tmp_name'], $target)) {
                $icon_image = $upload_url . $name;
            } else {
                $error_message = 'Failed to move icon image.';
            }
        } else {
            $error_message = 'Icon image: invalid type or size > 1 MB.';
        }
    }

    if (empty($title) || empty($description)) {
        $error_message = 'Title and description are required.';
    }

    if (empty($error_message)) {
        try {
            if ($action === 'edit' && $service_id) {
                $sql = "UPDATE services 
                        SET title = ?, description = ?, icon = ?, slug = ?, 
                            category = ?, author = ?, cover_image = ?, icon_image = ?
                        WHERE id = ?";
                executeQuery($sql, [
                    $title, $description, $icon, $slug,
                    $category, $author, $cover_image, $icon_image, $service_id
                ]);
                $success_message = 'Service updated successfully!';
            } else {
                $sql = "INSERT INTO services 
                        (title, description, icon, slug, category, author,
                         cover_image, icon_image, created_at)
                        VALUES (?,?,?, ?,?,?, ?,?, NOW())";
                executeQuery($sql, [
                    $title, $description, $icon, $slug,
                    $category, $author, $cover_image, $icon_image
                ]);
                $success_message = 'Service added successfully!';
            }
            $action = 'list';
        } catch (PDOException $e) {
            error_log('Save Service Error: ' . $e->getMessage());
            $error_message = 'Error saving service.';
        }
    }
}

/* -----------------------------------------------------------------
 * 3. FETCH SERVICE FOR EDIT FORM
 * ----------------------------------------------------------------- */
$service = null;
if ($action === 'edit' && $service_id) {
    $stmt = executeQuery("SELECT * FROM services WHERE id = ?", [$service_id]);
    $service = $stmt->fetch();
    if (!$service) {
        $error_message = 'Service not found.';
        $action = 'list';
    }
}

/* -----------------------------------------------------------------
 * 4. FETCH ALL SERVICES (LIST VIEW)
 * ----------------------------------------------------------------- */
$services = [];
if ($action === 'list') {
    $services = executeQuery("SELECT * FROM services ORDER BY created_at DESC")->fetchAll();
}

/* -----------------------------------------------------------------
 * 5. PAGE RENDERING
 * ----------------------------------------------------------------- */
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Services</h1>
    <p>Create and manage construction services offered</p>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success">
        <i data-feather="check-circle"></i> <?= sanitizeOutput($success_message) ?>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error">
        <i data-feather="alert-circle"></i> <?= sanitizeOutput($error_message) ?>
    </div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <div class="card">
        <div class="card-header">
            <h2>All Services</h2>
            <a href="?action=add" class="btn btn-primary">
                <i data-feather="plus"></i> Add New Service
            </a>
        </div>

        <?php if (empty($services)): ?>
            <p class="p-4">
                No services found. <a href="?action=add">Add your first service</a>!
            </p>
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
                                <td><?= sanitizeOutput($svc['slug'] ?? '-') ?></td>
                                <td><?= sanitizeOutput($svc['category'] ?? '-') ?></td>
                                <td><?= sanitizeOutput($svc['author'] ?? 'Admin') ?></td>
                                <td><?= date('M d, Y', strtotime($svc['created_at'])) ?></td>
                                <td class="table-actions">
                                    <a href="?action=edit&id=<?= $svc['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="?action=delete&id=<?= $svc['id'] ?>"
                                       class="btn-delete"
                                       onclick="return confirm('Delete this service?')">Delete</a>
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
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="arrow-left"></i> Back
            </a>
        </div>

        <form method="POST" action="" enctype="multipart/form-data" class="p-4" id="serviceForm">
            <div class="form-group mb-3">
                <label class="form-label">Service Title *</label>
                <input type="text" name="title" class="form-input" value="<?= $service['title'] ?? '' ?>" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Slug (auto if blank)</label>
                <input type="text" name="slug" class="form-input" value="<?= $service['slug'] ?? '' ?>">
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Description *</label>
                <textarea name="description" rows="6" class="form-textarea" required><?= $service['description'] ?? '' ?></textarea>
            </div>

            <!-- COVER IMAGE WITH PREVIEW -->
            <div class="form-group mb-3">
                <label class="form-label">Cover Image</label>
                <div class="image-preview-container mb-2" id="coverPreviewContainer">
                    <?php if (!empty($service['cover_image']) && $service['cover_image'] !== 'placeholder.jpg'): ?>
                        <img src="<?= sanitizeOutput($service['cover_image']) ?>" alt="Current cover" class="img-preview">
                    <?php elseif (!empty($service['cover_image'])): ?>
                        <img src="<?= SITE_URL ?>/assets/images/placeholder.jpg" alt="Placeholder" class="img-preview">
                    <?php endif; ?>
                </div>
                <input type="file" name="cover_image" accept=".jpg,.jpeg,.png,.webp" class="form-input" id="coverInput">
                <small class="text-muted">Max 2 MB – jpg, jpeg, png, webp</small>
            </div>

            <!-- ICON IMAGE WITH PREVIEW -->
            <div class="form-group mb-3">
                <label class="form-label">Icon Image (optional)</label>
                <div class="image-preview-container mb-2" id="iconPreviewContainer">
                    <?php if (!empty($service['icon_image'])): ?>
                        <img src="<?= sanitizeOutput($service['icon_image']) ?>" alt="Current icon" class="img-preview icon">
                    <?php endif; ?>
                </div>
                <input type="file" name="icon_image" accept=".jpg,.jpeg,.png,.svg,.webp" class="form-input" id="iconInput">
                <small class="text-muted">Max 1 MB – jpg, png, svg, webp</small>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Feather Icon Name (fallback)</label>
                <input type="text" name="icon" class="form-input" value="<?= $service['icon'] ?? 'tool' ?>">
                <small class="text-muted">e.g., tool, home, settings (<a href="https://feathericons.com" target="_blank">see all</a>)</small>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-input" value="<?= $service['category'] ?? '' ?>">
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Author</label>
                <input type="text" name="author" class="form-input" value="<?= $service['author'] ?? 'Admin' ?>">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i> Save Service
                </button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <!-- LIVE PREVIEW SCRIPT -->
    <script>
        document.getElementById('coverInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const container = document.getElementById('coverPreviewContainer');
            container.innerHTML = '';

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-preview';
                    img.style.maxHeight = '140px';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('iconInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const container = document.getElementById('iconPreviewContainer');
            container.innerHTML = '';

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-preview icon';
                    img.style.maxHeight = '60px';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

    <style>
        .image-preview-container {
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 8px;
        }
        .img-preview {
            max-width: 100%;
            max-height: 140px;
            border-radius: 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .img-preview.icon {
            max-height: 60px;
            max-width: 60px;
        }
    </style>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>