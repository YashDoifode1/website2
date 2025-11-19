<?php
/**
 * Admin Site Settings
 * 
 * Manage site-wide settings (logo, contact info, social media, etc.)
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/settings.php';

requireAdmin();

$page_title = 'Site Settings';
$success_message = '';
$error_message = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_count = 0;
    
    foreach ($_POST as $key => $value) {
        if ($key !== 'submit') {
            $type = 'text';
            if (strpos($key, 'email') !== false) $type = 'email';
            elseif (strpos($key, 'description') !== false || strpos($key, 'address') !== false) $type = 'textarea';
            elseif (in_array($key, ['years_experience', 'projects_completed', 'happy_clients'])) $type = 'number';
            
            if (updateSetting($key, trim($value), $type)) {
                $updated_count++;
            }
        }
    }
    
    if ($updated_count > 0) {
        $success_message = "Settings updated successfully! ($updated_count settings saved)";
    } else {
        $error_message = 'No settings were updated.';
    }
}

// Fetch all current settings
$settings = getAllSettings();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-header">
    <h1>Site Settings</h1>
    <p>Manage site-wide configuration, contact information, and branding</p>
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

<form method="POST" id="settingsForm">
    
    <!-- General Settings -->
    <div class="card">
        <h2>General Settings</h2>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Site Name *</label>
                <input type="text" name="site_name" class="form-input"
                       value="<?= sanitizeOutput($settings['site_name'] ?? 'Rakhi Construction & Consultancy Pvt Ltd') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Site Tagline</label>
                <input type="text" name="site_tagline" class="form-input"
                       value="<?= sanitizeOutput($settings['site_tagline'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Company Description</label>
            <textarea name="company_description" class="form-textarea" rows="4"><?= sanitizeOutput($settings['company_description'] ?? '') ?></textarea>
            <small class="form-help">This appears in the footer and about sections</small>
        </div>
    </div>

    <!-- Branding -->
    <div class="card">
        <h2>Branding</h2>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Logo Filename</label>
                <input type="text" name="site_logo" class="form-input"
                       value="<?= sanitizeOutput($settings['site_logo'] ?? 'logo.png') ?>"
                       placeholder="logo.png">
                <small class="form-help">Upload logo to /assets/images/ folder</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Favicon Filename</label>
                <input type="text" name="site_favicon" class="form-input"
                       value="<?= sanitizeOutput($settings['site_favicon'] ?? 'favicon.ico') ?>"
                       placeholder="favicon.ico">
                <small class="form-help">Upload favicon to root folder</small>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="card">
        <h2>Contact Information</h2>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Email Address *</label>
                <input type="email" name="contact_email" class="form-input"
                       value="<?= sanitizeOutput($settings['contact_email'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Phone Number *</label>
                <input type="text" name="contact_phone" class="form-input"
                       value="<?= sanitizeOutput($settings['contact_phone'] ?? '') ?>" required>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Address</label>
            <textarea name="contact_address" class="form-textarea" rows="3"><?= sanitizeOutput($settings['contact_address'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Social Media Links -->
    <div class="card">
        <h2>Social Media Links</h2>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Facebook URL</label>
                <input type="url" name="facebook_url" class="form-input"
                       value="<?= sanitizeOutput($settings['facebook_url'] ?? '') ?>"
                       placeholder="https://facebook.com/yourpage">
            </div>
            
            <div class="form-group">
                <label class="form-label">Twitter URL</label>
                <input type="url" name="twitter_url" class="form-input"
                       value="<?= sanitizeOutput($settings['twitter_url'] ?? '') ?>"
                       placeholder="https://twitter.com/yourhandle">
            </div>
            
            <div class="form-group">
                <label class="form-label">Instagram URL</label>
                <input type="url" name="instagram_url" class="form-input"
                       value="<?= sanitizeOutput($settings['instagram_url'] ?? '') ?>"
                       placeholder="https://instagram.com/yourprofile">
            </div>
            
            <div class="form-group">
                <label class="form-label">LinkedIn URL</label>
                <input type="url" name="linkedin_url" class="form-input"
                       value="<?= sanitizeOutput($settings['linkedin_url'] ?? '') ?>"
                       placeholder="https://linkedin.com/company/yourcompany">
            </div>
        </div>
    </div>

    <!-- Company Statistics -->
    <div class="card">
        <h2>Company Statistics</h2>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Years of Experience</label>
                <input type="number" name="years_experience" class="form-input"
                       value="<?= sanitizeOutput($settings['years_experience'] ?? '18') ?>" min="0">
            </div>
            
            <div class="form-group">
                <label class="form-label">Projects Completed</label>
                <input type="number" name="projects_completed" class="form-input"
                       value="<?= sanitizeOutput($settings['projects_completed'] ?? '500') ?>" min="0">
            </div>
            
            <div class="form-group">
                <label class="form-label">Happy Clients</label>
                <input type="number" name="happy_clients" class="form-input"
                       value="<?= sanitizeOutput($settings['happy_clients'] ?? '450') ?>" min="0">
            </div>
        </div>
        
        <small class="form-help">These numbers appear on the projects page statistics section</small>
    </div>

    <!-- Submit Buttons -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            Save All Settings
        </button>
        <button type="button" class="btn btn-secondary" onclick="window.location.reload()">
            Cancel
        </button>
    </div>
</form>

<style>
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e2e8f0;
}
</style>

<script>
// Simple client-side validation
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const required = this.querySelectorAll('[required]');
    let valid = true;
    required.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#ef4444';
            valid = false;
        } else {
            field.style.borderColor = '';
        }
    });
    
    if (!valid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>