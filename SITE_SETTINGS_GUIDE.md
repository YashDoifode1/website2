# Site Settings Management Guide

## Overview

All site settings, contact details, and header information can now be managed through the `.env` file. This makes it easy to update your website's information without touching any code.

## Quick Start

1. Open the `.env` file in your project root
2. Update the values you want to change
3. Save the file
4. Refresh your website - changes appear immediately!

## Available Settings

### 🏢 Site Information

```env
# Your company/site name
SITE_NAME="Grand Jyothi Construction"

# Tagline displayed on homepage and meta tags
SITE_TAGLINE="Building your vision with excellence and trust"

# SEO description (appears in search results)
SITE_DESCRIPTION="Leading construction company in Nagpur offering residential, commercial, and industrial construction services."

# SEO keywords (comma-separated)
SITE_KEYWORDS="construction, nagpur, residential, commercial, industrial, interior design, renovation"

# Base URL of your website
APP_URL=http://localhost/constructioninnagpur
```

### 📞 Contact Information

```env
# Primary contact email
CONTACT_EMAIL=info@grandjyothi.com

# Contact phone number (include country code)
CONTACT_PHONE="+91 98765 43210"

# Full business address
CONTACT_ADDRESS="123 Construction Plaza, Dharampeth, Nagpur - 440010, Maharashtra, India"
```

### ⏰ Business Hours

```env
# Monday to Friday hours
BUSINESS_HOURS_WEEKDAY="9:00 AM - 6:00 PM"

# Saturday hours
BUSINESS_HOURS_SATURDAY="9:00 AM - 2:00 PM"

# Sunday hours (or "Closed")
BUSINESS_HOURS_SUNDAY="Closed"
```

### 🎨 Header/Logo Settings

```env
# Main logo text (appears in navigation)
SITE_LOGO_TEXT="Grand Jyothi"

# Logo subtitle/suffix
SITE_LOGO_SUBTITLE="Construction"

# Show home icon in logo (true/false)
SHOW_LOGO_ICON=true
```

### 🌐 Social Media Links

```env
FACEBOOK_URL=https://facebook.com/grandjyothi
TWITTER_URL=https://twitter.com/grandjyothi
INSTAGRAM_URL=https://instagram.com/grandjyothi
LINKEDIN_URL=https://linkedin.com/company/grandjyothi
```

### 💾 Database Configuration

```env
DB_HOST=localhost
DB_NAME=constructioninnagpur
DB_USER=root
DB_PASS=your_password_here
DB_CHARSET=utf8mb4
```

### 📧 Email Configuration

```env
# Email driver: 'mail' or 'smtp'
MAIL_DRIVER=mail

# SMTP settings (if using SMTP)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls

# From address and name
MAIL_FROM_ADDRESS=info@grandjyothi.com
MAIL_FROM_NAME="Grand Jyothi Construction"
```

### ⚙️ Other Settings

```env
# Application environment (development/production)
APP_ENV=development

# Enable debug mode (true/false)
APP_DEBUG=true

# Session lifetime in seconds
SESSION_LIFETIME=7200

# Items per page for pagination
ITEMS_PER_PAGE=12

# Maximum upload size in bytes
MAX_UPLOAD_SIZE=5242880

# Allowed image types (comma-separated)
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp

# Timezone
TIMEZONE=Asia/Kolkata
```

## Common Customization Examples

### Example 1: Change Company Name

**Before:**
```env
SITE_NAME="Grand Jyothi Construction"
SITE_LOGO_TEXT="Grand Jyothi"
```

**After:**
```env
SITE_NAME="ABC Builders"
SITE_LOGO_TEXT="ABC"
```

### Example 2: Update Contact Details

```env
CONTACT_EMAIL=contact@yourcompany.com
CONTACT_PHONE="+91 12345 67890"
CONTACT_ADDRESS="456 New Street, Mumbai - 400001, Maharashtra, India"
```

### Example 3: Change Business Hours

```env
BUSINESS_HOURS_WEEKDAY="8:00 AM - 7:00 PM"
BUSINESS_HOURS_SATURDAY="9:00 AM - 5:00 PM"
BUSINESS_HOURS_SUNDAY="10:00 AM - 2:00 PM"
```

### Example 4: Hide Logo Icon

```env
SHOW_LOGO_ICON=false
```

### Example 5: Update Social Media

```env
FACEBOOK_URL=https://facebook.com/yourpage
TWITTER_URL=https://twitter.com/yourhandle
INSTAGRAM_URL=https://instagram.com/yourprofile
LINKEDIN_URL=https://linkedin.com/company/yourcompany
```

## Where Settings Are Used

### SITE_NAME
- Browser title
- Email templates
- Footer
- Admin panel
- Meta tags

### SITE_TAGLINE
- Homepage hero section
- Meta description
- Footer

### CONTACT_EMAIL
- Contact page
- Footer
- Email reply-to address
- Admin notifications

### CONTACT_PHONE
- Contact page
- Footer
- Click-to-call links

### CONTACT_ADDRESS
- Contact page
- Footer
- Google Maps integration

