<?php
/**
 * Admin Projects Management
 * 
 * CRUD operations for projects
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
        // Get project to delete image
        $stmt = executeQuery("SELECT image FROM projects WHERE id = ?", [$project_id]);
        $project = $stmt->fetch();
        
        if ($project) {
            deleteUploadedFile($project['image']);
        }
        
        executeQuery("DELETE FROM projects WHERE id = ?", [$project_id]);
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
    $completed_on = trim($_POST['completed_on'] ?? '');
    
    // Keep existing image for edit
    $image = 'placeholder.jpg';
    if ($action === 'edit' && $project_id) {
        $stmt = executeQuery("SELECT image FROM projects WHERE id = ?", [$project_id]);
        $existing = $stmt->fetch();
        $image = $existing['image'] ?? 'placeholder.jpg';
    }
    
    if (empty($title) || empty($location) || empty($description)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImage($_FILES['image']);
            
            if ($uploadResult['success']) {
                // Delete old image if editing
                if ($action === 'edit' && $image !== 'placeholder.jpg') {
                    deleteUploadedFile($image);
                }
                $image = $uploadResult['filename'];
            } else {
                $error_message = 'Image upload failed: ' . $uploadResult['error'];
            }
        }
        
        if (empty($error_message)) {
            try {
                if ($action === 'edit' && $project_id) {
                    $sql = "UPDATE projects SET title = ?, location = ?, description = ?, image = ?, completed_on = ? WHERE id = ?";
                    executeQuery($sql, [$title, $location, $description, $image, $completed_on ?: null, $project_id]);
                    $success_message = 'Project updated successfully!';
                } else {
                    $sql = "INSERT INTO projects (title, location, description, image, completed_on) VALUES (?, ?, ?, ?, ?)";
                    executeQuery($sql, [$title, $location, $description, $image, $completed_on ?: null]);
                    $success_message = 'Project added successfully!';
                }
                $action = 'list';
            } catch (PDOException $e) {
                error_log('Save Project Error: ' . $e->getMessage());
                $error_message = 'Error saving project.';
            }
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
    $projects = executeQuery("SELECT * FROM projects ORDER BY completed_on DESC, created_at DESC")->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Projects</h1>
    <p>Create and manage completed construction projects</p>
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
    <!-- List View -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">All Projects</h2>
            <a href="?action=add" class="btn btn-primary">
                <i data-feather="plus"></i> Add New Project
            </a>
        </div>
        
        <?php if (empty($projects)): ?>
            <p>No projects found. <a href="?action=add">Add your first project</a>!</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Description</th>
                            <th>Completed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $proj): ?>
                            <tr>
                                <td><strong><?= sanitizeOutput($proj['title']) ?></strong></td>
                                <td><?= sanitizeOutput($proj['location']) ?></td>
                                <td><?= sanitizeOutput(substr($proj['description'], 0, 60)) ?>...</td>
                                <td><?= $proj['completed_on'] ? date('M Y', strtotime($proj['completed_on'])) : 'N/A' ?></td>
                                <td class="table-actions">
                                    <a href="?action=edit&id=<?= $proj['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="?action=delete&id=<?= $proj['id'] ?>" class="btn-delete">Delete</a>
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
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
        </div>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="title" class="form-label">Project Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="form-input"
                           value="<?= $project ? sanitizeOutput($project['title']) : '' ?>"
                           placeholder="e.g., Mr. Kushal Harish Residence"
                           required>
                    <p class="form-help">Enter the project name or client name</p>
                </div>
                
                <div class="form-group">
                    <label for="location" class="form-label">Location *</label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           class="form-input"
                           value="<?= $project ? sanitizeOutput($project['location']) : '' ?>"
                           placeholder="e.g., Nelamangala, Bangalore"
                           required>
                    <p class="form-help">Enter the project location</p>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description *</label>
                <textarea id="description" 
                          name="description" 
                          class="form-textarea"
                          rows="5" 
                          placeholder="Enter project details including site dimensions, building type (G+2.5), etc."
                          required><?= $project ? sanitizeOutput($project['description']) : '' ?></textarea>
                <p class="form-help">Provide detailed description of the project</p>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="image" class="form-label">Project Image</label>
                    <input type="file" 
                           id="image" 
                           name="image" 
                           class="form-input"
                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                           onchange="previewImage(this)">
                    <p class="form-help">Upload JPG, PNG, GIF or WebP (Max: 5MB)</p>
                    
                    <?php if ($project && $project['image'] && $project['image'] !== 'placeholder.jpg'): ?>
                        <div class="current-image" style="margin-top: 1rem;">
                            <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.5rem;">Current Image:</p>
                            <img src="/constructioninnagpur/assets/images/<?= sanitizeOutput($project['image']) ?>" 
                                 alt="Current" 
                                 style="max-width: 200px; border-radius: 8px; border: 2px solid #e2e8f0;">
                        </div>
                    <?php endif; ?>
                    
                    <div id="imagePreview" style="margin-top: 1rem; display: none;">
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.5rem;">New Image Preview:</p>
                        <img id="preview" src="" alt="Preview" style="max-width: 200px; border-radius: 8px; border: 2px solid #3b82f6;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="completed_on" class="form-label">Completion Date</label>
                    <input type="date" 
                           id="completed_on" 
                           name="completed_on" 
                           class="form-input"
                           value="<?= $project && $project['completed_on'] ? $project['completed_on'] : '' ?>">
                    <p class="form-help">Select the project completion date</p>
                </div>
            </div>
            
            <script>
            function previewImage(input) {
                const preview = document.getElementById('preview');
                const previewContainer = document.getElementById('imagePreview');
                
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
            
            <div class="btn-group" style="margin-top: 2rem;">
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
