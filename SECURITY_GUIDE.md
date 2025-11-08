# Security Implementation Guide

## Overview

This document outlines the security features implemented in the Grand Jyothi Construction website to protect against common web vulnerabilities and attacks.

## Security Features Implemented

### 1. CSRF Protection

**Location:** `includes/security.php`

**Functions:**
- `generateCsrfToken()` - Generates a unique CSRF token for each session
- `validateCsrfToken($token)` - Validates submitted CSRF tokens
- `getCsrfTokenField()` - Returns HTML input field with CSRF token

**Usage Example:**
```php
// In your form
<?= getCsrfTokenField() ?>

// In form processing
if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $error_message = 'Invalid security token. Please try again.';
}
```

**Implemented In:**
- Contact form (`contact.php`)

### 2. Rate Limiting

**Location:** `includes/security.php`

**Functions:**
- `checkRateLimit($action, $maxAttempts, $timeWindow)` - Checks if action is rate limited
- `getRateLimitRemaining($action, $timeWindow)` - Gets remaining time for rate limit

**Default Settings:**
- Max attempts: 5
- Time window: 300 seconds (5 minutes)

**Usage Example:**
```php
if (!checkRateLimit('contact_form', 5, 300)) {
    $remaining = getRateLimitRemaining('contact_form', 300);
    $error_message = 'Too many submissions. Please try again in ' . ceil($remaining / 60) . ' minutes.';
}
```

**Implemented In:**
- Contact form submissions
- Can be added to login forms, search forms, etc.

### 3. Input Sanitization

**Location:** `includes/security.php`

**Functions:**
- `sanitizeInput($data)` - Removes null bytes, control characters, and trims whitespace
- `sanitizeOutput($data)` - HTML entity encoding (in `includes/db.php`)

**Usage:**
```php
$name = sanitizeInput(trim($_POST['name'] ?? ''));
echo sanitizeOutput($name);
```

### 4. File Upload Validation

**Location:** `includes/security.php`

**Functions:**
- `validateFileUpload($file, $allowedTypes, $maxSize)` - Comprehensive file validation
- `generateSecureFilename($originalName)` - Generates random secure filenames

**Features:**
- MIME type validation
- File size checking
- Extension verification
- Upload error handling

**Usage Example:**
```php
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
$maxSize = 5 * 1024 * 1024; // 5MB

$result = validateFileUpload($_FILES['image'], $allowedTypes, $maxSize);
if ($result['success']) {
    $secureFilename = generateSecureFilename($result['file']['name']);
    // Process upload
}
```

### 5. Password Security

**Location:** `includes/security.php`

**Functions:**
- `validatePasswordStrength($password, $minLength)` - Validates password complexity
- `hashPassword($password)` - Hashes password using Argon2ID
- `verifyPassword($password, $hash)` - Verifies password against hash

**Password Requirements:**
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character

### 6. Security Headers

**Location:** `.htaccess`

**Headers Implemented:**
- `X-Frame-Options: SAMEORIGIN` - Prevents clickjacking
- `X-XSS-Protection: 1; mode=block` - XSS protection
- `X-Content-Type-Options: nosniff` - Prevents MIME sniffing
- `Referrer-Policy: strict-origin-when-cross-origin` - Controls referrer information
- `Content-Security-Policy` - Restricts resource loading
- `Permissions-Policy` - Controls browser features

**HTTPS (When Enabled):**
```apache
# Uncomment in .htaccess when using HTTPS
Header set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
```

### 7. File and Directory Protection

**Location:** `.htaccess`

**Protected:**
- Hidden files (starting with `.`)
- Database files (`.sql`, `.db`)
- Configuration files (`config.php`, `.env`)
- Logs directory
- PHP files in includes directory
- Directory browsing disabled

### 8. Session Security

**Location:** `config.php`

**Settings:**
- `session.cookie_httponly = 1` - Prevents JavaScript access to cookies
- `session.use_strict_mode = 1` - Prevents session fixation
- `session.cookie_samesite = Strict` - CSRF protection

### 9. Security Logging

**Location:** `includes/security.php`

**Function:**
- `logSecurityEvent($event, $level)` - Logs security events

**Log Levels:**
- INFO - General information
- WARNING - Potential security issues
- ERROR - Security errors

**Log Location:** `logs/security.log`

**Logged Events:**
- CSRF token failures
- Rate limit violations
- Form submissions
- Database errors

## Database Security

### PDO Configuration

**Location:** `includes/db.php`

**Features:**
- Prepared statements (prevents SQL injection)
- Exception error mode
- No emulated prepares
- UTF-8 character set

**Usage:**
```php
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = executeQuery($sql, [$email]);
```

## Best Practices

### 1. Always Use Prepared Statements
```php
// Good
$sql = "SELECT * FROM users WHERE id = ?";
executeQuery($sql, [$userId]);

// Bad - Never do this
$sql = "SELECT * FROM users WHERE id = $userId";
```

### 2. Sanitize All Input
```php
$name = sanitizeInput($_POST['name']);
$email = sanitizeInput($_POST['email']);
```

### 3. Escape All Output
```php
echo sanitizeOutput($userInput);
```

### 4. Use CSRF Tokens on All Forms
```php
<form method="POST">
    <?= getCsrfTokenField() ?>
    <!-- form fields -->
</form>
```

### 5. Implement Rate Limiting on Sensitive Actions
```php
if (!checkRateLimit('login', 5, 300)) {
    // Show error
}
```

### 6. Validate File Uploads
```php
$result = validateFileUpload($_FILES['file'], $allowedTypes, $maxSize);
```

### 7. Use Strong Password Hashing
```php
$hash = hashPassword($password);
```

## Security Checklist

- [x] CSRF protection on forms
- [x] Rate limiting on submissions
- [x] Input sanitization
- [x] Output escaping
- [x] SQL injection prevention (prepared statements)
- [x] XSS prevention
- [x] File upload validation
- [x] Security headers
- [x] Session security
- [x] Password hashing
- [x] Security logging
- [x] Directory protection
- [x] Error handling (no sensitive info exposed)

## Production Deployment

### Before Going Live:

1. **Enable HTTPS:**
   - Obtain SSL certificate
   - Uncomment HSTS header in `.htaccess`
   - Update `SITE_URL` in `config.php`

2. **Disable Error Display:**
   ```php
   // In config.php
   error_reporting(0);
   ini_set('display_errors', '0');
   ```

3. **Update Database Credentials:**
   - Use strong database password
   - Create dedicated database user with minimal privileges

4. **Set Proper File Permissions:**
   ```bash
   chmod 644 *.php
   chmod 755 directories
   chmod 600 config.php
   chmod 700 logs/
   ```

5. **Review Security Logs:**
   - Monitor `logs/security.log` regularly
   - Set up log rotation

6. **Enable Additional Security:**
   - Consider adding Web Application Firewall (WAF)
   - Implement IP-based rate limiting
   - Add two-factor authentication for admin

## Maintenance

### Regular Tasks:

1. **Update Dependencies:**
   - Keep PHP updated
   - Update third-party libraries

2. **Review Logs:**
   - Check security logs weekly
   - Investigate suspicious activity

3. **Backup:**
   - Regular database backups
   - Backup uploaded files

4. **Security Audits:**
   - Perform quarterly security reviews
   - Test for vulnerabilities

## Contact

For security concerns or to report vulnerabilities:
- Email: security@grandjyothi.com
- Phone: +91 98765 43210

## References

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [OWASP CSRF Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html)
