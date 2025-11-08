# Admin Environment Settings Guide

## Overview

The Environment Settings page in the admin panel allows you to update all `.env` configuration values through a user-friendly interface. No need to manually edit the `.env` file!

## Accessing Environment Settings

1. Login to Admin Panel
2. Click **"Environment Settings"** in the sidebar (under Settings section)
3. Update any values
4. Click **"Save Settings"**

## Features

### ✅ What You Can Update

**Site Information:**
- Site Name
- Site Tagline
- Site Description (SEO)
- Keywords (SEO)

**Logo Settings:**
- Logo Text
- Logo Subtitle
- Show/Hide Logo Icon

**Contact Information:**
- Contact Email
- Contact Phone
- Business Address

**Business Hours:**
- Monday - Friday Hours
- Saturday Hours
- Sunday Hours

**Social Media:**
- Facebook URL
- Twitter URL
- Instagram URL
- LinkedIn URL

### ✅ Safety Features

1. **Automatic Backups**
   - Creates backup before each update
   - Backup format: `.env.backup.YYYY-MM-DD-HHMMSS`
   - Can restore if needed

2. **Validation**
   - Required fields are enforced
   - Email format validation
   - URL format validation

3. **Permission Checks**
   - Shows file status (exists, readable, writable)
   - Warns if file is not writable
   - Displays last modified time

4. **Confirmation**
   - Asks for confirmation before saving
   - Shows success/error messages
   - Displays number of settings updated

## How to Use

### Step 1: Access the Page

Navigate to: `Admin Panel → Environment Settings`

### Step 2: Update Values

Fill in the form fields with your desired values:

**Example - Update Company Name:**
```
Site Name: ABC Construction
Logo Text: ABC
Logo Subtitle: Builders
```

**Example - Update Contact Info:**
```
Contact Email: info@abcbuilders.com
Contact Phone: +91 98765 43210
Business Address: 123 Main Street, Mumbai
```

**Example - Update Business Hours:**
```
Monday - Friday: 8:00 AM - 7:00 PM
Saturday: 9:00 AM - 5:00 PM
Sunday: Closed
```

### Step 3: Save Changes

1. Click **"Save Settings"** button
2. Confirm the action
3. Wait for success message
4. Changes are applied immediately!

### Step 4: Verify Changes

1. Open your website in a new tab
2. Check if changes appear
3. Refresh if needed (Ctrl+F5)

## Field Descriptions

### Site Information Section

| Field | Description | Example |
|-------|-------------|---------|
| **Site Name** | Your company/website name | "Grand Jyothi Construction" |
| **Site Tagline** | Short slogan or tagline | "Building Dreams" |
| **Site Description** | SEO description for search engines | "Leading construction company..." |
| **Keywords** | Comma-separated SEO keywords | "construction, nagpur, residential" |

### Logo Settings Section

| Field | Description | Example |
|-------|-------------|---------|
| **Logo Text** | Main logo text | "Grand Jyothi" |
| **Logo Subtitle** | Logo suffix/subtitle | "Construction" |
| **Show Logo Icon** | Display home icon in logo | Checked/Unchecked |

### Contact Information Section

| Field | Description | Example |
|-------|-------------|---------|
| **Contact Email** | Primary contact email | "info@company.com" |
| **Contact Phone** | Phone with country code | "+91 98765 43210" |
| **Business Address** | Full business address | "123 Street, City - 440010" |

### Business Hours Section

| Field | Description | Example |
|-------|-------------|---------|
| **Monday - Friday** | Weekday operating hours | "9:00 AM - 6:00 PM" |
| **Saturday** | Saturday operating hours | "9:00 AM - 2:00 PM" |
| **Sunday** | Sunday hours or "Closed" | "Closed" |

### Social Media Section

| Field | Description | Example |
|-------|-------------|---------|
| **Facebook URL** | Facebook page link | "https://facebook.com/page" |
| **Twitter URL** | Twitter profile link | "https://twitter.com/handle" |
| **Instagram URL** | Instagram profile link | "https://instagram.com/profile" |
| **LinkedIn URL** | LinkedIn company page | "https://linkedin.com/company/name" |

## File Information Panel

Shows important information about the `.env` file:

- **File Status**: Whether file exists
- **Readable**: Can the system read the file
- **Writable**: Can the system write to the file
- **Last Modified**: When file was last updated

### Permission Issues

If you see "Writable: No":

**Windows:**
1. Right-click `.env` file
2. Properties → Security
3. Edit permissions
4. Allow "Full Control" for web server user

**Linux/Mac:**
```bash
chmod 644 .env
```

## Backup & Restore

### Automatic Backups

Every time you save settings, a backup is created automatically:
- Location: Project root directory
- Format: `.env.backup.2024-11-08-173045`
- Keeps all previous backups

