<?php
/**
 * Admin Environment Settings
 * 
 * Manage .env file settings from admin panel
 */

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/env_manager.php';
require_once __DIR__ . '/includes/auth.php';

requireAdmin();

$page_title = 'Environment Settings';
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'update_settings') {
        // Backup current .env file
        backupEnvFile();
        
        // Prepare values to update
        $values = [
            // Site Information
            'SITE_NAME' => $_POST['site_name'] ?? '',
            'SITE_TAGLINE' => $_POST['site_tagline'] ?? '',
            'SITE_DESCRIPTION' => $_POST['site_description'] ?? '',
            'SITE_KEYWORDS' => $_POST['site_keywords'] ?? '',
            'CONTACT_EMAIL' => $_POST['contact_email'] ?? '',
            'CONTACT_PHONE' => $_POST['contact_phone'] ?? '',
            'CONTACT_ADDRESS' => $_POST['contact_address'] ?? '',
            'BUSINESS_HOURS_WEEKDAY' => $_POST['business_hours_weekday'] ?? '',
            'BUSINESS_HOURS_SATURDAY' => $_POST['business_hours_saturday'] ?? '',
            'BUSINESS_HOURS_SUNDAY' => $_POST['business_hours_sunday'] ?? '',
            'SITE_LOGO_TEXT' => $_POST['site_logo_text'] ?? '',
            'SITE_LOGO_SUBTITLE' => $_POST['site_logo_subtitle'] ?? '',
            'SHOW_LOGO_ICON' => isset($_POST['show_logo_icon']) ? 'true' : 'false',
            'FACEBOOK_URL' => $_POST['facebook_url'] ?? '',
            'TWITTER_URL' => $_POST['twitter_url'] ?? '',
            'INSTAGRAM_URL' => $_POST['instagram_url'] ?? '',
            'LINKEDIN_URL' => $_POST['linkedin_url'] ?? '',

            // Application Settings
            'APP_NAME' => $_POST['app_name'] ?? '',
            'APP_ENV' => $_POST['app_env'] ?? 'production',
            'APP_DEBUG' => isset($_POST['app_debug']) ? 'true' : 'false',
            'APP_URL' => $_POST['app_url'] ?? '',

            // Email Configuration
            'MAIL_DRIVER' => $_POST['mail_driver'] ?? 'smtp',
            'MAIL_HOST' => $_POST['mail_host'] ?? '',
            'MAIL_PORT' => $_POST['mail_port'] ?? '',
            'MAIL_USERNAME' => $_POST['mail_username'] ?? '',
            'MAIL_PASSWORD' => $_POST['mail_password'] ?? '',
            'MAIL_ENCRYPTION' => $_POST['mail_encryption'] ?? '',
            'MAIL_FROM_ADDRESS' => $_POST['mail_from_address'] ?? '',
            'MAIL_FROM_NAME' => $_POST['mail_from_name'] ?? '',
            'MAIL_DEBUG' => isset($_POST['mail_debug']) ? 'true' : 'false',

            // Upload & Misc Settings
            'MAX_UPLOAD_SIZE' => $_POST['max_upload_size'] ?? '5242880',
            'ALLOWED_IMAGE_TYPES' => $_POST['allowed_image_types'] ?? 'jpg,jpeg,png,gif,webp',
            'ITEMS_PER_PAGE' => $_POST['items_per_page'] ?? '12',
            'TIMEZONE' => $_POST['timezone'] ?? 'Asia/Kolkata',

            // Security
            'SESSION_LIFETIME' => $_POST['session_lifetime'] ?? '7200',
            'CSRF_TOKEN_NAME' => $_POST['csrf_token_name'] ?? 'csrf_token',
        ];
        
        // Update .env file
        $results = updateEnvValues($values);
        
        if ($results['success'] > 0) {
            $success_message = sprintf(
                'Settings updated successfully! %d settings saved.',
                $results['success']
            );
        }
        
        if ($results['failed'] > 0) {
            $error_message = sprintf(
                'Failed to update %d settings: %s',
                $results['failed'],
                implode(', ', $results['errors'])
            );
        }
    }
}

// Get current .env values
$envValues = getAllEnvValues();

// Get file permissions
$permissions = getEnvFilePermissions();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="admin-header">
    <h1><i data-feather="settings"></i> Environment Settings</h1>
    <p>Manage site configuration from .env file</p>
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

<?php if (!$permissions['writable']): ?>
    <div class="alert alert-warning">
        <i data-feather="alert-triangle"></i>
        <strong>Warning:</strong> .env file is not writable. Please check file permissions.
    </div>
<?php endif; ?>