### SITE_LOGO_TEXT & SITE_LOGO_SUBTITLE
- Navigation header
- All pages header
- Mobile menu

### BUSINESS_HOURS_*
- Contact page
- Footer
- About page

### Social Media URLs
- Footer social links
- Share buttons
- Contact page

## Tips & Best Practices

### 1. Use Quotes for Text with Spaces
```env
# Good
SITE_NAME="Grand Jyothi Construction"

# Also works
SITE_NAME=Grand Jyothi Construction
```

### 2. No Spaces Around = Sign
```env
# Good
SITE_NAME="Grand Jyothi Construction"

# Bad
SITE_NAME = "Grand Jyothi Construction"
```

### 3. Boolean Values
```env
# Use lowercase true/false
SHOW_LOGO_ICON=true
APP_DEBUG=false
```

### 4. Comments
```env
# This is a comment
SITE_NAME="Grand Jyothi Construction"  # Inline comments work too
```

### 5. Multi-line Values
For long text, keep it on one line:
```env
SITE_DESCRIPTION="This is a long description that should stay on one line even if it's very long"
```

### 6. Special Characters
If your value contains special characters, use quotes:
```env
CONTACT_PHONE="+91 98765 43210"
CONTACT_ADDRESS="123 Street, City - 440010, State, India"
```

## Testing Your Changes

After updating `.env`:

1. **Refresh Website**
   - Open your website in browser
   - Press Ctrl+F5 (hard refresh)
   - Check if changes appear

2. **Check Specific Pages**
   - Homepage - Logo, tagline
   - Contact page - Email, phone, address
   - Footer - All contact info, social links
   - About page - Business hours

3. **Test Email Settings**
   - Send a test email from admin panel
   - Check if it uses correct from address

## Troubleshooting

### Changes Not Appearing?

1. **Clear Browser Cache**
   - Press Ctrl+Shift+Delete
   - Clear cached images and files

2. **Check .env File**
   - Ensure file is named exactly `.env`
   - No spaces in variable names
   - Values are properly quoted if needed

3. **Restart Server** (if needed)
   - Stop Apache
   - Start Apache
   - Refresh website

### Common Errors

**Error: "Undefined constant"**
- Solution: Check spelling in `.env` file
- Ensure no typos in variable names

**Error: "Parse error"**
- Solution: Check for syntax errors
- Remove extra quotes or spaces

**Settings Not Loading**
- Solution: Verify `.env` file exists in project root
- Check file permissions (should be readable)

## Production Deployment

When deploying to production:

1. **Update Environment**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Update URLs**
   ```env
   APP_URL=https://yourdomain.com
   ```

3. **Secure Database**
   ```env
   DB_PASS=strong_password_here
   ```

4. **Configure Email**
   ```env
   MAIL_DRIVER=smtp
   MAIL_HOST=your-smtp-host
   MAIL_USERNAME=your-email
   MAIL_PASSWORD=your-password
   ```

5. **Update Contact Info**
   - Use production email addresses
   - Use production phone numbers
   - Update business address

## Backup & Recovery

### Backup Your Settings

```bash
# Create backup
copy .env .env.backup

# Or with date
copy .env .env.backup.2024-11-08
```

### Restore Settings

```bash
# Restore from backup
copy .env.backup .env
```

### Version Control

**Never commit `.env` to Git!**

The `.gitignore` file already excludes it:
```
.env
```

Only commit `.env.example` with dummy values.

## Advanced Usage

### Environment-Specific Settings

**Development (.env):**
```env
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost/constructioninnagpur
MAIL_DRIVER=mail
```

**Production (.env):**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://grandjyothi.com
MAIL_DRIVER=smtp
```

### Custom Variables

You can add your own variables:

1. **Add to .env:**
   ```env
   CUSTOM_MESSAGE="Welcome to our site!"
   ```

2. **Use in code:**
   ```php
   $message = env('CUSTOM_MESSAGE', 'Default message');
   echo $message;
   ```

## Quick Reference Card

| Setting | Purpose | Example |
|---------|---------|---------|
| `SITE_NAME` | Company name | "ABC Construction" |
| `SITE_TAGLINE` | Tagline/slogan | "Building Dreams" |
| `CONTACT_EMAIL` | Contact email | contact@abc.com |
| `CONTACT_PHONE` | Phone number | "+91 12345 67890" |
| `SITE_LOGO_TEXT` | Logo main text | "ABC" |
| `SITE_LOGO_SUBTITLE` | Logo subtitle | "Builders" |
| `SHOW_LOGO_ICON` | Show/hide icon | true or false |
| `BUSINESS_HOURS_*` | Operating hours | "9:00 AM - 6:00 PM" |
| `APP_DEBUG` | Debug mode | true or false |
| `MAIL_DRIVER` | Email method | mail or smtp |

## Support

For help with settings:
1. Check this guide
2. Review `.env.example` for format
3. Test changes on development first
4. Contact system administrator

---

**Last Updated:** November 8, 2024
**Version:** 1.0
