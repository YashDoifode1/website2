<?php
/**
 * Admin Mail System
 * 
 * Send email notifications to users
 */

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/mailer.php';
require_once __DIR__ . '/includes/auth.php';

requireAdmin();

$page_title = 'Email Notifications';
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'send_email') {
        $emailType = $_POST['email_type'] ?? '';
        $recipients = $_POST['recipients'] ?? [];
        $subject = trim($_POST['subject'] ?? '');
        $customMessage = trim($_POST['custom_message'] ?? '');
        
        // Validate
        if (empty($recipients)) {
            $error_message = 'Please select at least one recipient.';
        } elseif (empty($subject)) {
            $error_message = 'Please enter an email subject.';
        } else {
            // Get recipient emails
            $recipientEmails = [];
            
            if (in_array('all_contacts', $recipients)) {
                // Get all contact emails
                $sql = "SELECT DISTINCT email FROM contact_messages WHERE email IS NOT NULL AND email != ''";
                $stmt = executeQuery($sql);
                $contacts = $stmt->fetchAll();
                foreach ($contacts as $contact) {
                    $recipientEmails[] = $contact['email'];
                }
            } else {
                // Get selected emails
                $placeholders = str_repeat('?,', count($recipients) - 1) . '?';
                $sql = "SELECT DISTINCT email FROM contact_messages WHERE id IN ($placeholders)";
                $stmt = executeQuery($sql, $recipients);
                $contacts = $stmt->fetchAll();
                foreach ($contacts as $contact) {
                    $recipientEmails[] = $contact['email'];
                }
            }
            
            if (empty($recipientEmails)) {
                $error_message = 'No valid email addresses found.';
            } else {
                // Prepare email content based on type
                $emailContent = '';
                
                if ($emailType === 'blog_notification' && !empty($_POST['blog_id'])) {
                    // Get blog details
                    try {
                        $blogSql = "SELECT * FROM blog_posts WHERE id = ?";
                        $blogStmt = executeQuery($blogSql, [$_POST['blog_id']]);
                        $blog = $blogStmt->fetch();
                        
                        if ($blog) {
                            $emailContent = getEmailTemplate('blog_notification', [
                                'site_name' => SITE_NAME,
                                'title' => $blog['title'],
                                'excerpt' => substr(strip_tags($blog['content']), 0, 200) . '...',
                                'link' => SITE_URL . '/blog-detail.php?id=' . $blog['id']
                            ]);
                        }
                    } catch (PDOException $e) {
                        $error_message = 'Blog posts table not available. Please use Custom Notification instead.';
                    }
                } elseif ($emailType === 'construction_update') {
                    $projectName = $_POST['project_name'] ?? 'N/A';
                    $status = $_POST['project_status'] ?? 'In Progress';
                    
                    $emailContent = getEmailTemplate('construction_update', [
                        'site_name' => SITE_NAME,
                        'title' => $subject,
                        'message' => nl2br($customMessage),
                        'project_name' => $projectName,
                        'status' => $status,
                        'contact_email' => CONTACT_EMAIL,
                        'contact_phone' => CONTACT_PHONE
                    ]);
                } else {
                    // Custom notification
                    $emailContent = getEmailTemplate('custom_notification', [
                        'site_name' => SITE_NAME,
                        'subtitle' => 'Important Update',
                        'title' => $subject,
                        'content' => nl2br($customMessage),
                        'footer_text' => 'Thank you for being a valued client of ' . SITE_NAME
                    ]);
                }
                
                // Send emails
                $results = sendBulkEmails($recipientEmails, $subject, $emailContent);
                
                // Log activity
                foreach ($recipientEmails as $email) {
                    logEmailActivity($email, $subject, true);
                }
                
                $success_message = sprintf(
                    'Emails sent successfully! Success: %d, Failed: %d',
                    $results['success'],
                    $results['failed']
                );
                
                if ($results['failed'] > 0) {
                    $error_message = 'Some emails failed to send: ' . implode(', ', $results['errors']);
                }
            }
        }
    }
}

