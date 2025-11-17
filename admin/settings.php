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
require_once __DIR__ . '/../config.php';

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

<div class="content-header">
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

<form method="POST" action="">
    <!-- General Settings -->
    <div class="card mb-lg">
        <div class="card-header">
            <h2 class="card-title">General Settings</h2>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="site_name" class="form-label">Site Name *</label>
                <input type="text" 
                       id="site_name" 
                       name="site_name" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['site_name'] ?? 'Grand Jyothi Construction') ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="site_tagline" class="form-label">Site Tagline</label>
                <input type="text" 
                       id="site_tagline" 
                       name="site_tagline" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['site_tagline'] ?? '') ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="company_description" class="form-label">Company Description</label>
            <textarea id="company_description" 
                      name="company_description" 
                      class="form-textarea"
                      rows="4"><?= sanitizeOutput($settings['company_description'] ?? '') ?></textarea>
            <p class="form-help">This appears in the footer and about sections</p>
        </div>
    </div>

    <!-- Branding -->
    <div class="card mb-lg">
        <div class="card-header">
            <h2 class="card-title">Branding</h2>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="site_logo" class="form-label">Logo Filename</label>
                <input type="text" 
                       id="site_logo" 
                       name="site_logo" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['site_logo'] ?? 'logo.png') ?>"
                       placeholder="logo.png">
                <p class="form-help">Upload logo to /assets/images/ folder</p>
            </div>
            
            <div class="form-group">
                <label for="site_favicon" class="form-label">Favicon Filename</label>
                <input type="text" 
                       id="site_favicon" 
                       name="site_favicon" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['site_favicon'] ?? 'favicon.ico') ?>"
                       placeholder="favicon.ico">
                <p class="form-help">Upload favicon to root folder</p>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="card mb-lg">
        <div class="card-header">
            <h2 class="card-title">Contact Information</h2>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="contact_email" class="form-label">Email Address *</label>
                <input type="email" 
                       id="contact_email" 
                       name="contact_email" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['contact_email'] ?? '') ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="contact_phone" class="form-label">Phone Number *</label>
                <input type="text" 
                       id="contact_phone" 
                       name="contact_phone" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['contact_phone'] ?? '') ?>"
                       required>
            </div>
        </div>
        
        <div class="form-group">
            <label for="contact_address" class="form-label">Address</label>
            <textarea id="contact_address" 
                      name="contact_address" 
                      class="form-textarea"
                      rows="3"><?= sanitizeOutput($settings['contact_address'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- Social Media Links -->
    <div class="card mb-lg">
        <div class="card-header">
            <h2 class="card-title">Social Media Links</h2>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="facebook_url" class="form-label">Facebook URL</label>
                <input type="url" 
                       id="facebook_url" 
                       name="facebook_url" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['facebook_url'] ?? '') ?>"
                       placeholder="https://facebook.com/yourpage">
            </div>
            
            <div class="form-group">
                <label for="twitter_url" class="form-label">Twitter URL</label>
                <input type="url" 
                       id="twitter_url" 
                       name="twitter_url" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['twitter_url'] ?? '') ?>"
                       placeholder="https://twitter.com/yourhandle">
            </div>
            
            <div class="form-group">
                <label for="instagram_url" class="form-label">Instagram URL</label>
                <input type="url" 
                       id="instagram_url" 
                       name="instagram_url" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['instagram_url'] ?? '') ?>"
                       placeholder="https://instagram.com/yourprofile">
            </div>
            
            <div class="form-group">
                <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                <input type="url" 
                       id="linkedin_url" 
                       name="linkedin_url" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['linkedin_url'] ?? '') ?>"
                       placeholder="https://linkedin.com/company/yourcompany">
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="card mb-lg">
        <div class="card-header">
            <h2 class="card-title">Company Statistics</h2>
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="years_experience" class="form-label">Years of Experience</label>
                <input type="number" 
                       id="years_experience" 
                       name="years_experience" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['years_experience'] ?? '18') ?>"
                       min="0">
            </div>
            
            <div class="form-group">
                <label for="projects_completed" class="form-label">Projects Completed</label>
                <input type="number" 
                       id="projects_completed" 
                       name="projects_completed" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['projects_completed'] ?? '500') ?>"
                       min="0">
            </div>
            
            <div class="form-group">
                <label for="happy_clients" class="form-label">Happy Clients</label>
                <input type="number" 
                       id="happy_clients" 
                       name="happy_clients" 
                       class="form-input"
                       value="<?= sanitizeOutput($settings['happy_clients'] ?? '450') ?>"
                       min="0">
            </div>
        </div>
        
        <p class="form-help">These numbers appear on the projects page statistics section</p>
    </div>

    <!-- Save Button -->
    <div class="btn-group">
        <button type="submit" name="submit" class="btn btn-primary">
            <i data-feather="save"></i> Save All Settings
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
