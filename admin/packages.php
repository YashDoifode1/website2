<?php
/**
 * Admin Packages Management
 * 
 * CRUD operations for construction packages + package_sections
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config.php';

requireAdmin();

$page_title = 'Manage Packages';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$package_id = $_GET['id'] ?? null;

// Handle Delete (package + sections)
if ($action === 'delete' && $package_id) {
    try {
        executeQuery("DELETE FROM package_sections WHERE package_id = ?", [$package_id]);
        executeQuery("DELETE FROM packages WHERE id = ?", [$package_id]);
        $success_message = 'Package and its sections deleted successfully!';
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

    if (empty($title) || empty($price_per_sqft)) {
        $error_message = 'Please fill in all required fields (Title, Price).';
    } elseif (!is_numeric($price_per_sqft) || $price_per_sqft <= 0) {
        $error_message = 'Please enter a valid price per sqft.';
    } else {
        try {
            if ($action === 'edit' && $package_id) {
                executeQuery(
                    "UPDATE packages SET title=?, price_per_sqft=?, description=?, features=?, notes=?, is_active=?, display_order=? WHERE id=?",
                    [$title, $price_per_sqft, $description, $features, $notes, $is_active, $display_order, $package_id]
                );
                $success_message = 'Package updated successfully!';
            } else {
                executeQuery(
                    "INSERT INTO packages (title, price_per_sqft, description, features, notes, is_active, display_order)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [$title, $price_per_sqft, $description, $features, $notes, $is_active, $display_order]
                );
                $package_id = getPDO()->lastInsertId();
                $success_message = 'Package added successfully!';
            }

            // Handle Package Sections
            if (!empty($_POST['section_title'])) {
                executeQuery("DELETE FROM package_sections WHERE package_id = ?", [$package_id]); // simple replace approach
                foreach ($_POST['section_title'] as $i => $stitle) {
                    $stitle = trim($stitle);
                    $scontent = trim($_POST['section_content'][$i] ?? '');
                    $sactive = isset($_POST['section_active'][$i]) ? 1 : 0;
                    $sorder = intval($_POST['section_order'][$i] ?? 0);
                    if ($stitle !== '') {
                        executeQuery(
                            "INSERT INTO package_sections (package_id, title, content, display_order, is_active)
                             VALUES (?, ?, ?, ?, ?)",
                            [$package_id, $stitle, $scontent, $sorder, $sactive]
                        );
                    }
                }
            }

            $action = 'list';
        } catch (PDOException $e) {
            error_log('Save Package Error: ' . $e->getMessage());
            $error_message = 'Error saving package or sections.';
        }
    }
}

// Fetch package for editing
$package = null;
$sections = [];
if ($action === 'edit' && $package_id) {
    $stmt = executeQuery("SELECT * FROM packages WHERE id = ?", [$package_id]);
    $package = $stmt->fetch();

    if ($package) {
        $sections = executeQuery(
            "SELECT * FROM package_sections WHERE package_id = ? ORDER BY display_order ASC",
            [$package_id]
        )->fetchAll();
    } else {
        $error_message = 'Package not found.';
        $action = 'list';
    }
}

// Fetch all packages for listing
if ($action === 'list') {
    $packages = executeQuery("SELECT * FROM packages ORDER BY display_order ASC, price_per_sqft ASC")->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Packages</h1>
    <p>Control all construction packages and their detailed sections</p>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success"><?= sanitizeOutput($success_message) ?></div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error"><?= sanitizeOutput($error_message) ?></div>
<?php endif; ?>


<?php if ($action === 'list'): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">All Packages</h2>
            <a href="?action=add" class="btn btn-primary"><i data-feather="plus"></i> Add New Package</a>
        </div>
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
                            <td><?= (int)$pkg['display_order'] ?></td>
                            <td><strong><?= sanitizeOutput($pkg['title']) ?></strong></td>
                            <td>₹<?= number_format((float)$pkg['price_per_sqft'], 2) ?></td>
                            <td><?= $pkg['is_active'] ? '<span class="text-success">Active</span>' : 'Inactive' ?></td>
                            <td><?= date('M d, Y', strtotime($pkg['created_at'])) ?></td>
                            <td class="table-actions">
                                <a href="?action=edit&id=<?= $pkg['id'] ?>" class="btn-edit">Edit</a>
                                <a href="?action=delete&id=<?= $pkg['id'] ?>" class="btn-delete" 
                                   onclick="return confirm('Delete this package and all its sections?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?= $action === 'edit' ? 'Edit Package' : 'Add New Package' ?></h2>
            <a href="?action=list" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back</a>
        </div>

        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label>Package Title *</label>
                    <input type="text" name="title" class="form-input" required
                           value="<?= $package ? sanitizeOutput($package['title']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Price per Sqft (₹) *</label>
                    <input type="number" step="0.01" name="price_per_sqft" class="form-input" required
                           value="<?= $package ? sanitizeOutput($package['price_per_sqft']) : '' ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" class="form-textarea"><?= $package ? sanitizeOutput($package['description']) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label>Features (for comparison)</label>
                <textarea name="features" rows="4" class="form-textarea"><?= $package ? sanitizeOutput($package['features']) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3" class="form-textarea"><?= $package ? sanitizeOutput($package['notes']) : '' ?></textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Display Order</label>
                    <input type="number" name="display_order" class="form-input" value="<?= $package ? (int)$package['display_order'] : 0 ?>">
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_active" <?= ($package && $package['is_active']) || !$package ? 'checked' : '' ?>> Active</label>
                </div>
            </div>

            <hr>
            <h3 style="margin-top:1.5rem;">Package Sections (Accordion Details)</h3>
            <p class="form-help">Each section will appear as a dropdown accordion under this package on the website.</p>

            <div id="sections-container">
                <?php if (!empty($sections)): foreach ($sections as $i => $sec): ?>
                    <div class="section-block border p-3 mb-3 rounded">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Section Title</label>
                                <input type="text" name="section_title[]" class="form-input" value="<?= sanitizeOutput($sec['title']) ?>">
                            </div>
                            <div class="form-group">
                                <label>Display Order</label>
                                <input type="number" name="section_order[]" class="form-input" value="<?= (int)$sec['display_order'] ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Section Content (HTML allowed)</label>
                            <textarea name="section_content[]" class="form-textarea" rows="4"><?= htmlspecialchars($sec['content']) ?></textarea>
                        </div>
                        <label><input type="checkbox" name="section_active[<?= $i ?>]" <?= $sec['is_active'] ? 'checked' : '' ?>> Active</label>
                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-section">Remove Section</button>
                    </div>
                <?php endforeach; endif; ?>
            </div>

            <button type="button" id="add-section" class="btn btn-outline-primary mt-2">
                <i data-feather="plus-circle"></i> Add Section
            </button>

            <div class="btn-group mt-4">
                <button type="submit" class="btn btn-primary"><?= $action === 'edit' ? 'Update Package' : 'Add Package' ?></button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<script>
document.getElementById('add-section')?.addEventListener('click', function() {
    const container = document.getElementById('sections-container');
    const index = container.children.length;
    const block = document.createElement('div');
    block.className = 'section-block border p-3 mb-3 rounded';
    block.innerHTML = `
        <div class="form-grid">
            <div class="form-group">
                <label>Section Title</label>
                <input type="text" name="section_title[]" class="form-input" required>
            </div>
            <div class="form-group">
                <label>Display Order</label>
                <input type="number" name="section_order[]" class="form-input" value="0">
            </div>
        </div>
        <div class="form-group">
            <label>Section Content</label>
            <textarea name="section_content[]" class="form-textarea" rows="4"></textarea>
        </div>
        <label><input type="checkbox" name="section_active[${index}]" checked> Active</label>
        <button type="button" class="btn btn-danger btn-sm mt-2 remove-section">Remove Section</button>
    `;
    container.appendChild(block);
});

document.addEventListener('click', e => {
    if (e.target.classList.contains('remove-section')) {
        e.target.closest('.section-block').remove();
    }
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