// Fetch contacts for recipient selection
$contacts_sql = "SELECT id, name, email, created_at FROM contact_messages ORDER BY created_at DESC";
$contacts_stmt = executeQuery($contacts_sql);
$contacts = $contacts_stmt->fetchAll();

// Fetch recent blog posts (if table exists)
$blogs = [];
try {
    $blogs_sql = "SELECT id, title, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 10";
    $blogs_stmt = executeQuery($blogs_sql);
    $blogs = $blogs_stmt->fetchAll();
} catch (PDOException $e) {
    // Blog table doesn't exist yet, that's okay
    error_log('Blog posts table not found: ' . $e->getMessage());
}

// Fetch recent projects (if table exists)
$projects = [];
try {
    $projects_sql = "SELECT id, title FROM projects ORDER BY created_at DESC LIMIT 10";
    $projects_stmt = executeQuery($projects_sql);
    $projects = $projects_stmt->fetchAll();
} catch (PDOException $e) {
    // Projects table doesn't exist yet, that's okay
    error_log('Projects table not found: ' . $e->getMessage());
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-header">
    <h1><i data-feather="mail"></i> Email Notifications</h1>
    <p>Send email notifications to your contacts</p>
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

<div class="card">
    <h2>Compose Email</h2>
    
    <form method="POST" id="emailForm">
        <input type="hidden" name="action" value="send_email">
        
        <!-- Email Type Selection -->
        <div class="form-group">
            <label class="form-label">Email Type</label>
            <select name="email_type" id="emailType" class="form-input" required>
                <option value="">Select Type</option>
                <option value="blog_notification">Blog Notification</option>
                <option value="construction_update">Construction Update</option>
                <option value="custom">Custom Notification</option>
            </select>
        </div>
        
        <!-- Blog Selection (shown when blog_notification selected) -->
        <div class="form-group" id="blogSelection" style="display: none;">
            <label class="form-label">Select Blog Post</label>
            <?php if (empty($blogs)): ?>
                <p class="text-muted" style="padding: 1rem; background: #f9fafb; border-radius: 6px;">
                    <i data-feather="info"></i> No blog posts available yet. Create blog posts first or use Custom Notification instead.
                </p>
            <?php else: ?>
                <select name="blog_id" class="form-input">
                    <option value="">Select a blog post</option>
                    <?php foreach ($blogs as $blog): ?>
                        <option value="<?= $blog['id'] ?>">
                            <?= sanitizeOutput($blog['title']) ?> (<?= date('M d, Y', strtotime($blog['created_at'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>
        
        <!-- Construction Update Fields -->
        <div id="constructionFields" style="display: none;">
            <div class="form-group">
                <label class="form-label">Project Name</label>
                <input type="text" name="project_name" class="form-input" placeholder="Enter project name">
            </div>
            
            <div class="form-group">
                <label class="form-label">Project Status</label>
                <select name="project_status" class="form-input">
                    <option value="Planning">Planning</option>
                    <option value="Foundation">Foundation</option>
                    <option value="Structure">Structure</option>
                    <option value="Finishing">Finishing</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
        </div>
        
        <!-- Subject -->
        <div class="form-group">
            <label class="form-label">Email Subject *</label>
            <input type="text" name="subject" class="form-input" placeholder="Enter email subject" required>
        </div>
        
        <!-- Custom Message -->
        <div class="form-group">
            <label class="form-label">Message *</label>
            <textarea name="custom_message" class="form-textarea" rows="8" placeholder="Enter your message..." required></textarea>
            <small class="form-help">You can use HTML formatting in your message.</small>
        </div>
        
        <!-- Recipients -->
        <div class="form-group">
            <label class="form-label">Recipients *</label>
            <div style="margin-bottom: 1rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="recipients[]" value="all_contacts" id="selectAll">
                    <strong>Select All Contacts (<?= count($contacts) ?> recipients)</strong>
                </label>
            </div>
            
            <div style="max-height: 300px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 6px; padding: 1rem;">
                <?php if (empty($contacts)): ?>
                    <p class="text-muted">No contacts available. Contacts are added when users submit the contact form.</p>
                <?php else: ?>
                    <?php foreach ($contacts as $contact): ?>
                        <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem; cursor: pointer; border-bottom: 1px solid #f1f5f9;">
                            <input type="checkbox" name="recipients[]" value="<?= $contact['id'] ?>" class="recipient-checkbox">
                            <div>
                                <strong><?= sanitizeOutput($contact['name']) ?></strong>
                                <br>
                                <small class="text-muted"><?= sanitizeOutput($contact['email']) ?></small>
                            </div>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Preview Button -->
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="previewEmail()">
                <i data-feather="eye"></i> Preview
            </button>
            <button type="submit" class="btn btn-primary">
                <i data-feather="send"></i> Send Emails
            </button>
        </div>
    </form>
</div>

<!-- Email Statistics -->
<div class="card">
    <h2>Email Statistics</h2>
    <div class="stats-grid">
        <div class="stat-card">
            <i data-feather="users"></i>
            <div>
                <h3><?= count($contacts) ?></h3>
                <p>Total Contacts</p>
            </div>
        </div>
        <div class="stat-card">
            <i data-feather="file-text"></i>
            <div>
                <h3><?= count($blogs) ?></h3>
                <p>Recent Blogs</p>
            </div>
        </div>
        <div class="stat-card">
            <i data-feather="briefcase"></i>
            <div>
                <h3><?= count($projects) ?></h3>
                <p>Active Projects</p>
            </div>
        </div>
    </div>
</div>

<script>
// Email type change handler
document.getElementById('emailType').addEventListener('change', function() {
    const blogSelection = document.getElementById('blogSelection');
    const constructionFields = document.getElementById('constructionFields');
    
    // Hide all conditional fields
    blogSelection.style.display = 'none';
    constructionFields.style.display = 'none';
    
    // Show relevant fields
    if (this.value === 'blog_notification') {
        blogSelection.style.display = 'block';
    } else if (this.value === 'construction_update') {
        constructionFields.style.display = 'block';
    }
});

// Select all checkbox handler
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.recipient-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Preview email function
function previewEmail() {
    const subject = document.querySelector('[name="subject"]').value;
    const message = document.querySelector('[name="custom_message"]').value;
    
    if (!subject || !message) {
        alert('Please fill in subject and message to preview.');
        return;
    }
    
    const previewWindow = window.open('', 'Email Preview', 'width=600,height=800');
    previewWindow.document.write(`
        <html>
        <head>
            <title>Email Preview</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; background: #f9fafb; }
                .preview { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                h2 { color: #004AAD; }
            </style>
        </head>
        <body>
            <div class="preview">
                <h2>${subject}</h2>
                <div>${message.replace(/\n/g, '<br>')}</div>
            </div>
        </body>
        </html>
    `);
}

// Form validation
document.getElementById('emailForm').addEventListener('submit', function(e) {
    const selectedRecipients = document.querySelectorAll('.recipient-checkbox:checked');
    const selectAll = document.getElementById('selectAll').checked;
    
    if (!selectAll && selectedRecipients.length === 0) {
        e.preventDefault();
        alert('Please select at least one recipient.');
        return false;
    }
    
    if (!confirm(`Are you sure you want to send this email to ${selectAll ? 'all contacts' : selectedRecipients.length + ' recipient(s)'}?`)) {
        e.preventDefault();
        return false;
    }
});
</script>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.stat-card i {
    width: 40px;
    height: 40px;
    color: #004AAD;
}

.stat-card h3 {
    font-size: 2rem;
    margin: 0;
    color: #004AAD;
}

.stat-card p {
    margin: 0;
    color: #64748b;
    font-size: 0.9rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}
</style>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
