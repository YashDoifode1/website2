# PHPMailer Setup Guide

## Overview

The email system now uses PHPMailer, a professional email library for PHP that provides:
- ✅ Better SMTP support
- ✅ Improved error handling
- ✅ HTML and plain text emails
- ✅ Attachment support
- ✅ Better Gmail compatibility
- ✅ Detailed debugging

## Installation

### Method 1: Using Composer (Recommended)

1. **Install Composer** (if not already installed)
   - Download from: https://getcomposer.org/download/
   - Run the installer
   - Verify: `composer --version`

2. **Install PHPMailer**
   ```bash
   cd c:\xampp\htdocs\constructioninnagpur
   composer install
   ```

3. **Verify Installation**
   - Check that `vendor/` folder exists
   - Check that `vendor/phpmailer/` folder exists

### Method 2: Manual Installation (Alternative)

If Composer is not available:

1. **Download PHPMailer**
   - Visit: https://github.com/PHPMailer/PHPMailer/releases
   - Download latest release (ZIP file)

2. **Extract Files**
   - Extract to: `c:\xampp\htdocs\constructioninnagpur\vendor\phpmailer\phpmailer\`
   - Folder structure should be:
     ```
     vendor/
       phpmailer/
         phpmailer/
           src/
             PHPMailer.php
             SMTP.php
             Exception.php
     ```

3. **Create Autoloader**
   Create `vendor/autoload.php`:
   ```php
   <?php
   require_once __DIR__ . '/phpmailer/phpmailer/src/PHPMailer.php';
   require_once __DIR__ . '/phpmailer/phpmailer/src/SMTP.php';
   require_once __DIR__ . '/phpmailer/phpmailer/src/Exception.php';
   ```

## Configuration

### 1. Update .env File

```env
# Use SMTP for better reliability
MAIL_DRIVER=smtp

# Gmail SMTP Settings
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# From Address
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Grand Jyothi Construction"

# Debug mode (true for troubleshooting)
MAIL_DEBUG=false
```

### 2. Gmail App Password Setup

**Important:** Don't use your regular Gmail password!

1. **Enable 2-Factor Authentication**
   - Go to: https://myaccount.google.com/security
   - Click "2-Step Verification"
   - Follow setup instructions

2. **Generate App Password**
   - Go to: https://myaccount.google.com/apppasswords
   - Select "Mail" and your device
   - Click "Generate"
   - Copy the 16-character password
   - Use this as `MAIL_PASSWORD` in .env

3. **Update .env**
   ```env
   MAIL_USERNAME=youremail@gmail.com
   MAIL_PASSWORD=abcd efgh ijkl mnop
   ```

### 3. Other SMTP Providers

**Outlook/Hotmail:**
```env
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

**Yahoo:**
```env
MAIL_HOST=smtp.mail.yahoo.com
MAIL_PORT=587
MAIL_USERNAME=your-email@yahoo.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
```

**Custom SMTP:**
```env
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=info@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

## Testing

### Test Email Function

Create `test-phpmailer.php` in project root:

```php
<?php
require_once 'includes/mailer.php';

// Test email
$to = 'test@example.com';
$subject = 'PHPMailer Test';
$message = '<h1>Test Email</h1><p>This is a test email from PHPMailer.</p>';

if (sendEmail($to, $subject, $message)) {
    echo 'Email sent successfully!';
} else {
    echo 'Email failed to send.';
}
```

Run: `http://localhost/constructioninnagpur/test-phpmailer.php`

### Enable Debug Mode

For troubleshooting, enable debug mode in .env:

```env
MAIL_DEBUG=true
```

This will show detailed SMTP communication.

## Features

### 1. HTML Emails

```php
$html = '<h1>Hello</h1><p>This is <strong>HTML</strong> email.</p>';
sendEmail('user@example.com', 'Subject', $html);
```

### 2. Plain Text Alternative

```php
$html = '<h1>Hello</h1><p>Welcome to our site.</p>';
$text = 'Hello. Welcome to our site.';
sendEmail('user@example.com', 'Subject', $html, $text);
```

### 3. Bulk Emails

```php
$recipients = ['user1@example.com', 'user2@example.com'];
$results = sendBulkEmails($recipients, 'Subject', $html);
echo "Success: {$results['success']}, Failed: {$results['failed']}";
```

### 4. Email Templates

```php
$html = getEmailTemplate('blog_notification', [
    'site_name' => 'Grand Jyothi',
    'title' => 'New Blog Post',
    'excerpt' => 'Check out our latest article...',
    'link' => 'https://example.com/blog/1'
]);

sendEmail('user@example.com', 'New Blog Post', $html);
```

## Troubleshooting

### PHPMailer Not Available

