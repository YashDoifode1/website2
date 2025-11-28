<?php
/**
 * Admin Users Management
 * CRUD for admin_users (limited editable fields for safety)
 *
 * Assumptions:
 * - executeQuery($sql, $params = []) returns a PDOStatement (as in your project)
 * - sanitizeOutput($str) exists for safe HTML output
 * - requireAdmin() checks admin session
 * - includes/admin_header.php and includes/admin_footer.php exist and match your theme
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config.php';

requireAdmin();

$page_title = 'Manage Admin Users';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// -------------------------
// Helpers
// -------------------------
function uniqueUsernameEmail(int $excludeId = 0, string $username, string $email): array
{
    $stmt = executeQuery(
        "SELECT id, username, email FROM admin_users WHERE (username = ? OR email = ?) " . ($excludeId ? "AND id != ?" : ""),
        $excludeId ? [$username, $email, $excludeId] : [$username, $email]
    );
    $rows = $stmt->fetchAll();
    $res = ['username_conflict' => false, 'email_conflict' => false];
    foreach ($rows as $r) {
        if (strcasecmp($r['username'], $username) === 0) $res['username_conflict'] = true;
        if (strcasecmp($r['email'], $email) === 0) $res['email_conflict'] = true;
    }
    return $res;
}

// -------------------------
// Handle Delete
// -------------------------
if ($action === 'delete' && $user_id) {
    try {
        // Optional: prevent deleting the currently logged-in admin (assuming $_SESSION['admin_id'])
        if (!empty($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] === $user_id) {
            $error_message = 'You cannot delete your own admin account while logged in.';
        } else {
            executeQuery("DELETE FROM admin_users WHERE id = ?", [$user_id]);
            $success_message = 'Admin user deleted successfully.';
        }
    } catch (PDOException $e) {
        error_log('Delete Admin User Error: ' . $e->getMessage());
        $error_message = 'Error deleting admin user.';
    }
    $action = 'list';
}

// -------------------------
// Handle Add/Edit Submission
// -------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email)) {
        $error_message = 'Please fill in username and email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please provide a valid email address.';
    } elseif (!empty($new_password) && ($new_password !== $confirm_password)) {
        $error_message = 'Passwords do not match.';
    } else {
        try {
            // check uniqueness
            $excludeId = ($action === 'edit' && !empty($_POST['id'])) ? (int)$_POST['id'] : 0;
            $conflicts = uniqueUsernameEmail($excludeId, $username, $email);
            if ($conflicts['username_conflict']) {
                $error_message = 'Username is already taken.';
            } elseif ($conflicts['email_conflict']) {
                $error_message = 'Email is already in use.';
            } else {
                if ($action === 'edit' && $excludeId) {
                    // Build update with optional password
                    $params = [
                        $username,
                        $email
                    ];
                    $sql = "UPDATE admin_users SET username = ?, email = ?, updated_at = NOW()";

                    if (!empty($new_password)) {
                        $hash = password_hash($new_password, PASSWORD_DEFAULT);
                        $sql .= ", password_hash = ?, password_changed_at = NOW(), failed_attempts = 0, locked_until = NULL";
                        $params[] = $hash;
                    }

                    $sql .= " WHERE id = ?";
                    $params[] = $excludeId;

                    executeQuery($sql, $params);
                    $success_message = 'Admin user updated successfully.';
                    $action = 'list';
                } else {
                    // INSERT new user
                    if (empty($new_password)) {
                        $error_message = 'Please provide a password for the new user.';
                    } else {
                        $hash = password_hash($new_password, PASSWORD_DEFAULT);

                        $sql = "INSERT INTO admin_users 
                                (username, email, password_hash, created_at, password_changed_at, failed_attempts)
                                VALUES (?, ?, ?, NOW(), NOW(), 0)";
                        executeQuery($sql, [$username, $email, $hash]);

                        $success_message = 'Admin user added successfully.';
                        $action = 'list';
                    }
                }
            }
        } catch (PDOException $e) {
            error_log('Save Admin User Error: ' . $e->getMessage());
            $error_message = 'Error saving admin user.';
        }
    }
}

// -------------------------
// Fetch user for editing
// -------------------------
$user = null;
if ($action === 'edit' && $user_id) {
    $stmt = executeQuery("SELECT id, username, email, created_at, password_changed_at, failed_attempts, locked_until FROM admin_users WHERE id = ?", [$user_id]);
    $user = $stmt->fetch();
    if (!$user) {
        $error_message = 'Admin user not found.';
        $action = 'list';
    }
}

// -------------------------
// Fetch all users (list)
// -------------------------
$users = [];
if ($action === 'list') {
    // optional: add search
    $search = trim($_GET['q'] ?? '');
    if ($search !== '') {
        $like = '%' . $search . '%';
        $stmt = executeQuery("SELECT id, username, email, created_at, password_changed_at, failed_attempts, locked_until FROM admin_users WHERE username LIKE ? OR email LIKE ? ORDER BY created_at DESC", [$like, $like]);
    } else {
        $stmt = executeQuery("SELECT id, username, email, created_at, password_changed_at, failed_attempts, locked_until FROM admin_users ORDER BY created_at DESC");
    }
    $users = $stmt->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Manage Admin Users</h1>
    <p>Create and manage administrator accounts</p>
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
        <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
            <div>
                <h2 class="card-title">All Admin Users</h2>
                <p class="card-sub">Manage login accounts for site admins</p>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <form method="GET" style="margin:0;">
                    <input type="hidden" name="action" value="list">
                    <input type="text" name="q" class="form-input" placeholder="Search username or email" value="<?= sanitizeOutput($_GET['q'] ?? '') ?>" style="width:220px;">
                    <button type="submit" class="btn">Search</button>
                    <a href="?action=list" class="btn btn-secondary">Clear</a>
                </form>
                <a href="?action=add" class="btn btn-primary">
                    <i data-feather="plus"></i> Add New Admin
                </a>
            </div>
        </div>

        <?php if (empty($users)): ?>
            <p>No admin users found. <a href="?action=add">Add your first admin</a>.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Created</th>
                            <th>Password Changed</th>
                            <th>Failed Attempts</th>
                            <th>Locked Until</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><strong><?= sanitizeOutput($u['username']) ?></strong></td>
                            <td><?= sanitizeOutput($u['email']) ?></td>
                            <td><?= !empty($u['created_at']) ? date('Y-m-d H:i', strtotime($u['created_at'])) : '—' ?></td>
                            <td><?= !empty($u['password_changed_at']) ? date('Y-m-d H:i', strtotime($u['password_changed_at'])) : '—' ?></td>
                            <td><?= is_null($u['failed_attempts']) ? '0' : (int)$u['failed_attempts'] ?></td>
                            <td><?= !empty($u['locked_until']) ? date('Y-m-d H:i', strtotime($u['locked_until'])) : '—' ?></td>
                            <td class="table-actions">
                                <a href="?action=edit&id=<?= (int)$u['id'] ?>" class="btn-edit">Edit</a>

                                <?php if (!empty($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] === (int)$u['id']): ?>
                                    <!-- Prevent self-delete while logged in -->
                                    <a href="#" class="btn btn-disabled" title="Cannot delete logged-in user">Delete</a>
                                <?php else: ?>
                                    <a href="?action=delete&id=<?= (int)$u['id'] ?>"
                                       class="btn-delete"
                                       onclick="return confirm('Delete this admin user? This action cannot be undone.');">Delete</a>
                                <?php endif; ?>

                                <a href="?action=reset_password&id=<?= (int)$u['id'] ?>" class="btn btn-info btn-sm">Reset Password</a>
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
            <h2 class="card-title"><?= $action === 'edit' ? 'Edit Admin User' : 'Add New Admin' ?></h2>
            <a href="?action=list" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back</a>
        </div>

        <form method="POST" novalidate>
            <?php if ($action === 'edit'): ?>
                <input type="hidden" name="id" value="<?= (int)$user['id'] ?>">
            <?php endif; ?>

            <div class="form-grid">
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-input" required
                           value="<?= sanitizeOutput($user['username'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-input" required
                           value="<?= sanitizeOutput($user['email'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label><?= $action === 'edit' ? 'Set New Password (leave empty to keep current password)' : 'Password *' ?></label>
                <input type="password" name="password" class="form-input" <?= $action === 'add' ? 'required' : '' ?>>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-input" <?= $action === 'add' ? 'required' : '' ?>>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Created At</label>
                    <input type="text" class="form-input" disabled value="<?= isset($user['created_at']) ? date('Y-m-d H:i', strtotime($user['created_at'])) : '—' ?>">
                </div>

                <div class="form-group">
                    <label>Last Password Change</label>
                    <input type="text" class="form-input" disabled value="<?= isset($user['password_changed_at']) ? date('Y-m-d H:i', strtotime($user['password_changed_at'])) : '—' ?>">
                </div>
            </div>

            <div class="btn-group" style="margin-top: 20px;">
                <button type="submit" class="btn btn-primary">
                    <i data-feather="save"></i>
                    <?= $action === 'edit' ? 'Update Admin' : 'Add Admin' ?>
                </button>
                <a href="?action=list" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

<?php elseif ($action === 'reset_password' && $user_id): ?>

    <?php
    // Fetch target user to ensure exists
    $stmt = executeQuery("SELECT id, username, email FROM admin_users WHERE id = ?", [$user_id]);
    $target = $stmt->fetch();
    if (!$target) {
        $error_message = 'Admin user not found.';
        $action = 'list';
    } else {
        // If posted, process password reset
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($new_password) || $new_password !== $confirm_password) {
                $error_message = 'Please provide and confirm the new password (they must match).';
            } else {
                try {
                    $hash = password_hash($new_password, PASSWORD_DEFAULT);
                    executeQuery("UPDATE admin_users SET password_hash = ?, password_changed_at = NOW(), failed_attempts = 0, locked_until = NULL WHERE id = ?", [$hash, $user_id]);
                    $success_message = 'Password reset successfully.';
                    $action = 'list';
                } catch (PDOException $e) {
                    error_log('Reset Admin Password Error: ' . $e->getMessage());
                    $error_message = 'Error resetting password.';
                }
            }
        }
    }
    ?>

    <?php if (!isset($target)): ?>
        <div class="card">
            <p>Admin user not found.</p>
            <a href="?action=list" class="btn btn-secondary">Back</a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Reset Password for <?= sanitizeOutput($target['username']) ?></h2>
                <a href="?action=list" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back</a>
            </div>

            <form method="POST" novalidate>
                <div class="form-group">
                    <label>New Password *</label>
                    <input type="password" name="password" class="form-input" required>
                </div>

                <div class="form-group">
                    <label>Confirm New Password *</label>
                    <input type="password" name="confirm_password" class="form-input" required>
                </div>

                <div class="btn-group" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary"><i data-feather="save"></i> Reset Password</button>
                    <a href="?action=list" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
