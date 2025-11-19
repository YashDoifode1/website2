<?php
/**
 * Admin Messages Management
 * Uses ORIGINAL CSS + Updated for new schema + selected_plan
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config.php';

requireAdmin();

$page_title = 'Contact Messages';
$success_message = '';
$error_message = '';

$action = $_GET['action'] ?? 'list';
$message_id = $_GET['id'] ?? null;

// Handle Delete
if ($action === 'delete' && $message_id && is_numeric($message_id)) {
    try {
        $stmt = executeQuery("DELETE FROM contact_messages WHERE id = ?", [$message_id]);
        if ($stmt->rowCount() > 0) {
            $success_message = 'Message deleted successfully!';
        } else {
            $error_message = 'Message not found or already deleted.';
        }
        $action = 'list';
    } catch (PDOException $e) {
        error_log('Delete Message Error: ' . $e->getMessage());
        $error_message = 'Database error while deleting message.';
    }
}

// Fetch single message for view
$message = null;
if ($action === 'view' && $message_id && is_numeric($message_id)) {
    $stmt = executeQuery("
        SELECT *, 
               CONCAT(first_name, ' ', last_name) AS full_name 
        FROM contact_messages 
        WHERE id = ?
    ", [$message_id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$message) {
        $error_message = 'Message not found.';
        $action = 'list';
    }
}

// Fetch all messages for list
$messages = [];
if ($action === 'list') {
    $messages = executeQuery("
        SELECT 
            id, 
            first_name, 
            last_name,
            email, 
            phone, 
            project_type, 
            budget,
            selected_plan,
            message, 
            submitted_at,
            ip_address
        FROM contact_messages 
        ORDER BY submitted_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<style>
/* Search and scrollbar styles - keeping original design intact */
.search-container {
    margin-bottom: 1.5rem;
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    flex: 1;
    max-width: 400px;
}