**Error:** "PHPMailer not available. Run: composer install"

**Solution:**
1. Run `composer install` in project directory
2. Or install manually (see Method 2 above)
3. System will fallback to PHP mail() if PHPMailer not available

### SMTP Authentication Failed

**Error:** "SMTP authentication failed"

**Solutions:**
1. Verify username and password are correct
2. For Gmail, use App Password (not regular password)
3. Enable 2-Factor Authentication first
4. Check MAIL_USERNAME is full email address

### Connection Timeout

**Error:** "SMTP connection timeout"

**Solutions:**
1. Check MAIL_HOST is correct
2. Verify port (587 for TLS, 465 for SSL)
3. Check firewall settings
4. Try different port
5. Verify internet connection

### SSL Certificate Error

**Error:** "SSL certificate problem"

**Solution:**
Add to .env:
```env
MAIL_ENCRYPTION=tls
MAIL_PORT=587
```

Or for SSL:
```env
MAIL_ENCRYPTION=ssl
MAIL_PORT=465
```

### Emails Going to Spam

**Solutions:**
1. Use authenticated SMTP (not PHP mail())
2. Use real from address that matches domain
3. Add SPF record to DNS
4. Add DKIM signature
5. Avoid spam trigger words
6. Include unsubscribe link

### Debug Mode Not Working

**Solution:**
1. Enable in .env: `MAIL_DEBUG=true`
2. Check error logs: `C:\xampp\php\logs\php_error_log`
3. Check email logs: `logs/email.log`

## Advantages Over PHP mail()

| Feature | PHPMailer | PHP mail() |
|---------|-----------|------------|
| **SMTP Support** | ✅ Full | ❌ Limited |
| **Authentication** | ✅ Yes | ❌ No |
| **Error Messages** | ✅ Detailed | ❌ Generic |
| **HTML Emails** | ✅ Easy | ⚠️ Manual |
| **Attachments** | ✅ Simple | ⚠️ Complex |
| **Gmail Compatible** | ✅ Yes | ❌ Often blocked |
| **Debugging** | ✅ Built-in | ❌ No |
| **Security** | ✅ Better | ⚠️ Basic |

## Security Best Practices

### 1. Protect Credentials

```env
# Never commit .env to version control
# Use strong passwords
# Rotate credentials regularly
```

### 2. Use App Passwords

- Never use regular Gmail password
- Generate app-specific passwords
- Revoke if compromised

### 3. Validate Input

```php
// Always validate email addresses
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Invalid email');
}
```

### 4. Rate Limiting

```php
// Prevent spam
// System includes 0.1s delay between emails
sendBulkEmails($recipients, $subject, $message);
```

### 5. Logging

```php
// All emails are logged
// Check logs/email.log for activity
```

## Performance Tips

### 1. Connection Reuse

For bulk emails, PHPMailer reuses SMTP connection automatically.

### 2. Async Processing

For large batches, consider background processing:
- Use queue system
- Process in batches
- Schedule during off-peak hours

### 3. Optimize Templates

- Minimize HTML size
- Inline CSS
- Compress images
- Use CDN for assets

## Fallback System

If PHPMailer is not available, the system automatically falls back to PHP mail():

```php
// Automatic fallback
if (!PHPMAILER_AVAILABLE) {
    return sendMailFallback($to, $subject, $message);
}
```

This ensures emails still work even without PHPMailer.

## Updating PHPMailer

### Via Composer

```bash
composer update phpmailer/phpmailer
```

### Manual Update

1. Download latest release
2. Replace files in `vendor/phpmailer/phpmailer/`
3. Test email functionality

## Support

### Common Issues

**Q: Do I need Composer?**
A: Recommended but not required. Manual installation works too.

**Q: Can I use without SMTP?**
A: Yes, set `MAIL_DRIVER=mail` in .env

**Q: Does it work with Gmail?**
A: Yes, use App Password with 2FA enabled

**Q: Can I send attachments?**
A: Yes, PHPMailer supports attachments (feature can be added)

**Q: Is it secure?**
A: Yes, uses TLS/SSL encryption and authentication

### Getting Help

1. Check this guide
2. Enable debug mode
3. Check error logs
4. Review PHPMailer docs: https://github.com/PHPMailer/PHPMailer
5. Contact system administrator

## Resources

- **PHPMailer GitHub:** https://github.com/PHPMailer/PHPMailer
- **PHPMailer Docs:** https://github.com/PHPMailer/PHPMailer/wiki
- **Composer:** https://getcomposer.org/
- **Gmail App Passwords:** https://myaccount.google.com/apppasswords

---

**Last Updated:** November 8, 2024
**Version:** 1.0
**PHPMailer Version:** 6.9+
