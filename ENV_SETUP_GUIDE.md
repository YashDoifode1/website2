# Environment Configuration Setup Guide

## Overview

The application now uses a `.env` file system for managing configuration variables. This provides better security and flexibility for different environments (development, staging, production).

## Setup Instructions

### 1. Create Your .env File

Copy the example file to create your actual environment file:

```bash
# In the project root directory
copy .env.example .env
```

Or manually create a file named `.env` in the project root.

### 2. Configure Your Environment

Edit the `.env` file with your actual values:

```env
# Application Settings
APP_NAME="Grand Jyothi Construction"
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/constructioninnagpur

# Database Configuration
DB_HOST=localhost
DB_NAME=constructioninnagpur
DB_USER=root
DB_PASS=your_password_here
DB_CHARSET=utf8mb4

# Contact Information
CONTACT_EMAIL=your-email@example.com
CONTACT_PHONE="+91 98765 43210"
```

### 3. Important Security Notes

**Never commit your `.env` file to version control!**

- The `.env` file is already listed in `.gitignore`
- Only commit `.env.example` with dummy values
- Each environment (dev, staging, production) should have its own `.env` file

## Environment Variables Reference

### Application Settings

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name | Grand Jyothi Construction |
| `APP_ENV` | Environment (development/production) | development |
| `APP_DEBUG` | Enable debug mode | true |
| `APP_URL` | Base URL of the application | http://localhost/constructioninnagpur |

### Database Configuration

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | Database host | localhost |
| `DB_NAME` | Database name | constructioninnagpur |
| `DB_USER` | Database username | root |
| `DB_PASS` | Database password | (empty) |
| `DB_CHARSET` | Database character set | utf8mb4 |

### Site Information

| Variable | Description |
|----------|-------------|
| `SITE_NAME` | Website name |
| `SITE_TAGLINE` | Website tagline |
| `CONTACT_EMAIL` | Contact email address |
| `CONTACT_PHONE` | Contact phone number |
| `CONTACT_ADDRESS` | Physical address |

### Social Media

| Variable | Description |
|----------|-------------|
| `FACEBOOK_URL` | Facebook page URL |
| `TWITTER_URL` | Twitter profile URL |
| `INSTAGRAM_URL` | Instagram profile URL |
| `LINKEDIN_URL` | LinkedIn company URL |

### Security Settings

| Variable | Description | Default |
|----------|-------------|---------|
| `SESSION_LIFETIME` | Session lifetime in seconds | 7200 |
| `CSRF_TOKEN_NAME` | CSRF token field name | csrf_token |

### Upload Settings

| Variable | Description | Default |
|----------|-------------|---------|
| `MAX_UPLOAD_SIZE` | Maximum file upload size in bytes | 5242880 (5MB) |
| `ALLOWED_IMAGE_TYPES` | Comma-separated allowed image extensions | jpg,jpeg,png,gif,webp |

### Other Settings

| Variable | Description | Default |
|----------|-------------|---------|
| `ITEMS_PER_PAGE` | Items per page for pagination | 12 |
| `TIMEZONE` | Application timezone | Asia/Kolkata |

## Usage in Code

### Getting Environment Variables

```php
// Get environment variable with default
$dbHost = env('DB_HOST', 'localhost');

// Get boolean value
$isDebug = env('APP_DEBUG', false);

// Get numeric value
$maxSize = env('MAX_UPLOAD_SIZE', 5242880);

// Check if variable exists
if (hasEnv('MAIL_HOST')) {
    // Configure mail
}
```

### Type Conversion

The `env()` function automatically converts values:

- `true`, `(true)` → boolean `true`
- `false`, `(false)` → boolean `false`
- `null`, `(null)` → `null`
- `empty`, `(empty)` → empty string `""`
- Numeric strings → int or float
- Everything else → string

## Environment-Specific Configuration

### Development Environment

```env
APP_ENV=development
APP_DEBUG=true
DB_HOST=localhost
DB_USER=root
DB_PASS=
```

### Production Environment

```env
APP_ENV=production
APP_DEBUG=false
DB_HOST=your-production-host
DB_USER=production_user
DB_PASS=strong_password_here
```

**Production Checklist:**
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Use strong database password
- [ ] Update `APP_URL` to production domain
- [ ] Configure email settings
- [ ] Set proper file permissions (600 for .env)

## File Permissions

### Linux/Mac

```bash
chmod 600 .env
```

### Windows

Right-click `.env` → Properties → Security → Edit permissions to restrict access

## Troubleshooting

### .env File Not Loading

**Check:**
1. File is named exactly `.env` (not `.env.txt`)
2. File is in the project root directory
3. File has proper read permissions
4. No syntax errors in the file

### Variables Not Working

**Check:**
1. No spaces around `=` sign: `KEY=value` not `KEY = value`
2. Quotes are optional but recommended for strings with spaces
3. No trailing spaces after values
4. Comments start with `#` at the beginning of line

### Common Errors

**Error:** "Cannot load .env file"
- **Solution:** Ensure `.env` file exists and is readable

**Error:** "Undefined constant"
- **Solution:** Check that `env.php` is loaded before using `env()` function

**Error:** "Database connection failed"
- **Solution:** Verify database credentials in `.env` file

## Security Best Practices

1. **Never commit `.env` to version control**
   - Always use `.gitignore`
   - Only commit `.env.example`

2. **Use strong passwords**
   - Especially for production database
   - Use password generators

3. **Restrict file access**
   - Set proper file permissions
   - `.env` should only be readable by web server

4. **Separate environments**
   - Different `.env` for dev/staging/production
   - Never use production credentials in development

5. **Regular updates**
   - Update `.env.example` when adding new variables
   - Document all environment variables

## Backup and Recovery

### Backup Your .env

```bash
# Create encrypted backup
cp .env .env.backup
# Store securely, never in version control
```

### Recovery

If you lose your `.env` file:
1. Copy `.env.example` to `.env`
2. Fill in your actual values
3. Test database connection
4. Verify all features work

## Migration from Old Config

The old hardcoded values in `config.php` and `includes/db.php` now use `.env` variables with fallback defaults. This means:

- **Backward compatible**: Works without `.env` file using defaults
- **Flexible**: Override any value via `.env`
- **Secure**: Sensitive data not in code

## Support

For issues with environment configuration:
1. Check this guide
2. Verify `.env` syntax
3. Check file permissions
4. Review error logs

---

**Last Updated:** November 8, 2024
**Version:** 1.0