### Manual Backup

Before making major changes:
```bash
copy .env .env.manual-backup
```

### Restore from Backup

If something goes wrong:

1. **Via File Manager:**
   - Rename `.env.backup.YYYY-MM-DD-HHMMSS` to `.env`
   - Overwrite existing file

2. **Via Command Line:**
   ```bash
   copy .env.backup.2024-11-08-173045 .env
   ```

## Troubleshooting

### Changes Not Saving

**Problem:** Click save but nothing happens

**Solutions:**
1. Check file permissions (must be writable)
2. Check for PHP errors in error log
3. Verify all required fields are filled
4. Try refreshing the page

### Changes Not Appearing on Website

**Problem:** Settings saved but website unchanged

**Solutions:**
1. Hard refresh browser (Ctrl+Shift+R)
2. Clear browser cache
3. Check if correct .env file is being used
4. Restart Apache server

### Permission Denied Error

**Problem:** "File is not writable" warning

**Solutions:**
1. Check file permissions
2. Ensure web server has write access
3. On Windows, check file is not read-only
4. On Linux, run: `chmod 644 .env`

### Backup Files Accumulating

**Problem:** Too many backup files

**Solution:**
- Manually delete old backups
- Keep only recent ones
- Backups are in project root: `.env.backup.*`

## Best Practices

### 1. Test Changes First

- Make one change at a time
- Verify each change works
- Don't change everything at once

### 2. Keep Backups

- Don't delete all backup files
- Keep at least 2-3 recent backups
- Create manual backup before major changes

### 3. Use Valid Data

- Email addresses must be valid format
- URLs must start with http:// or https://
- Phone numbers should include country code
- Business hours should be clear and consistent

### 4. Document Changes

- Keep a log of what you changed
- Note the date and reason
- Helps troubleshoot issues later

### 5. Regular Updates

- Update contact info when it changes
- Keep social media links current
- Update business hours for holidays

## Security Considerations

### 1. Access Control

- Only admins can access this page
- Requires login to admin panel
- Session-based authentication

### 2. File Protection

- `.env` file is protected by `.htaccess`
- Not accessible via web browser
- Only server-side PHP can read it

### 3. Backup Security

- Backup files also protected
- Same security as `.env` file
- Delete old backups regularly

### 4. Sensitive Data

- Don't store passwords in these fields
- Use separate database settings
- Email passwords go in email config section

## Advanced Usage

### Programmatic Updates

You can also update .env values from code:

```php
require_once 'includes/env_manager.php';

// Update single value
updateEnvValue('SITE_NAME', 'New Company Name');

// Update multiple values
updateEnvValues([
    'SITE_NAME' => 'New Name',
    'CONTACT_EMAIL' => 'new@email.com'
]);

// Get all values
$values = getAllEnvValues();
print_r($values);
```

### Validation

Check if .env file is valid:

```php
$validation = validateEnvFile();
if (!$validation['valid']) {
    foreach ($validation['errors'] as $error) {
        echo $error . "\n";
    }
}
```

## Comparison: Admin Panel vs Manual Editing

| Feature | Admin Panel | Manual Edit |
|---------|-------------|-------------|
| **Ease of Use** | ✅ Very Easy | ❌ Technical |
| **Validation** | ✅ Built-in | ❌ Manual |
| **Backups** | ✅ Automatic | ❌ Manual |
| **Error Checking** | ✅ Yes | ❌ No |
| **User Friendly** | ✅ Yes | ❌ No |
| **Requires Tech Skills** | ❌ No | ✅ Yes |

## FAQs

**Q: Can I break my site by changing these settings?**
A: No, backups are created automatically. You can always restore.

**Q: Do changes take effect immediately?**
A: Yes, changes are applied as soon as you save.

**Q: Can multiple admins edit at the same time?**
A: Yes, but last save wins. Coordinate with team.

**Q: What if I make a mistake?**
A: Restore from automatic backup created before your change.

**Q: Are there any settings I shouldn't change?**
A: All settings here are safe to change. Database settings are separate.

**Q: How often should I update these?**
A: Update when information changes (new phone, address, etc.)

**Q: Can I add new settings?**
A: Not through this interface. Add to .env manually, then they'll appear here.

## Support

For issues with Environment Settings:

1. Check this guide
2. Verify file permissions
3. Check backup files exist
4. Review error messages
5. Contact system administrator

## Related Documentation

- `SITE_SETTINGS_GUIDE.md` - Manual .env editing guide
- `ENV_SETUP_GUIDE.md` - Environment configuration guide
- `EMAIL_SYSTEM_GUIDE.md` - Email settings guide

---

**Last Updated:** November 8, 2024
**Version:** 1.0
**Feature:** Admin Environment Settings
