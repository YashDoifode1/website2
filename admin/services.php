<?php
/**
 * Admin Services Management
 * 
 * CRUD operations for services
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$page_title = 'Manage Services';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$service_id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $service_id) {
    try {
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
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'tool');
    
    if (empty($title) || empty($description)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        try {
            if ($action === 'edit' && $service_id) {
                $sql = "UPDATE services SET title = ?, description = ?, icon = ? WHERE id = ?";
                executeQuery($sql, [$title, $description, $icon, $service_id]);
                $success_message = 'Service updated successfully!';
            } else {
                $sql = "INSERT INTO services (title, description, icon) VALUES (?, ?, ?)";
                executeQuery($sql, [$title, $description, $icon]);
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

// Fetch all services for listing
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
            <h2 class="card-title">All Services</h2>
            <a href="?action=add" class="btn btn-primary">
                <i data-feather="plus"></i> Add New Service
            </a>
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
                            <th>Description</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $svc): ?>
                            <tr>
                                <td><i data-feather="<?= sanitizeOutput($svc['icon']) ?>"></i></td>
                                <td><strong><?= sanitizeOutput($svc['title']) ?></strong></td>
                                <td><?= sanitizeOutput(substr($svc['description'], 0, 80)) ?>...</td>
                                <td><?= date('M d, Y', strtotime($svc['created_at'])) ?></td>
                                <td class="table-actions">
                                    <a href="?action=edit&id=<?= $svc['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="?action=delete&id=<?= $svc['id'] ?>" class="btn-delete">Delete</a>
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
            <h2 class="card-title"><?= $action === 'edit' ? 'Edit Service' : 'Add New Service' ?></h2>
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
        </div>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="title" class="form-label">Service Title *</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       class="form-input"
                       value="<?= $service ? sanitizeOutput($service['title']) : '' ?>"
                       placeholder="e.g., Residential Construction"
                       required>
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description *</label>
                <textarea id="description" 
                          name="description" 
                          class="form-textarea"
                          rows="5" 
                          placeholder="Describe the service..."
                          required><?= $service ? sanitizeOutput($service['description']) : '' ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="icon" class="form-label">Icon Name (Feather Icons)</label>
                <input type="text" 
                       id="icon" 
                       name="icon" 
                       class="form-input"
                       value="<?= $service ? sanitizeOutput($service['icon']) : 'tool' ?>"
                       placeholder="e.g., home, briefcase, tool">
                <p class="form-help">
                    Browse icons at: <a href="https://feathericons.com/" target="_blank">feathericons.com</a>
                </p>
            </div>
            
            <div class="btn-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i> <?= $action === 'edit' ? 'Update Service' : 'Add Service' ?>
                </button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
