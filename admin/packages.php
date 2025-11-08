<?php
/**
 * Admin Packages Management
 * 
 * CRUD operations for construction packages
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$page_title = 'Manage Packages';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$package_id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $package_id) {
    try {
        executeQuery("DELETE FROM packages WHERE id = ?", [$package_id]);
        $success_message = 'Package deleted successfully!';
        $action = 'list';
    } catch (PDOException $e) {
        error_log('Delete Package Error: ' . $e->getMessage());
        $error_message = 'Error deleting package.';
    }
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $price_per_sqft = trim($_POST['price_per_sqft'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $features = trim($_POST['features'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $display_order = intval($_POST['display_order'] ?? 0);
    
    if (empty($title) || empty($price_per_sqft) || empty($features)) {
        $error_message = 'Please fill in all required fields (Title, Price, Features).';
    } elseif (!is_numeric($price_per_sqft) || $price_per_sqft <= 0) {
        $error_message = 'Please enter a valid price per sqft.';
    } else {
        try {
            if ($action === 'edit' && $package_id) {
                $sql = "UPDATE packages SET title = ?, price_per_sqft = ?, description = ?, features = ?, notes = ?, is_active = ?, display_order = ? WHERE id = ?";
                executeQuery($sql, [$title, $price_per_sqft, $description, $features, $notes, $is_active, $display_order, $package_id]);
                $success_message = 'Package updated successfully!';
            } else {
                $sql = "INSERT INTO packages (title, price_per_sqft, description, features, notes, is_active, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)";
                executeQuery($sql, [$title, $price_per_sqft, $description, $features, $notes, $is_active, $display_order]);
                $success_message = 'Package added successfully!';
            }
            $action = 'list';
        } catch (PDOException $e) {
            error_log('Save Package Error: ' . $e->getMessage());
            $error_message = 'Error saving package.';
        }
    }
}

// Fetch package for editing
$package = null;
if ($action === 'edit' && $package_id) {
    $stmt = executeQuery("SELECT * FROM packages WHERE id = ?", [$package_id]);
    $package = $stmt->fetch();
    if (!$package) {
        $error_message = 'Package not found.';
        $action = 'list';
    }
}

// Fetch all packages for listing
$packages = [];
if ($action === 'list') {
    $packages = executeQuery("SELECT * FROM packages ORDER BY display_order ASC, price_per_sqft ASC")->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Packages</h1>
    <p>Create and manage construction package plans</p>
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
            <h2 class="card-title">All Packages</h2>
            <a href="?action=add" class="btn btn-primary">
                <i data-feather="plus"></i> Add New Package
            </a>
        </div>
        
        <?php if (empty($packages)): ?>
            <p>No packages found. <a href="?action=add">Add your first package</a>.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Title</th>
                            <th>Price/sqft</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($packages as $pkg): ?>
                            <tr>
                                <td><?= sanitizeOutput($pkg['display_order']) ?></td>
                                <td><strong><?= sanitizeOutput($pkg['title']) ?></strong></td>
                                <td>₹<?= number_format((float)$pkg['price_per_sqft'], 2) ?></td>
                                <td>
                                    <?php if ($pkg['is_active']): ?>
                                        <span style="color: var(--admin-success); font-weight: 600;">Active</span>
                                    <?php else: ?>
                                        <span style="color: var(--admin-text-light);">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('M d, Y', strtotime($pkg['created_at'])) ?></td>
                                <td class="table-actions">
                                    <a href="?action=edit&id=<?= $pkg['id'] ?>" class="btn-edit">Edit</a>
                                    <a href="?action=delete&id=<?= $pkg['id'] ?>" class="btn-delete">Delete</a>
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
            <h2 class="card-title"><?= $action === 'edit' ? 'Edit Package' : 'Add New Package' ?></h2>
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
        </div>
        
        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label for="title" class="form-label">Package Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="form-input" 
                           value="<?= $package ? sanitizeOutput($package['title']) : '' ?>" 
                           required 
                           placeholder="e.g., Gold Plan">
                    <p class="form-help">Enter the package name (e.g., Gold, Platinum, Diamond)</p>
                </div>
                
                <div class="form-group">
                    <label for="price_per_sqft" class="form-label">Price per Sqft (₹) *</label>
                    <input type="number" 
                           id="price_per_sqft" 
                           name="price_per_sqft" 
                           class="form-input" 
                           step="0.01" 
                           min="0" 
                           value="<?= $package ? sanitizeOutput($package['price_per_sqft']) : '' ?>" 
                           required 
                           placeholder="1699.00">
                    <p class="form-help">Enter price in rupees per square foot</p>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" 
                          name="description" 
                          class="form-textarea" 
                          rows="3" 
                          placeholder="Brief description of this package..."><?= $package ? sanitizeOutput($package['description']) : '' ?></textarea>
                <p class="form-help">Optional short description for the package</p>
            </div>
            
            <div class="form-group">
                <label for="features" class="form-label">Features *</label>
                <textarea id="features" 
                          name="features" 
                          class="form-textarea" 
                          rows="8" 
                          required 
                          placeholder="Enter features separated by pipe (|)&#10;Example:&#10;Design & Drawings|Structure (Foundation, Columns, Beams)|Flooring (Vitrified Tiles)|Kitchen (Granite Platform)"><?= $package ? sanitizeOutput($package['features']) : '' ?></textarea>
                <p class="form-help">Enter each feature on a new line or separated by pipe (|) symbol</p>
            </div>
            
            <div class="form-group">
                <label for="notes" class="form-label">Notes</label>
                <textarea id="notes" 
                          name="notes" 
                          class="form-textarea" 
                          rows="3" 
                          placeholder="Additional notes or recommendations..."><?= $package ? sanitizeOutput($package['notes']) : '' ?></textarea>
                <p class="form-help">Optional additional information or recommendations</p>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label for="display_order" class="form-label">Display Order</label>
                    <input type="number" 
                           id="display_order" 
                           name="display_order" 
                           class="form-input" 
                           min="0" 
                           value="<?= $package ? sanitizeOutput($package['display_order']) : '0' ?>" 
                           placeholder="0">
                    <p class="form-help">Lower numbers appear first (0 = first)</p>
                </div>
                
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               <?= ($package && $package['is_active']) || !$package ? 'checked' : '' ?>>
                        <span class="form-label" style="margin: 0;">Active (Show on website)</span>
                    </label>
                    <p class="form-help">Uncheck to hide this package from the website</p>
                </div>
            </div>
            
            <div class="btn-group" style="margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i>
                    <?= $action === 'edit' ? 'Update Package' : 'Add Package' ?>
                </button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
