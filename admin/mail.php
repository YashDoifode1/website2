<?php
/**
 * Admin Mail System
 * Send email notifications to users
 * Updated for new contact_messages schema (first_name, last_name)
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
                // Prepare email content
                $emailContent = '';
                
                if ($emailType === 'blog_notification' && !empty($_POST['blog_id'])) {
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
                        $error_message = 'Blog posts table not available.';
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
                    'Emails sent! Success: %d, Failed: %d',
                    $results['success'],
                    $results['failed']
                );
                
                if ($results['failed'] > 0) {
                    $error_message = 'Some emails failed: ' . implode(', ', $results['errors']);
                }
            }
        }
    }
}

// Fetch contacts with first_name + last_name
$contacts_sql = "
    SELECT 
        id, 
        first_name, 
        last_name, 
        email, 
        submitted_at 
    FROM contact_messages 
    WHERE email IS NOT NULL AND email != '' 
    ORDER BY submitted_at DESC
";
$contacts_stmt = executeQuery($contacts_sql);
$contacts = $contacts_stmt->fetchAll();

// Fetch recent blog posts
$blogs = [];
try {
    $blogs_sql = "SELECT id, title, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 10";
    $blogs_stmt = executeQuery($blogs_sql);
    $blogs = $blogs_stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Blog posts table not found: ' . $e->getMessage());
}

// Fetch recent projects
$projects = [];
try {
    $projects_sql = "SELECT id, title FROM projects ORDER BY created_at DESC LIMIT 10";
    $projects_stmt = executeQuery($projects_sql);
    $projects = $projects_stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Projects table not found: ' . $e->getMessage());
}

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-header">
    <h1>Email Notifications</h1>
    <p>Send updates to your clients and leads</p>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success">
        <?= sanitizeOutput($success_message) ?>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-error">
        <?= sanitizeOutput($error_message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <h2 class="card-title">Compose Email</h2>
    
    <form method="POST" id="emailForm">
        <input type="hidden" name="action" value="send_email">
        
        <!-- Email Type -->
        <div class="form-group">
            <label class="form-label">Email Type</label>
            <select name="email_type" id="emailType" class="form-input" required>
                <option value="">Select Type</option>
                <option value="blog_notification">Blog Notification</option>
                <option value="construction_update">Construction Update</option>
                <option value="custom">Custom Notification</option>
            </select>
        </div>
        
        <!-- Blog Selection -->
        <div class="form-group" id="blogSelection" style="display: none;">
            <label class="form-label">Select Blog Post</label>
            <?php if (empty($blogs)): ?>
                <p class="text-muted p-3 bg-light rounded">
                    No blog posts available. Use Custom Notification.
                </p>
            <?php else: ?>
                <select name="blog_id" class="form-input">
                    <option value="">Choose a post</option>
                    <?php foreach ($blogs as $blog): ?>
                        <option value="<?= $blog['id'] ?>">
                            <?= sanitizeOutput($blog['title']) ?> 
                            (<?= date('M d, Y', strtotime($blog['created_at'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>
        
        <!-- Construction Update Fields -->
        <div id="constructionFields" style="display: none;">
            <div class="form-group">
                <label class="form-label">Project Name</label>
                <input type="text" name="project_name" class="form-input" placeholder="e.g. Villa at Nagpur">
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
            <label class="form-label">Subject *</label>
            <input type="text" name="subject" class="form-input" placeholder="Enter email subject" required>
        </div>
        
        <!-- Custom Message -->
        <div class="form-group">
            <label class="form-label">Message *</label>
            <textarea name="custom_message" class="form-textarea" rows="8" 
                      placeholder="Write your message here..." required></textarea>
            <small class="form-help">HTML is supported.</small>
        </div>
        
        <!-- Recipients -->
        <div class="form-group">
            <label class="form-label">Recipients *</label>
            <div class="mb-3">
                <label class="form-check">
                    <input type="checkbox" name="recipients[]" value="all_contacts" id="selectAll" class="form-check-input">
                    <span class="form-check-label fw-bold">
                        Select All Contacts (<?= count($contacts) ?>)
                    </span>
                </label>
            </div>
            
            <div class="recipients-list border rounded p-3" style="max-height: 320px; overflow-y: auto;">
                <?php if (empty($contacts)): ?>
                    <p class="text-muted mb-0">No contacts yet. They appear here after form submissions.</p>
                <?php else: ?>
                    <?php foreach ($contacts as $contact): 
                        $full_name = trim($contact['first_name'] . ' ' . $contact['last_name']);
                    ?>
                        <label class="form-check d-block p-2 border-bottom">
                            <input type="checkbox" name="recipients[]" value="<?= $contact['id'] ?>" class="form-check-input recipient-checkbox">
                            <span class="form-check-label">
                                <strong><?= sanitizeOutput($full_name) ?></strong><br>
                                <small class="text-muted"><?= sanitizeOutput($contact['email']) ?></small>
                            </span>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="form-actions d-flex gap-2 mt-4">
            <button type="button" class="btn btn-outline-secondary" onclick="previewEmail()">
                Preview
            </button>
            <button type="submit" class="btn btn-primary">
                Send Emails
            </button>
        </div>
    </form>
</div>

<!-- Statistics -->
<div class="card mt-4">
    <h2 class="card-title">Email Statistics</h2>
    <div class="row g-3">
        <div class="col-md-4">
            <div class="stat-card p-3 text-center border rounded">
                <h3 class="mb-1"><?= count($contacts) ?></h3>
                <p class="mb-0 text-muted">Total Contacts</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-3 text-center border rounded">
                <h3 class="mb-1"><?= count($blogs) ?></h3>
                <p class="mb-0 text-muted">Recent Blogs</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card p-3 text-center border rounded">
                <h3 class="mb-1"><?= count($projects) ?></h3>
                <p class="mb-0 text-muted">Active Projects</p>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('emailType').addEventListener('change', function() {
    document.getElementById('blogSelection').style.display = 'none';
    document.getElementById('constructionFields').style.display = 'none';
    
    if (this.value === 'blog_notification') {
        document.getElementById('blogSelection').style.display = 'block';
    } else if (this.value === 'construction_update') {
        document.getElementById('constructionFields').style.display = 'block';
    }
});

document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.recipient-checkbox').forEach(cb => cb.checked = this.checked);
});

function previewEmail() {
    const subject = document.querySelector('[name="subject"]').value;
    const message = document.querySelector('[name="custom_message"]').value;
    
    if (!subject || !message) {
        alert('Please fill subject and message first.');
        return;
    }
    
    const win = window.open('', 'Preview', 'width=650,height=800');
    win.document.write(`
        <html><head><title>Preview</title>
        <style>
            body { font-family: Arial; background: #f4f4f4; padding: 20px; }
            .email { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
            h2 { color: #1A1A1A; }
        </style>
        </head><body>
        <div class="email">
            <h2>${subject}</h2>
            <div>${message.replace(/\n/g, '<br>')}</div>
        </div>
        </body></html>
    `);
}

document.getElementById('emailForm').addEventListener('submit', function(e) {
    const selected = document.querySelectorAll('.recipient-checkbox:checked').length;
    const all = document.getElementById('selectAll').checked;
    
    if (!all && selected === 0) {
        e.preventDefault();
        alert('Please select at least one recipient.');
        return;
    }
    
    if (!confirm(`Send to ${all ? 'all contacts' : selected + ' recipient(s)'}?`)) {
        e.preventDefault();
    }
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>