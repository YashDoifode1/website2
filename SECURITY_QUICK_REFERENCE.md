# Security Quick Reference Card

## CSRF Protection

```php
// Include security module
require_once __DIR__ . '/includes/security.php';

// In your form HTML
<form method="POST">
    <?= getCsrfTokenField() ?>
    <!-- form fields -->
</form>

// In form processing
if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $error = 'Invalid security token';
    logSecurityEvent('CSRF validation failed', 'WARNING');
}
```

## Rate Limiting

```php
// Check rate limit (5 attempts per 5 minutes)
if (!checkRateLimit('action_name', 5, 300)) {
    $remaining = getRateLimitRemaining('action_name', 300);
    $error = "Too many attempts. Try again in " . ceil($remaining / 60) . " minutes.";
    logSecurityEvent('Rate limit exceeded', 'WARNING');
}
```

## Input Sanitization

```php
// Sanitize user input
$name = sanitizeInput($_POST['name']);
$email = sanitizeInput($_POST['email']);

// Output escaping (always!)
echo sanitizeOutput($userInput);
```

## File Upload

```php
$allowedTypes = ['image/jpeg', 'image/png'];
$maxSize = 5 * 1024 * 1024; // 5MB

$result = validateFileUpload($_FILES['file'], $allowedTypes, $maxSize);
if ($result['success']) {
    $filename = generateSecureFilename($result['file']['name']);
    move_uploaded_file($result['file']['tmp_name'], "uploads/$filename");
}
```

## Password Handling

```php
// Validate strength
$validation = validatePasswordStrength($password, 8);
if (!$validation['valid']) {
    $error = $validation['message'];
}

// Hash password
$hash = hashPassword($password);

// Verify password
if (verifyPassword($password, $hash)) {
    // Password correct
}
```

## Database Queries

```php
// Always use prepared statements
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = executeQuery($sql, [$email]);

// Never concatenate user input
// BAD: $sql = "SELECT * FROM users WHERE email = '$email'";
```

## Security Logging

```php
// Log security events
logSecurityEvent('User login successful', 'INFO');
logSecurityEvent('Failed login attempt', 'WARNING');
logSecurityEvent('SQL injection attempt detected', 'ERROR');
```

## Common Actions to Protect

### Contact Forms
```php
require_once __DIR__ . '/includes/security.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid token';
    } elseif (!checkRateLimit('contact', 5, 300)) {
        $error = 'Too many submissions';
    } else {
        $data = sanitizeInput($_POST);
        // Process form
    }
}
```

### Login Forms
```php
if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
    $error = 'Invalid token';
} elseif (!checkRateLimit('login', 3, 600)) {
    $error = 'Too many login attempts';
} else {
    $email = sanitizeInput($_POST['email']);
    // Verify credentials
}
```

### Search Forms
```php
if (!checkRateLimit('search', 10, 60)) {
    $error = 'Too many searches';
} else {
    $query = sanitizeInput($_POST['query']);
    // Perform search
}
```

## Security Headers (Already Set in .htaccess)

- ✓ X-Frame-Options: SAMEORIGIN
- ✓ X-XSS-Protection: 1; mode=block
- ✓ X-Content-Type-Options: nosniff
- ✓ Referrer-Policy: strict-origin-when-cross-origin
- ✓ Content-Security-Policy
- ✓ Permissions-Policy

## Protected Files/Directories

- ✓ `.htaccess` files
- ✓ `config.php`
- ✓ `.env` files
- ✓ `logs/` directory
- ✓ `.sql` and `.db` files
- ✓ PHP files in `includes/`

## Quick Security Checklist

**Every Form:**
- [ ] CSRF token included
- [ ] Rate limiting implemented
- [ ] Input sanitization
- [ ] Output escaping

**Every Database Query:**
- [ ] Using prepared statements
- [ ] Parameters properly bound
- [ ] No string concatenation

**Every File Upload:**
- [ ] MIME type validation
- [ ] File size check
- [ ] Secure filename generation
- [ ] Proper storage location

**Every User Input:**
- [ ] Sanitized on input
- [ ] Escaped on output
- [ ] Validated for type/format

## Emergency Contacts

**Security Issues:**
- Email: security@grandjyothi.com
- Phone: +91 98765 43210

**Log Location:**
- `logs/security.log`

## Additional Resources

- Full Guide: `SECURITY_GUIDE.md`
- Implementation Details: `IMPLEMENTATION_SUMMARY.md`
- Security Module: `includes/security.php`