.search-box input {
    width: 100%;
    padding: 10px 40px 10px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.table-container {
    max-height: 600px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.table-container table {
    margin-bottom: 0;
}

/* Custom scrollbar styling */
.table-container::-webkit-scrollbar {
    width: 8px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 0 8px 8px 0;
}

.table-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.search-stats {
    color: #666;
    font-size: 14px;
    margin-left: auto;
}

.clear-search {
    background: #6b7280;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
}

.clear-search:hover {
    background: #4b5563;
}

@media (max-width: 768px) {
    .search-container {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        max-width: 100%;
    }
    
    .search-stats {
        margin-left: 0;
        text-align: center;
    }
}
</style>

<div class="content-header">
    <h1>Contact Messages</h1>
    <p>View and manage all customer inquiries from the contact form</p>
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
            <div class="p-4 text-center text-muted">
                <i data-feather="inbox" style="width: 48px; height: 48px; opacity: 0.3;"></i>
                <p class="mt-3">No messages yet. New inquiries will appear here.</p>
            </div>
        <?php else: ?>
            <!-- Search Box -->
            <div class="search-container p-4 border-bottom">
                <div class="search-box">
                    <input type="text" id="messageSearch" placeholder="Search messages by name, email, phone, project type, or message content...">
                    <span class="search-icon">
                        <i data-feather="search"></i>
                    </span>
                </div>
                <div class="search-stats">
                    <span id="searchResultsCount"><?= count($messages) ?></span> messages found
                </div>
                <button type="button" class="clear-search" id="clearSearch" style="display: none;">
                    Clear Search
                </button>
            </div>

            <div class="table-container">
                <table class="admin-table" id="messagesTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Project Type</th>
                            <th>Budget</th>
                            <th>Plan</th>
                            <th>Message Preview</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): 
                            $full_name = trim($msg['first_name'] . ' ' . $msg['last_name']);
                        ?>
                            <tr class="message-row">
                                <td data-label="Date">
                                    <?= date('M d, Y', strtotime($msg['submitted_at'])) ?><br>
                                    <small class="text-muted"><?= date('H:i', strtotime($msg['submitted_at'])) ?></small>
                                </td>
                                <td data-label="Name" class="searchable-name">
                                    <strong><?= sanitizeOutput($full_name) ?></strong>
                                </td>
                                <td data-label="Email" class="searchable-email">
                                    <a href="mailto:<?= sanitizeOutput($msg['email']) ?>" class="text-primary">
                                        <?= sanitizeOutput($msg['email']) ?>
                                    </a>
                                </td>
                                <td data-label="Phone" class="searchable-phone">
                                    <a href="tel:<?= sanitizeOutput($msg['phone']) ?>" class="text-primary">
                                        <?= sanitizeOutput($msg['phone']) ?>
                                    </a>
                                </td>
                                <td data-label="Project Type" class="searchable-project">
                                    <?= $msg['project_type'] ? sanitizeOutput($msg['project_type']) : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td data-label="Budget">
                                    <?= $msg['budget'] ? sanitizeOutput($msg['budget']) : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td data-label="Plan">
                                    <?= $msg['selected_plan'] ? '<strong>' . sanitizeOutput($msg['selected_plan']) . '</strong>' : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td data-label="Message" class="searchable-message">
                                    <?= sanitizeOutput(substr(strip_tags($msg['message']), 0, 60)) ?>
                                    <?= strlen(strip_tags($msg['message'])) > 60 ? '...' : '' ?>
                                </td>
                                <td class="table-actions">
                                    <a href="?action=view&id=<?= $msg['id'] ?>" class="btn-edit" title="View">
                                        <i data-feather="eye"></i>
                                    </a>
                                    <a href="?action=delete&id=<?= $msg['id'] ?>" 
                                       class="btn-delete" 
                                       title="Delete"
                                       onclick="return confirm('Delete this message permanently?')">
                                        <i data-feather="trash-2"></i>
                                    </a>
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="card-title mb-0">Message Details</h2>
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
        </div>

        <div class="p-4 bg-light rounded mb-4">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <small class="text-muted">Full Name</small>
                    <p class="fw-bold mb-0">
                        <?= sanitizeOutput($message['full_name']) ?>
                    </p>
                </div>
                <div class="col-md-6 col-lg-3">
                    <small class="text-muted">Email</small>
                    <p class="mb-0">
                        <a href="mailto:<?= sanitizeOutput($message['email']) ?>" class="text-primary fw-bold">
                            <?= sanitizeOutput($message['email']) ?>
                        </a>
                    </p>
                </div>
                <div class="col-md-6 col-lg-3">
                    <small class="text-muted">Phone</small>
                    <p class="mb-0">
                        <a href="tel:<?= sanitizeOutput($message['phone']) ?>" class="text-primary fw-bold">
                            <?= sanitizeOutput($message['phone']) ?>
                        </a>
                    </p>
                </div>
                <div class="col-md-6 col-lg-3">
                    <small class="text-muted">Submitted</small>
                    <p class="mb-0 fw-bold">
                        <?= date('F d, Y \a\t H:i', strtotime($message['submitted_at'])) ?>
                    </p>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <?php if ($message['project_type']): ?>
                <div class="col-md-6 col-lg-3">
                    <small class="text-muted">Project Type</small>
                    <p class="mb-0">
                        <span class="badge bg-warning text-dark"><?= sanitizeOutput($message['project_type']) ?></span>
                    </p>
                </div>
                <?php endif; ?>
                <?php if ($message['budget']): ?>
                <div class="col-md-6 col-lg-3">
                    <small class="text-muted">Budget Range</small>
                    <p class="mb-0">
                        <span class="badge bg-success text-white"><?= sanitizeOutput($message['budget']) ?></span>
                    </p>
                </div>
                <?php endif; ?>
                <?php if ($message['selected_plan']): ?>
                <div class="col-md-6 col-lg-3">
                    <small class="text-muted">Selected Plan</small>
                    <p class="mb-0">
                        <span class="badge bg-primary text-white"><?= sanitizeOutput($message['selected_plan']) ?></span>
                    </p>
                </div>
                <?php endif; ?>
                <?php if ($message['ip_address']): ?>
                <div class="col-md-6 col-lg-3">
                    <small class="text-muted">IP Address</small>
                    <p class="mb-0 text-muted small">
                        <?= sanitizeOutput($message['ip_address']) ?>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-4">
            <h3 class="h5 text-dark mb-3">Full Message:</h3>
            <div class="bg-white p-4 border rounded" style="line-height: 1.8; white-space: pre-wrap;">
                <?= nl2br(sanitizeOutput($message['message'])) ?>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="mailto:<?= sanitizeOutput($message['email']) ?>?subject=Re:%20Your%20Inquiry%20at%20Grand%20Jyothi" 
               class="btn btn-primary">
                <i data-feather="mail"></i> Reply via Email
            </a>
            <a href="?action=list" class="btn btn-secondary">
                <i data-feather="list"></i> All Messages
            </a>
            <a href="?action=delete&id=<?= $message['id'] ?>" 
               class="btn btn-danger"
               onclick="return confirm('Permanently delete this message?')">
                <i data-feather="trash-2"></i> Delete
            </a>
        </div>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('messageSearch');
    const clearSearchBtn = document.getElementById('clearSearch');
    const searchResultsCount = document.getElementById('searchResultsCount');
    const messageRows = document.querySelectorAll('.message-row');
    const totalMessages = <?= count($messages) ?>;
    
    if (searchInput) {
        // Search functionality
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            messageRows.forEach(row => {
                const name = row.querySelector('.searchable-name').textContent.toLowerCase();
                const email = row.querySelector('.searchable-email').textContent.toLowerCase();
                const phone = row.querySelector('.searchable-phone').textContent.toLowerCase();
                const project = row.querySelector('.searchable-project').textContent.toLowerCase();
                const message = row.querySelector('.searchable-message').textContent.toLowerCase();
                
                const matches = name.includes(searchTerm) || 
                              email.includes(searchTerm) || 
                              phone.includes(searchTerm) || 
                              project.includes(searchTerm) || 
                              message.includes(searchTerm);
                
                row.style.display = matches ? '' : 'none';
                if (matches) visibleCount++;
            });
            
            // Update results count
            searchResultsCount.textContent = visibleCount;
            
            // Show/hide clear button
            clearSearchBtn.style.display = searchTerm ? 'block' : 'none';
        });
        
        // Clear search functionality
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
        });
        
        // Focus search input on page load
        searchInput.focus();
    }
    
    // Initialize Feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>