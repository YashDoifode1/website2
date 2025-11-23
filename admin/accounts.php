<?php
/**
 * Admin Accounts Management
 * Change Password, Update Username & Email, Manage Sessions
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireAdminAuth();

$page_title = 'Account Management';
$success_message = '';
$error_message = '';

// ────────────────────── HELPER FUNCTIONS (FIXED & INCLUDED) ──────────────────────
// function getAdminByUsernameOrEmail(string $username, string $email)
// {
//     global $pdo;
//     $stmt = $pdo->prepare("SELECT id, username, email FROM admin_users WHERE username = ? OR email = ? LIMIT 1");
//     $stmt->execute([$username, $email]);
//     return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
// }

// function updateAdminProfile(int $admin_id, string $username, string $email): bool
// {
//     global $pdo;
//     $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, email = ? WHERE id = ?");
//     return $stmt->execute([$username, $email, $admin_id]);
// }
// ─────────────────────────────────────────────────────────────────────────────

// Handle Profile Update (Username + Email)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_username = trim($_POST['username'] ?? '');
    $new_email    = trim(strtolower($_POST['email'] ?? ''));

    if ($new_username === '' || $new_email === '') {
        $error_message = 'Username and email are required.';
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (strlen($new_username) < 3) {
        $error_message = 'Username must be at least 3 characters long.';
    } else {
        $admin_id = (int)$_SESSION['admin_id'];

        $conflict = getAdminByUsernameOrEmail($new_username, $new_email);

        if ($conflict && $conflict['id'] != $admin_id) {
            $error_message = $conflict['username'] === $new_username
                ? 'This username is already taken.'
                : 'This email is already in use.';
        } else {
            if (updateAdminProfile($admin_id, $new_username, $new_email)) {
                $success_message = 'Profile updated successfully.';
                $_SESSION['admin_username'] = $new_username; // Update navbar instantly
                $admin_data = getAdminData($admin_id); // Refresh displayed data
            } else {
                $error_message = 'Failed to update profile.';
            }
        }
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'All password fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'New passwords do not match.';
    } elseif (strlen($new_password) < 8) {
        $error_message = 'New password must be at least 8 characters long.';
    } else {
        $admin_id = (int)$_SESSION['admin_id'];
        
        if (changeAdminPassword($admin_id, $current_password, $new_password)) {
            $success_message = 'Password changed successfully.';
        } else {
            $error_message = 'Current password is incorrect.';
        }
    }
}

// Handle Session Actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'logout_all':
            if (logoutAllSessions($_SESSION['admin_id'])) {
                $success_message = 'All other sessions logged out.';
            } else {
                $error_message = 'Failed to logout other sessions.';
            }
            break;
        case 'delete_session':
            if (isset($_GET['session_id']) && deleteSession($_GET['session_id'])) {
                $success_message = 'Session terminated.';
            } else {
                $error_message = 'Failed to terminate session.';
            }
            break;
    }
    header('Location: accounts.php');
    exit;
}

// Load Data
$admin_data         = getAdminData($_SESSION['admin_id']);
$active_sessions    = getAdminSessions($_SESSION['admin_id']);
$current_session_id = $_SESSION['admin_session_id'] ?? null;

// ────────────────────── HELPER FUNCTIONS (UPDATED) ──────────────────────
function getAdminByUsernameOrEmail(string $username, string $email)
{
    $pdo = getDbConnection();   // this is the correct way

    $stmt = $pdo->prepare("SELECT id, username, email FROM admin_users WHERE username = ? OR email = ? LIMIT 1");
    $stmt->execute([$username, $email]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
}

function updateAdminProfile(int $admin_id, string $username, string $email): bool
{
    $pdo = getDbConnection();

    $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, email = ? WHERE id = ?");
    return $stmt->execute([$username, $email, $admin_id]);
}

require_once 'includes/admin_header.php';
?>

<style>
    .accounts-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .content-header {
        margin-bottom: 2rem;
    }

    .content-header h1 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 700;
        font-size: 2rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .content-header p {
        color: #666;
        font-size: 1.1rem;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 3rem;
    }

    @media (max-width: 968px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }

    .card-header {
        background: #f8f9fa;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header h2 {
        font-family: 'Montserrat', sans-serif;
        font-weight: 600;
        font-size: 1.25rem;
        color: #333;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-body {
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #333;
        font-size: 0.9rem;
    }

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: white;
    }

    .form-input:focus {
        outline: none;
        border-color: #555;
        box-shadow: 0 0 0 3px rgba(85, 85, 85, 0.1);
    }

    .password-strength {
        margin-top: 0.5rem;
        font-size: 0.8rem;
    }

    .strength-weak { color: #dc3545; }
    .strength-medium { color: #fd7e14; }
    .strength-strong { color: #198754; }

    .btn {
        padding: 0.875rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Montserrat', sans-serif;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #555, #666);
        color: white;
        box-shadow: 0 4px 15px rgba(85, 85, 85, 0.3);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #444, #555);
        transform: translateY(-2px);
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .sessions-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .session-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        background: #f8f9fa;
    }

    .session-item.current {
        background: #e7f3ff;
        border-color: #007bff;
    }

    .alert {
        padding: 1rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .alert-success {
        background: #f0f9f0;
        border: 1px solid #d1f0d1;
        color: #2d5a2d;
    }

    .alert-error {
        background: #fdf2f2;
        border: 1px solid #f8d7da;
        color: #721c24;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #555;
    }

    .info-label {
        font-size: 0.875rem;
        color: #666;
    }

    .info-value {
        font-weight: 600;
        color: #333;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #666;
    }
</style>

<div class="accounts-container">
    <div class="content-header">
        <h1>Account Management</h1>
        <p>Update your profile, change password, and manage active sessions</p>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i data-feather="check-circle"></i>
            <span><?= htmlspecialchars($success_message) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-error">
            <i data-feather="alert-circle"></i>
            <span><?= htmlspecialchars($error_message) ?></span>
        </div>
    <?php endif; ?>

    <!-- Account Information -->
    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h2><i data-feather="user"></i> Account Information</h2>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Username</div>
                    <div class="info-value"><?= htmlspecialchars($admin_data['username'] ?? 'N/A') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?= htmlspecialchars($admin_data['email'] ?? 'Not set') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Account Created</div>
                    <div class="info-value"><?= htmlspecialchars($admin_data['created_at'] ?? 'N/A') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Last Password Change</div>
                    <div class="info-value"><?= htmlspecialchars($admin_data['password_changed_at'] ?? 'Never') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Active Sessions</div>
                    <div class="info-value"><?= count($active_sessions) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-grid">

        <!-- Update Profile (Username & Email) -->
        <div class="card">
            <div class="card-header">
                <h2><i data-feather="edit-3"></i> Update Profile</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-input" 
                               value="<?= htmlspecialchars($admin_data['username'] ?? '') ?>" required minlength="3">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-input" 
                               value="<?= htmlspecialchars($admin_data['email'] ?? '') ?>" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="card">
            <div class="card-header">
                <h2><i data-feather="lock"></i> Change Password</h2>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-input" required minlength="8">
                        <div class="password-strength" id="password-strength">Password Strength: Weak</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-input" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Active Sessions -->
    <div class="card">
        <div class="card-header">
            <h2><i data-feather="monitor"></i> Active Sessions</h2>
            <?php if (count($active_sessions) > 1): ?>
                <a href="accounts.php?action=logout_all" class="btn btn-danger btn-sm"
                   onclick="return confirm('Logout all other sessions?')">
                    <i data-feather="log-out"></i> Logout All Others
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <div class="sessions-list">
                <?php if (empty($active_sessions)): ?>
                    <div class="empty-state">
                        <p>No active sessions found.</p>
                    </div>
                <?php else: foreach ($active_sessions as $session): ?>
                    <div class="session-item <?= $session['session_id'] === $current_session_id ? 'current' : '' ?>">
                        <div class="session-info">
                            <strong><?= htmlspecialchars($session['ip_address']) ?>
                                <?= $session['session_id'] === $current_session_id ? '<span style="color:#007bff">(Current)</span>' : '' ?>
                            </strong>
                            <div class="session-meta">
                                <div>Device: <?= htmlspecialchars($session['user_agent']) ?></div>
                                <div>Last Active: <?= htmlspecialchars($session['last_activity']) ?></div>
                            </div>
                        </div>
                        <?php if ($session['session_id'] !== $current_session_id): ?>
                            <a href="accounts.php?action=delete_session&session_id=<?= urlencode($session['session_id']) ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Terminate this session?')">
                                <i data-feather="x-circle"></i> Terminate
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();

        const pwd = document.getElementById('new_password');
        const strength = document.getElementById('password-strength');
        const confirm = document.getElementById('confirm_password');

        if (pwd) {
            pwd.addEventListener('input', () => {
                const len = pwd.value.length;
                const text = len >= 12 ? 'Strong' : len >= 8 ? 'Medium' : 'Weak';
                const cls = len >= 12 ? 'strength-strong' : len >= 8 ? 'strength-medium' : 'strength-weak';
                strength.textContent = `Password Strength: ${text}`;
                strength.className = `password-strength ${cls}`;
            });
        }

        if (confirm && pwd) {
            confirm.addEventListener('input', () => {
                confirm.style.borderColor = confirm.value === pwd.value ? '#198754' : '#dc3545';
            });
        }
    });
</script>

<?php require_once 'includes/admin_footer.php'; ?>