<?php
/**
 * Admin Testimonials Management
 * 
 * CRUD operations for testimonials
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config.php';

requireAdmin();

$page_title = 'Manage Testimonials';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$testimonial_id = $_GET['id'] ?? null;

// Fetch all projects for dropdown
$projects = executeQuery("SELECT id, title FROM projects ORDER BY title ASC")->fetchAll();

// Handle Delete
if ($action === 'delete' && $testimonial_id) {
    try {
        executeQuery("DELETE FROM testimonials WHERE id = ?", [$testimonial_id]);
        $success_message = 'Testimonial deleted successfully!';
        $action = 'list';
    } catch (PDOException $e) {
        error_log('Delete Testimonial Error: ' . $e->getMessage());
        $error_message = 'Error deleting testimonial.';
    }
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name'] ?? '');
    $text = trim($_POST['text'] ?? '');
    $project_id = trim($_POST['project_id'] ?? '');
    
    if (empty($client_name) || empty($text)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        try {
            if ($action === 'edit' && $testimonial_id) {
                $sql = "UPDATE testimonials SET client_name = ?, text = ?, project_id = ? WHERE id = ?";
                executeQuery($sql, [$client_name, $text, $project_id ?: null, $testimonial_id]);
                $success_message = 'Testimonial updated successfully!';
            } else {
                $sql = "INSERT INTO testimonials (client_name, text, project_id) VALUES (?, ?, ?)";
                executeQuery($sql, [$client_name, $text, $project_id ?: null]);
                $success_message = 'Testimonial added successfully!';
            }
            $action = 'list';
        } catch (PDOException $e) {
            error_log('Save Testimonial Error: ' . $e->getMessage());
            $error_message = 'Error saving testimonial.';
        }
    }
}

// Fetch testimonial for editing
$testimonial = null;
if ($action === 'edit' && $testimonial_id) {
    $stmt = executeQuery("SELECT * FROM testimonials WHERE id = ?", [$testimonial_id]);
    $testimonial = $stmt->fetch();
    if (!$testimonial) {
        $error_message = 'Testimonial not found.';
        $action = 'list';
    }
}

// Fetch all testimonials for listing
$testimonials = [];
if ($action === 'list') {
    $sql = "SELECT t.*, p.title as project_title 
            FROM testimonials t 
            LEFT JOIN projects p ON t.project_id = p.id 
            ORDER BY t.created_at DESC";
    $testimonials = executeQuery($sql)->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Testimonials</h1>
    <p>Create and manage client testimonials</p>
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
            <h2 class="card-title">All Testimonials</h2>
            <a href="?action=add" class="btn btn-primary">
                <i data-feather="plus"></i> Add New Testimonial
            </a>
        </div>
        
        <?php if (empty($testimonials)): ?>
            <p>No testimonials found. <a href="?action=add">Add your first testimonial</a>!</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Testimonial</th>
                            <th>Project</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testimonials as $test): ?>
                            <tr>
                                <td><strong><?= sanitizeOutput($test['client_name']) ?></strong></td>
                                <td><?= sanitizeOutput(substr($test['text'], 0, 80)) ?>...</td>
                                <td><?= $test['project_title'] ? sanitizeOutput($test['project_title']) : 'N/A' ?></td>
                                <td><?= date('M d, Y', strtotime($test['created_at'])) ?></td>
                                <td class="table-actions">
                                    <a href="?action=edit&id=<?= $test['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="?action=delete&id=<?= $test['id'] ?>" class="btn-delete">Delete</a>
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
            <h2 class="card-title"><?= $action === 'edit' ? 'Edit Testimonial' : 'Add New Testimonial' ?></h2>
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="client_name" class="form-label">Client Name *</label>
                <input type="text" 
                       id="client_name" 
                       name="client_name" 
                       class="form-input"
                       value="<?= $testimonial ? sanitizeOutput($testimonial['client_name']) : '' ?>"
                       placeholder="e.g., Mr. Suresh Deshmukh"
                       required>
            </div>
            
            <div class="form-group">
                <label for="text" class="form-label">Testimonial Text *</label>
                <textarea id="text" 
                          name="text" 
                          class="form-textarea"
                          rows="5" 
                          placeholder="Enter the client's testimonial..."
                          required><?= $testimonial ? sanitizeOutput($testimonial['text']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="project_id" class="form-label">Related Project (Optional)</label>
                <select id="project_id" name="project_id" class="form-select">
                    <option value="">-- Select Project --</option>
                    <?php foreach ($projects as $proj): ?>
                        <option value="<?= $proj['id'] ?>" 
                                <?= ($testimonial && $testimonial['project_id'] == $proj['id']) ? 'selected' : '' ?>>
                            <?= sanitizeOutput($proj['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="form-help">Link this testimonial to a specific project</p>
            </div>
            
            <div class="btn-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i> <?= $action === 'edit' ? 'Update Testimonial' : 'Add Testimonial' ?>
                </button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
