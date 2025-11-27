<?php
/**
 * Admin Projects Management
 * CRUD operations for projects (NO main image in DB)
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_once __DIR__ . '/../config.php';

requireAdmin();

$page_title = 'Manage Projects';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$project_id = $_GET['id'] ?? null;

// ---------------------------------------------------------------------
// Helper: Get first gallery image as thumbnail
// ---------------------------------------------------------------------
function getProjectThumbnail(int $pid, string $basePath): string
{
    $stmt = executeQuery(
        "SELECT image_path FROM project_images 
         WHERE project_id = ? 
         ORDER BY `order` ASC, id ASC 
         LIMIT 1",
        [$pid]
    );
    $row = $stmt->fetch();

    if ($row && !empty($row['image_path'])) {
        return $basePath . '/assets/images/' . $row['image_path'];
    }

    return 'https://via.placeholder.com/600x400/1A1A1A/F9A826?text=No+Image';
}

// ---------------------------------------------------------------------
// Handle Delete
// ---------------------------------------------------------------------
if ($action === 'delete' && $project_id) {
    try {
        // Delete gallery images + files
        $gallery = executeQuery("SELECT image_path FROM project_images WHERE project_id = ?", [$project_id])->fetchAll();
        foreach ($gallery as $g) {
            if (!empty($g['image_path'])) {
                deleteUploadedFile($g['image_path']);
            }
        }
        executeQuery("DELETE FROM project_images WHERE project_id = ?", [$project_id]);

        // Delete project
        executeQuery("DELETE FROM projects WHERE id = ?", [$project_id]);

        $success_message = 'Project and all images deleted successfully!';
        $action = 'list';
    } catch (PDOException $e) {
        error_log('Delete Project Error: ' . $e->getMessage());
        $error_message = 'Error deleting project.';
    }
}

// ---------------------------------------------------------------------
// Handle Add/Edit Submission
// ---------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title        = trim($_POST['title'] ?? '');
    $location     = trim($_POST['location'] ?? '');
    $description  = trim($_POST['description'] ?? '');

    // NEW FIELDS
    $client_name        = trim($_POST['client_name'] ?? '');
    $client_testimonial = trim($_POST['client_testimonial'] ?? '');
    $client_budget      = trim($_POST['client_budget'] ?? '');

    $type         = $_POST['type'] ?? 'residential';
    $status       = $_POST['status'] ?? 'current';
    $size         = trim($_POST['size'] ?? '');
    $duration     = trim($_POST['duration'] ?? '');
    $completed_on = trim($_POST['completed_on'] ?? '');

    if (empty($title) || empty($location) || empty($description)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        try {

            if ($action === 'edit' && $project_id) {

                $sql = "UPDATE projects SET 
                        title = ?, 
                        location = ?, 
                        description = ?, 
                        client_name = ?,
                        client_testimonial = ?,
                        client_budget = ?,
                        type = ?, 
                        status = ?, 
                        size = ?, 
                        duration = ?, 
                        completed_on = ?,
                        updated_at = NOW()
                    WHERE id = ?";

                executeQuery($sql, [
                    $title,
                    $location,
                    $description,
                    $client_name,
                    $client_testimonial,
                    $client_budget ?: null,
                    $type,
                    $status,
                    $size,
                    $duration,
                    $completed_on ?: null,
                    $project_id
                ]);

                $success_message = 'Project updated successfully!';

            } else {

                $sql = "INSERT INTO projects 
                        (title, location, description,
                         client_name, client_testimonial, client_budget,
                         type, status, size, duration, completed_on, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

                executeQuery($sql, [
                    $title,
                    $location,
                    $description,
                    $client_name,
                    $client_testimonial,
                    $client_budget ?: null,
                    $type,
                    $status,
                    $size,
                    $duration,
                    $completed_on ?: null
                ]);

                $success_message = 'Project added successfully!';
            }

            $action = 'list';

        } catch (PDOException $e) {
            error_log('Save Project Error: ' . $e->getMessage());
            $error_message = 'Error saving project.';
        }
    }
}

// ---------------------------------------------------------------------
// Fetch project for editing
// ---------------------------------------------------------------------
$project = null;
if ($action === 'edit' && $project_id) {
    $stmt = executeQuery("SELECT * FROM projects WHERE id = ?", [$project_id]);
    $project = $stmt->fetch();
    if (!$project) {
        $error_message = 'Project not found.';
        $action = 'list';
    }
}

// ---------------------------------------------------------------------
// Fetch all projects
// ---------------------------------------------------------------------
$projects = [];
if ($action === 'list') {
    $stmt = executeQuery("
        SELECT * FROM projects 
        ORDER BY 
            CASE status WHEN 'current' THEN 1 WHEN 'future' THEN 2 ELSE 3 END,
            completed_on DESC, created_at DESC
    ");
    $raw = $stmt->fetchAll();
    $basePath = rtrim(SITE_URL, '/');

    foreach ($raw as $p) {
        $p['thumbnail'] = getProjectThumbnail((int)$p['id'], $basePath);
        $projects[] = $p;
    }
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Projects</h1>
    <p>Create and manage construction projects</p>
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

    <!-- LIST VIEW -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">All Projects</h2>
            <a href="?action=add" class="btn btn-primary">
                <i data-feather="plus"></i> Add New Project
            </a>
        </div>

        <?php if (empty($projects)): ?>
            <p>No projects found. <a href="?action=add">Add your first project</a>.</p>

        <?php else: ?>

            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Thumbnail</th>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Client</th>
                            <th>Budget</th>
                            <th>Status</th>
                            <th>Completed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($projects as $proj): ?>
                        <tr>
                            <td>
                                <img src="<?= $proj['thumbnail'] ?>" 
                                     style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
                            </td>

                            <td><strong><?= sanitizeOutput($proj['title']) ?></strong></td>

                            <td><?= sanitizeOutput($proj['location']) ?></td>

                            <td><?= sanitizeOutput($proj['client_name'] ?? '—') ?></td>

                            <td>
                                <?= is_numeric($proj['client_budget']) ? number_format((float)$proj['client_budget']) : '—' ?>
                            </td>

                            <td><?= ucfirst($proj['status']) ?></td>

                            <td><?= $proj['completed_on'] ? date('M Y', strtotime($proj['completed_on'])) : '—' ?></td>

                            <td class="table-actions">
                                <a href="?action=edit&id=<?= $proj['id'] ?>" class="btn-edit">Edit</a>
                                <a href="?action=delete&id=<?= $proj['id'] ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Delete project and ALL images?');">Delete</a>
                                <a href="project_gallery.php?project_id=<?= $proj['id'] ?>" class="btn btn-info btn-sm">Gallery</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        <?php endif; ?>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>

    <!-- ADD / EDIT FORM -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?= $action === 'edit' ? 'Edit Project' : 'Add New Project' ?></h2>
            <a href="?action=list" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back</a>
        </div>

        <form method="POST">

            <div class="form-grid">
                <div class="form-group">
                    <label>Project Title *</label>
                    <input type="text" name="title" class="form-input"
                           value="<?= $project['title'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label>Location *</label>
                    <input type="text" name="location" class="form-input"
                           value="<?= $project['location'] ?? '' ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" class="form-textarea" rows="5" required><?= $project['description'] ?? '' ?></textarea>
            </div>

            <!-- NEW: Client Name -->
            <div class="form-group">
                <label>Client Name</label>
                <input type="text" name="client_name" class="form-input"
                       value="<?= $project['client_name'] ?? '' ?>">
            </div>

            <!-- NEW: Testimonial -->
            <div class="form-group">
                <label>Client Testimonial</label>
                <textarea name="client_testimonial" class="form-textarea" rows="4"><?= $project['client_testimonial'] ?? '' ?></textarea>
            </div>

            <!-- NEW: Budget -->
            <div class="form-group">
                <label>Client Budget (₹)</label>
                <input type="number" step="0.01" name="client_budget" class="form-input"
                       value="<?= $project['client_budget'] ?? '' ?>">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Type</label>
                    <select name="type" class="form-input">
                        <option value="residential" <?= isset($project) && $project['type']=='residential'?'selected':'' ?>>Residential</option>
                        <option value="commercial" <?= isset($project) && $project['type']=='commercial'?'selected':'' ?>>Commercial</option>
                        <option value="renovation" <?= isset($project) && $project['type']=='renovation'?'selected':'' ?>>Renovation</option>
                        <option value="institutional" <?= isset($project) && $project['type']=='institutional'?'selected':'' ?>>Institutional</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-input">
                        <option value="current" <?= isset($project) && $project['status']=='current'?'selected':'' ?>>Current</option>
                        <option value="future" <?= isset($project) && $project['status']=='future'?'selected':'' ?>>Future</option>
                        <option value="completed" <?= isset($project) && $project['status']=='completed'?'selected':'' ?>>Completed</option>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Size</label>
                    <input type="text" name="size" class="form-input"
                           value="<?= $project['size'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" name="duration" class="form-input"
                           value="<?= $project['duration'] ?? '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Completion Date</label>
                <input type="date" name="completed_on" class="form-input"
                       value="<?= $project['completed_on'] ?? '' ?>">
            </div>

            <div class="btn-group" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i>
                    <?= $action === 'edit' ? 'Update Project' : 'Add Project' ?>
                </button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