<form method="POST" id="settingsForm">
    <input type="hidden" name="action" value="update_settings">
    
    <!-- Application Settings -->
    <div class="card">
        <h2><i data-feather="cpu"></i> Application Settings</h2>
        
        <div class="form-group">
            <label class="form-label">Application Name</label>
            <input type="text" name="app_name" class="form-input" 
                   value="<?= sanitizeOutput($envValues['APP_NAME'] ?? '') ?>">
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Environment</label>
                <select name="app_env" class="form-input">
                    <option value="local" <?= ($envValues['APP_ENV'] ?? '') === 'local' ? 'selected' : '' ?>>local</option>
                    <option value="development" <?= ($envValues['APP_ENV'] ?? '') === 'development' ? 'selected' : '' ?>>development</option>
                    <option value="production" <?= ($envValues['APP_ENV'] ?? '') === 'production' ? 'selected' : '' ?>>production</option>
                </select>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="app_debug" <?= ($envValues['APP_DEBUG'] ?? 'false') === 'true' ? 'checked' : '' ?>>
                    Debug Mode (Shows errors)
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Application URL</label>
            <input type="url" name="app_url" class="form-input" 
                   value="<?= sanitizeOutput($envValues['APP_URL'] ?? '') ?>" placeholder="https://yourdomain.com">
        </div>
    </div>

    <!-- Site Information -->
    <div class="card">
        <h2><i data-feather="info"></i> Site Information</h2>
        <!-- ... existing fields unchanged ... -->
        <div class="form-group">
            <label class="form-label">Site Name *</label>
            <input type="text" name="site_name" class="form-input" 
                   value="<?= sanitizeOutput($envValues['SITE_NAME'] ?? '') ?>" required>
            <small class="form-help">Your company or website name</small>
        </div>
        
        <div class="form-group">
            <label class="form-label">Site Tagline</label>
            <input type="text" name="site_tagline" class="form-input" 
                   value="<?= sanitizeOutput($envValues['SITE_TAGLINE'] ?? '') ?>">
            <small class="form-help">A short tagline or slogan</small>
        </div>
        
        <div class="form-group">
            <label class="form-label">Site Description (SEO)</label>
            <textarea name="site_description" class="form-textarea" rows="3"><?= sanitizeOutput($envValues['SITE_DESCRIPTION'] ?? '') ?></textarea>
            <small class="form-help">Used in meta tags and search results</small>
        </div>
        
        <div class="form-group">
            <label class="form-label">Keywords (SEO)</label>
            <input type="text" name="site_keywords" class="form-input" 
                   value="<?= sanitizeOutput($envValues['SITE_KEYWORDS'] ?? '') ?>">
            <small class="form-help">Comma-separated keywords for SEO</small>
        </div>
    </div>
    
    <!-- Logo Settings -->
    <div class="card">
        <h2><i data-feather="image"></i> Logo Settings</h2>
        
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Logo Text</label>
                <input type="text" name="site_logo_text" class="form-input" 
                       value="<?= sanitizeOutput($envValues['SITE_LOGO_TEXT'] ?? '') ?>">
                <small class="form-help">Main logo text (e.g., "Grand Jyothi")</small>
            </div>
            
            <div class="form-group">
                <label class="form-label">Logo Subtitle</label>
                <input type="text" name="site_logo_subtitle" class="form-input" 
                       value="<?= sanitizeOutput($envValues['SITE_LOGO_SUBTITLE'] ?? '') ?>">
                <small class="form-help">Logo subtitle (e.g., "Construction")</small>
            </div>
        </div>
        
        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="show_logo_icon" 
                       <?= ($envValues['SHOW_LOGO_ICON'] ?? 'true') === 'true' ? 'checked' : '' ?>>
                Show Logo Icon (Home icon in header)
            </label>
        </div>
    </div>
    
    <!-- Contact Information -->
    <div class="card">
        <h2><i data-feather="phone"></i> Contact Information</h2>
        <!-- ... unchanged ... -->
        <div class="form-group">
            <label class="form-label">Contact Email *</label>
            <input type="email" name="contact_email" class="form-input" 
                   value="<?= sanitizeOutput($envValues['CONTACT_EMAIL'] ?? '') ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label">Contact Phone *</label>
            <input type="text" name="contact_phone" class="form-input" 
                   value="<?= sanitizeOutput($envValues['CONTACT_PHONE'] ?? '') ?>" required>
            <small class="form-help">Include country code (e.g., +91 12345 67890)</small>
        </div>
        
        <div class="form-group">
            <label class="form-label">Business Address</label>
            <textarea name="contact_address" class="form-textarea" rows="3"><?= sanitizeOutput($envValues['CONTACT_ADDRESS'] ?? '') ?></textarea>
        </div>
    </div>
    
    <!-- Business Hours -->
    <div class="card">
        <h2><i data-feather="clock"></i> Business Hours</h2>
        <!-- ... unchanged ... -->
        <div class="form-group">
            <label class="form-label">Monday - Friday</label>
            <input type="text" name="business_hours_weekday" class="form-input" 
                   value="<?= sanitizeOutput($envValues['BUSINESS_HOURS_WEEKDAY'] ?? '') ?>"
                   placeholder="9:00 AM - 6:00 PM">
        </div>
        
        <div class="form-group">
            <label class="form-label">Saturday</label>
            <input type="text" name="business_hours_saturday" class="form-input" 
                   value="<?= sanitizeOutput($envValues['BUSINESS_HOURS_SATURDAY'] ?? '') ?>"
                   placeholder="9:00 AM - 2:00 PM">
        </div>
        
        <div class="form-group">
            <label class="form-label">Sunday</label>
            <input type="text" name="business_hours_sunday" class="form-input" 
                   value="<?= sanitizeOutput($envValues['BUSINESS_HOURS_SUNDAY'] ?? '') ?>"
                   placeholder="Closed">
        </div>
    </div>
    
    <!-- Social Media -->
    <div class="card">
        <h2><i data-feather="share-2"></i> Social Media Links</h2>
        <!-- ... unchanged ... -->
        <div class="form-group">
            <label class="form-label">Facebook URL</label>
            <input type="url" name="facebook_url" class="form-input" 
                   value="<?= sanitizeOutput($envValues['FACEBOOK_URL'] ?? '') ?>"
                   placeholder="https://facebook.com/yourpage">
        </div>
        <div class="form-group">
            <label class="form-label">Twitter URL</label>
            <input type="url" name="twitter_url" class="form-input" 
                   value="<?= sanitizeOutput($envValues['TWITTER_URL'] ?? '') ?>"
                   placeholder="https://twitter.com/yourhandle">
        </div>
        <div class="form-group">
            <label class="form-label">Instagram URL</label>
            <input type="url" name="instagram_url" class="form-input" 
                   value="<?= sanitizeOutput($envValues['INSTAGRAM_URL'] ?? '') ?>"
                   placeholder="https://instagram.com/yourprofile">
        </div>
        <div class="form-group">
            <label class="form-label">LinkedIn URL</label>
            <input type="url" name="linkedin_url" class="form-input" 
                   value="<?= sanitizeOutput($envValues['LINKEDIN_URL'] ?? '') ?>"
                   placeholder="https://linkedin.com/company/yourcompany">
        </div>
    </div>

    <!-- Email Configuration -->
    <div class="card">
        <h2><i data-feather="mail"></i> Email Configuration (SMTP)</h2>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Mail Driver</label>
                <select name="mail_driver" class="form-input">
                    <option value="smtp" <?= ($envValues['MAIL_DRIVER'] ?? '') === 'smtp' ? 'selected' : '' ?>>smtp</option>
                    <option value="mail" <?= ($envValues['MAIL_DRIVER'] ?? '') === 'mail' ? 'selected' : '' ?>>mail (PHP mail)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Debug Email</label>
                <label class="checkbox-label">
                    <input type="checkbox" name="mail_debug" <?= ($envValues['MAIL_DEBUG'] ?? 'false') === 'true' ? 'checked' : '' ?>>
                    Enable MAIL_DEBUG
                </label>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">SMTP Host</label>
                <input type="text" name="mail_host" class="form-input" value="<?= sanitizeOutput($envValues['MAIL_HOST'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">SMTP Port</label>
                <input type="text" name="mail_port" class="form-input" value="<?= sanitizeOutput($envValues['MAIL_PORT'] ?? '') ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">SMTP Username</label>
                <input type="text" name="mail_username" class="form-input" value="<?= sanitizeOutput($envValues['MAIL_USERNAME'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">SMTP Password</label>
                <input type="password" name="mail_password" class="form-input" value="<?= sanitizeOutput($envValues['MAIL_PASSWORD'] ?? '') ?>" autocomplete="new-password">
                <small class="form-help">Leave blank to keep current password</small>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Encryption</label>
                <select name="mail_encryption" class="form-input">
                    <option value="">None</option>
                    <option value="tls" <?= ($envValues['MAIL_ENCRYPTION'] ?? '') === 'tls' ? 'selected' : '' ?>>tls</option>
                    <option value="ssl" <?= ($envValues['MAIL_ENCRYPTION'] ?? '') === 'ssl' ? 'selected' : '' ?>>ssl</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">From Address</label>
                <input type="email" name="mail_from_address" class="form-input" value="<?= sanitizeOutput($envValues['MAIL_FROM_ADDRESS'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">From Name</label>
            <input type="text" name="mail_from_name" class="form-input" value="<?= sanitizeOutput($envValues['MAIL_FROM_NAME'] ?? '') ?>">
        </div>
    </div>

    <!-- Upload & System Settings -->
    <div class="card">
        <h2><i data-feather="upload"></i> Upload & System Settings</h2>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Max Upload Size (bytes)</label>
                <input type="number" name="max_upload_size" class="form-input" value="<?= sanitizeOutput($envValues['MAX_UPLOAD_SIZE'] ?? '5242880') ?>">
                <small class="form-help">Default: 5MB = 5242880</small>
            </div>
            <div class="form-group">
                <label class="form-label">Items Per Page</label>
                <input type="number" name="items_per_page" class="form-input" value="<?= sanitizeOutput($envValues['ITEMS_PER_PAGE'] ?? '12') ?>">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Allowed Image Types</label>
                <input type="text" name="allowed_image_types" class="form-input" value="<?= sanitizeOutput($envValues['ALLOWED_IMAGE_TYPES'] ?? 'jpg,jpeg,png,gif,webp') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Timezone</label>
                <input type="text" name="timezone" class="form-input" value="<?= sanitizeOutput($envValues['TIMEZONE'] ?? 'Asia/Kolkata') ?>">
            </div>
        </div>
    </div>

    <!-- Security Settings -->
    <div class="card">
        <h2><i data-feather="shield"></i> Security Settings</h2>

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Session Lifetime (seconds)</label>
                <input type="number" name="session_lifetime" class="form-input" value="<?= sanitizeOutput($envValues['SESSION_LIFETIME'] ?? '7200') ?>">
                <small class="form-help">7200 = 2 hours</small>
            </div>
            <div class="form-group">
                <label class="form-label">CSRF Token Name</label>
                <input type="text" name="csrf_token_name" class="form-input" value="<?= sanitizeOutput($envValues['CSRF_TOKEN_NAME'] ?? 'csrf_token') ?>">
            </div>
        </div>
    </div>

    <!-- File Information -->
    <div class="card">
        <h2><i data-feather="file-text"></i> File Information</h2>
        <!-- ... unchanged ... -->
        <div class="info-grid">
            <div class="info-item">
                <strong>File Status:</strong>
                <span class="badge <?= $permissions['exists'] ? 'badge-success' : 'badge-error' ?>">
                    <?= $permissions['exists'] ? 'Exists' : 'Not Found' ?>
                </span>
            </div>
            <div class="info-item">
                <strong>Readable:</strong>
                <span class="badge <?= $permissions['readable'] ? 'badge-success' : 'badge-error' ?>">
                    <?= $permissions['readable'] ? 'Yes' : 'No' ?>
                </span>
            </div>
            <div class="info-item">
                <strong>Writable:</strong>
                <span class="badge <?= $permissions['writable'] ? 'badge-success' : 'badge-error' ?>">
                    <?= $permissions['writable'] ? 'Yes' : 'No' ?>
                </span>
            </div>
            <?php if ($permissions['exists']): ?>
            <div class="info-item">
                <strong>Last Modified:</strong>
                <?= date('M d, Y H:i:s', $permissions['modified']) ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="alert alert-info" style="margin-top: 1rem;">
            <i data-feather="info"></i>
            <strong>Note:</strong> Changes are saved to the .env file and take effect immediately. A backup is created automatically before each update.
        </div>
    </div>
    
    <!-- Submit Button -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <i data-feather="save"></i> Save Settings
        </button>
        <button type="button" class="btn btn-secondary" onclick="window.location.reload()">
            <i data-feather="x"></i> Cancel
        </button>
    </div>
</form>

<!-- Existing styles and script unchanged -->
<style>
/* ... your existing styles ... */
.form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
.info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; padding: 1rem; background: #f9fafb; border-radius: 6px; }
.info-item { display: flex; flex-direction: column; gap: 0.5rem; }
.badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem; font-weight: 600; width: fit-content; }
.badge-success { background: #dcfce7; color: #166534; }
.badge-error { background: #fee2e2; color: #991b1b; }
.checkbox-label { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-weight: 500; }
.checkbox-label input[type="checkbox"] { width: 18px; height: 18px; cursor: pointer; }
.form-actions { display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e2e8f0; }
</style>

<script>
// Existing validation script (unchanged)
document.getElementById('settingsForm').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = '#ef4444';
        } else {
            field.style.borderColor = '';
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return false;
    }
    
    return confirm('Are you sure you want to update these settings? A backup will be created automatically.');
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>