<?php
/**
 * Admin Projects Management (Updated)
 * Supports status, type, size, duration, and project gallery
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';

requireAdmin();

$page_title = 'Manage Projects';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$project_id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $project_id) {
    try {
        executeQuery("DELETE FROM projects WHERE id = ?", [$project_id]);
        executeQuery("DELETE FROM project_images WHERE project_id = ?", [$project_id]);
        $success_message = 'Project deleted successfully!';
        $action = 'list';
    } catch (PDOException $e) {
        error_log('Delete Project Error: ' . $e->getMessage());
        $error_message = 'Error deleting project.';
    }
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $type = $_POST['type'] ?? 'residential';
    $status = $_POST['status'] ?? 'current';
    $size = trim($_POST['size'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $completed_on = trim($_POST['completed_on'] ?? '');

    if (empty($title) || empty($location) || empty($description)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        try {
            if ($action === 'edit' && $project_id) {
                $sql = "UPDATE projects 
                        SET title = ?, location = ?, description = ?, type = ?, status = ?, 
                            size = ?, duration = ?, completed_on = ? 
                        WHERE id = ?";
                executeQuery($sql, [$title, $location, $description, $type, $status, $size ?: null, $duration ?: null, $completed_on ?: null, $project_id]);
                $success_message = 'Project updated successfully!';
            } else {
                $sql = "INSERT INTO projects (title, location, description, type, status, size, duration, completed_on) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                executeQuery($sql, [$title, $location, $description, $type, $status, $size ?: null, $duration ?: null, $completed_on ?: null]);
                $success_message = 'Project added successfully!';
            }
            $action = 'list';
        } catch (PDOException $e) {
            error_log('Save Project Error: ' . $e->getMessage());
            $error_message = 'Error saving project.';
        }
    }
}

// Fetch project for editing
$project = null;
if ($action === 'edit' && $project_id) {
    $stmt = executeQuery("SELECT * FROM projects WHERE id = ?", [$project_id]);
    $project = $stmt->fetch();
    if (!$project) {
        $error_message = 'Project not found.';
        $action = 'list';
    }
}

// Fetch all projects for listing
$projects = [];
if ($action === 'list') {
    $projects = executeQuery("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Projects</h1>
    <p>Create and manage all construction projects</p>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success"><?= sanitizeOutput($success_message) ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error"><?= sanitizeOutput($error_message) ?></div>
<?php endif; ?>


<?php if ($action === 'list'): ?>
    <!-- Project List View -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">All Projects</h2>
            <a href="?action=add" class="btn btn-primary"><i data-feather="plus"></i> Add New Project</a>
        </div>

        <?php if (empty($projects)): ?>
            <p>No projects found. <a href="?action=add">Add one now</a>.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Size</th>
                            <th>Duration</th>
                            <th>Completed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $proj): ?>
                            <tr>
                                <td><strong><?= sanitizeOutput($proj['title']) ?></strong></td>
                                <td><?= ucfirst($proj['type']) ?></td>
                                <td><span class="badge <?= $proj['status'] === 'completed' ? 'bg-success' : ($proj['status'] === 'future' ? 'bg-info' : 'bg-warning') ?>">
                                    <?= ucfirst($proj['status']) ?>
                                </span></td>
                                <td><?= sanitizeOutput($proj['location']) ?></td>
                                <td><?= sanitizeOutput($proj['size'] ?? '-') ?></td>
                                <td><?= sanitizeOutput($proj['duration'] ?? '-') ?></td>
                                <td><?= $proj['completed_on'] ? date('M Y', strtotime($proj['completed_on'])) : 'Ongoing' ?></td>
                                <td class="table-actions">
                                    <a href="?action=edit&id=<?= $proj['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="project_gallery.php?project_id=<?= $proj['id'] ?>" class="btn btn-secondary btn-sm">Gallery</a>
                                    <a href="?action=delete&id=<?= $proj['id'] ?>" class="btn-delete" onclick="return confirm('Delete this project?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <!-- Add/Edit Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?= $action === 'edit' ? 'Edit Project' : 'Add New Project' ?></h2>
            <a href="?action=list" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back to List</a>
        </div>

        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-input" value="<?= $project['title'] ?? '' ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Location *</label>
                    <input type="text" name="location" class="form-input" value="<?= $project['location'] ?? '' ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-textarea" rows="5" required><?= $project['description'] ?? '' ?></textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Project Type</label>
                    <select name="type" class="form-select">
                        <?php
                        $types = ['residential', 'commercial', 'renovation', 'industrial', 'infrastructure'];
                        foreach ($types as $t) {
                            $selected = isset($project['type']) && $project['type'] === $t ? 'selected' : '';
                            echo "<option value='$t' $selected>" . ucfirst($t) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Project Status</label>
                    <select name="status" class="form-select">
                        <?php
                        $statuses = ['current', 'future', 'completed'];
                        foreach ($statuses as $s) {
                            $selected = isset($project['status']) && $project['status'] === $s ? 'selected' : '';
                            echo "<option value='$s' $selected>" . ucfirst($s) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Project Size</label>
                    <input type="text" name="size" class="form-input" value="<?= $project['size'] ?? '' ?>" placeholder="e.g., 40x60 site">
                </div>

                <div class="form-group">
                    <label class="form-label">Project Duration</label>
                    <input type="text" name="duration" class="form-input" value="<?= $project['duration'] ?? '' ?>" placeholder="e.g., 8 months">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Completion Date</label>
                <input type="date" name="completed_on" class="form-input" value="<?= $project['completed_on'] ?? '' ?>">
            </div>

            <div class="btn-group mt-3">
                <button type="submit" class="btn btn-primary"><i data-feather="save"></i> <?= $action === 'edit' ? 'Update Project' : 'Add Project' ?></button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
