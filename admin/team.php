<?php
/**
 * Admin Team Management
 * 
 * CRUD operations for team members
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/upload.php';
require_once __DIR__ . '/../config.php';

requireAdmin();

$page_title = 'Manage Team';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$member_id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $member_id) {
    try {
        // Get member to delete photo
        $stmt = executeQuery("SELECT photo FROM team WHERE id = ?", [$member_id]);
        $member = $stmt->fetch();
        
        if ($member) {
            deleteUploadedFile($member['photo']);
        }
        
        executeQuery("DELETE FROM team WHERE id = ?", [$member_id]);
        $success_message = 'Team member deleted successfully!';
        $action = 'list';
    } catch (PDOException $e) {
        error_log('Delete Team Member Error: ' . $e->getMessage());
        $error_message = 'Error deleting team member.';
    }
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    
    // Keep existing photo for edit
    $photo = 'avatar.jpg';
    if ($action === 'edit' && $member_id) {
        $stmt = executeQuery("SELECT photo FROM team WHERE id = ?", [$member_id]);
        $existing = $stmt->fetch();
        $photo = $existing['photo'] ?? 'avatar.jpg';
    }
    
    if (empty($name) || empty($role)) {
        $error_message = 'Please fill in all required fields.';
    } else {
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImage($_FILES['photo']);
            
            if ($uploadResult['success']) {
                // Delete old photo if editing
                if ($action === 'edit' && $photo !== 'avatar.jpg') {
                    deleteUploadedFile($photo);
                }
                $photo = $uploadResult['filename'];
            } else {
                $error_message = 'Photo upload failed: ' . $uploadResult['error'];
            }
        }
        
        if (empty($error_message)) {
            try {
                if ($action === 'edit' && $member_id) {
                    $sql = "UPDATE team SET name = ?, role = ?, photo = ?, bio = ? WHERE id = ?";
                    executeQuery($sql, [$name, $role, $photo, $bio, $member_id]);
                    $success_message = 'Team member updated successfully!';
                } else {
                    $sql = "INSERT INTO team (name, role, photo, bio) VALUES (?, ?, ?, ?)";
                    executeQuery($sql, [$name, $role, $photo, $bio]);
                    $success_message = 'Team member added successfully!';
                }
                $action = 'list';
            } catch (PDOException $e) {
                error_log('Save Team Member Error: ' . $e->getMessage());
                $error_message = 'Error saving team member.';
            }
        }
    }
}

// Fetch member for editing
$member = null;
if ($action === 'edit' && $member_id) {
    $stmt = executeQuery("SELECT * FROM team WHERE id = ?", [$member_id]);
    $member = $stmt->fetch();
    if (!$member) {
        $error_message = 'Team member not found.';
        $action = 'list';
    }
}

// Fetch all team members for listing
$team_members = [];
if ($action === 'list') {
    $team_members = executeQuery("SELECT * FROM team ORDER BY created_at ASC")->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Team</h1>
    <p>Create and manage team members</p>
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
            <h2 class="card-title">All Team Members</h2>
            <a href="?action=add" class="btn btn-primary">
                <i data-feather="plus"></i> Add Team Member
            </a>
        </div>
        
        <?php if (empty($team_members)): ?>
            <p>No team members found. <a href="?action=add">Add your first team member</a>!</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Bio</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($team_members as $tm): ?>
                            <tr>
                                <td><strong><?= sanitizeOutput($tm['name']) ?></strong></td>
                                <td><?= sanitizeOutput($tm['role']) ?></td>
                                <td><?= sanitizeOutput(substr($tm['bio'], 0, 60)) ?>...</td>
                                <td><?= date('M d, Y', strtotime($tm['created_at'])) ?></td>
                                <td class="table-actions">
                                    <a href="?action=edit&id=<?= $tm['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="?action=delete&id=<?= $tm['id'] ?>" class="btn-delete">Delete</a>
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
            <h2 class="card-title"><?= $action === 'edit' ? 'Edit Team Member' : 'Add Team Member' ?></h2>
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
        </div>
        
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-input"
                           value="<?= $member ? sanitizeOutput($member['name']) : '' ?>"
                           placeholder="e.g., Rajesh Kumar"
                           required>
                </div>
                
                <div class="form-group">
                    <label for="role" class="form-label">Role/Position *</label>
                    <input type="text" 
                           id="role" 
                           name="role" 
                           class="form-input"
                           value="<?= $member ? sanitizeOutput($member['role']) : '' ?>"
                           placeholder="e.g., Managing Director"
                           required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="photo" class="form-label">Member Photo</label>
                <input type="file" 
                       id="photo" 
                       name="photo" 
                       class="form-input"
                       accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                       onchange="previewPhoto(this)">
                <p class="form-help">Upload JPG, PNG, GIF or WebP (Max: 5MB, Recommended: 400x400px square)</p>
                
                <?php if ($member && $member['photo'] && $member['photo'] !== 'avatar.jpg'): ?>
                    <div class="current-image" style="margin-top: 1rem;">
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.5rem;">Current Photo:</p>
                        <img src="<?php echo SITE_URL; ?>/assets/images/<?= sanitizeOutput($member['photo']) ?>" 
                             alt="Current" 
                             style="max-width: 150px; border-radius: 50%; border: 2px solid #e2e8f0;">
                    </div>
                <?php endif; ?>
                
                <div id="photoPreview" style="margin-top: 1rem; display: none;">
                    <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.5rem;">New Photo Preview:</p>
                    <img id="preview" src="" alt="Preview" style="max-width: 150px; border-radius: 50%; border: 2px solid #3b82f6;">
                </div>
            </div>
            
            <div class="form-group">
                <label for="bio" class="form-label">Bio/Description</label>
                <textarea id="bio" 
                          name="bio" 
                          class="form-textarea"
                          rows="4"
                          placeholder="Brief description about the team member..."><?= $member ? sanitizeOutput($member['bio']) : '' ?></textarea>
            </div>
            
            <script>
            function previewPhoto(input) {
                const preview = document.getElementById('preview');
                const previewContainer = document.getElementById('photoPreview');
                
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
                    <i data-feather="save"></i> <?= $action === 'edit' ? 'Update Member' : 'Add Member' ?>
                </button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
