<?php
/**
 * Admin Messages Management
 * 
 * View and delete contact form messages
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

$page_title = 'Contact Messages';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$message_id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $message_id) {
    try {
        executeQuery("DELETE FROM contact_messages WHERE id = ?", [$message_id]);
        $success_message = 'Message deleted successfully!';
        $action = 'list';
    } catch (PDOException $e) {
        error_log('Delete Message Error: ' . $e->getMessage());
        $error_message = 'Error deleting message.';
    }
}

// Fetch message for viewing
$message = null;
if ($action === 'view' && $message_id) {
    $stmt = executeQuery("SELECT * FROM contact_messages WHERE id = ?", [$message_id]);
    $message = $stmt->fetch();
    if (!$message) {
        $error_message = 'Message not found.';
        $action = 'list';
    }
}

// Fetch all messages for listing
$messages = [];
if ($action === 'list') {
    $messages = executeQuery("SELECT id, name, email, phone, message, selected_plan, created_at FROM contact_messages ORDER BY created_at DESC")->fetchAll();
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Contact Messages</h1>
    <p>View and manage customer inquiries and contact form submissions</p>
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
            <h2 class="card-title">All Messages (<?= count($messages) ?>)</h2>
        </div>
        
        <?php if (empty($messages)): ?>
            <p>No messages yet. Messages from the contact form will appear here.</p>
        <?php else: ?>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Selected Plan</th>
                            <th>Message Preview</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr>
                                <td><?= date('M d, Y H:i', strtotime($msg['created_at'])) ?></td>
                                <td><strong><?= sanitizeOutput($msg['name']) ?></strong></td>
                                <td>
                                    <a href="mailto:<?= sanitizeOutput($msg['email']) ?>" style="color: var(--admin-primary);">
                                        <?= sanitizeOutput($msg['email']) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:<?= sanitizeOutput($msg['phone']) ?>" style="color: var(--admin-primary);">
                                        <?= sanitizeOutput($msg['phone']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($msg['selected_plan']): ?>
                                        <span style="background: var(--admin-primary); color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 500;">
                                            <?= sanitizeOutput($msg['selected_plan']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--admin-text-gray);">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= sanitizeOutput(substr($msg['message'], 0, 50)) ?>...</td>
                                <td class="table-actions">
                                    <a href="?action=view&id=<?= $msg['id'] ?>" class="btn-edit">View</a>
                                    <a href="?action=delete&id=<?= $msg['id'] ?>" class="btn-delete">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
        
    
<?php elseif ($action === 'view' && $message): ?>
    <!-- View Message -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Message Details</h2>
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
        </div>
        
        <div style="background: var(--admin-bg-light); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <p style="color: var(--admin-text-gray); font-size: var(--font-size-sm); margin-bottom: 0.25rem;">From</p>
                    <p style="font-weight: 600; color: var(--admin-text-dark);"><?= sanitizeOutput($message['name']) ?></p>
                </div>
                <div>
                    <p style="color: var(--admin-text-gray); font-size: var(--font-size-sm); margin-bottom: 0.25rem;">Email</p>
                    <p><a href="mailto:<?= sanitizeOutput($message['email']) ?>" style="color: var(--admin-primary); font-weight: 600;"><?= sanitizeOutput($message['email']) ?></a></p>
                </div>
                <div>
                    <p style="color: var(--admin-text-gray); font-size: var(--font-size-sm); margin-bottom: 0.25rem;">Phone</p>
                    <p><a href="tel:<?= sanitizeOutput($message['phone']) ?>" style="color: var(--admin-primary); font-weight: 600;"><?= sanitizeOutput($message['phone']) ?></a></p>
                </div>
                <div>
                    <p style="color: var(--admin-text-gray); font-size: var(--font-size-sm); margin-bottom: 0.25rem;">Date</p>
                    <p style="font-weight: 600; color: var(--admin-text-dark);"><?= date('F d, Y \a\t H:i', strtotime($message['created_at'])) ?></p>
                </div>
                <?php if ($message['selected_plan']): ?>
                <div>
                    <p style="color: var(--admin-text-gray); font-size: var(--font-size-sm); margin-bottom: 0.25rem;">Selected Plan</p>
                    <p style="background: var(--admin-primary); color: white; padding: 0.5rem 1rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600; display: inline-block;">
                        <?= sanitizeOutput($message['selected_plan']) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <h3 style="font-size: var(--font-size-lg); margin-bottom: 1rem; color: var(--admin-text-dark);">Message:</h3>
            <div style="background: var(--admin-bg-white); padding: 1.5rem; border: 1px solid var(--admin-border); border-radius: var(--radius-md); line-height: 1.8;">
                <?= nl2br(sanitizeOutput($message['message'])) ?>
            </div>
        </div>
        
        <div class="btn-group">
            <a href="mailto:<?= sanitizeOutput($message['email']) ?>?subject=Re: Your Inquiry" class="btn btn-primary">
                <i data-feather="mail"></i> Reply via Email
            </a>
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
            <a href="?action=delete&id=<?= $message['id'] ?>" class="btn btn-danger">
                <i data-feather="trash-2"></i> Delete Message
            </a>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
