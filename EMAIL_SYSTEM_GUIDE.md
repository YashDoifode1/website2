# Email Notification System Guide

## Overview

The email notification system allows administrators to send professional email notifications to contacts who have submitted inquiries through the contact form. The system supports multiple email types including blog notifications, construction updates, and custom messages.

## Features

### ✅ Email Types

1. **Blog Notifications**
   - Notify contacts about new blog posts
   - Includes blog title, excerpt, and link
   - Professional HTML template

2. **Construction Updates**
   - Send project progress updates
   - Include project name and status
   - Customizable message content

3. **Custom Notifications**
   - Send any custom message
   - Flexible HTML formatting
   - Personalized content

### ✅ Recipient Management

- Send to all contacts
- Select specific recipients
- View contact list with emails
- Bulk email sending with progress tracking

### ✅ Email Templates

- Professional HTML templates
- Branded with company colors
- Mobile-responsive design
- Consistent styling

### ✅ Logging & Tracking

- Email activity logging
- Success/failure tracking
- Error reporting
- Audit trail

## Setup Instructions

### 1. Configure Email Settings

Edit your `.env` file:

```env
# For PHP mail() function (default)
MAIL_DRIVER=mail
MAIL_FROM_ADDRESS=info@grandjyothi.com
MAIL_FROM_NAME="Grand Jyothi Construction"

# For SMTP (Gmail example)
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Grand Jyothi Construction"
```

### 2. Gmail SMTP Setup

If using Gmail SMTP:

1. **Enable 2-Factor Authentication**
   - Go to Google Account Settings
   - Security → 2-Step Verification
   - Enable it

2. **Generate App Password**
   - Visit: https://myaccount.google.com/apppasswords
   - Select "Mail" and your device
   - Copy the 16-character password
   - Use this as `MAIL_PASSWORD` in `.env`

3. **Update .env**
   ```env
   MAIL_DRIVER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=youremail@gmail.com
   MAIL_PASSWORD=your-16-char-app-password
   MAIL_ENCRYPTION=tls
   ```

### 3. Test Email Configuration

Create a test file `test-email.php`:

```php
<?php
require_once 'includes/mailer.php';

$result = sendEmail(
    'test@example.com',
    'Test Email',
    '<h1>Test</h1><p>This is a test email.</p>'
);

echo $result ? 'Email sent successfully!' : 'Email failed to send.';
```

## Using the Email System

### Access the Email Panel

1. Log in to Admin Panel
2. Click "Email Notifications" in sidebar
3. You'll see the email composition interface

### Sending Blog Notifications

1. Select **Email Type**: "Blog Notification"
2. Choose a blog post from dropdown
3. Enter email subject (e.g., "New Blog Post: [Title]")
4. Add custom message (optional)
5. Select recipients
6. Click "Send Emails"

**Example:**
```
Subject: New Blog Post: Top 10 Construction Tips
Message: We've just published a new article that you might find interesting!
```

### Sending Construction Updates

1. Select **Email Type**: "Construction Update"
2. Enter project name
3. Select project status
4. Write update message
5. Select recipients
6. Click "Send Emails"

**Example:**
```
Project Name: Skyline Apartments
Status: Structure
Message: We're pleased to announce that the structure work for Skyline Apartments is now 75% complete. The project is on schedule for completion by March 2025.
```

### Sending Custom Notifications

1. Select **Email Type**: "Custom Notification"
2. Enter subject
3. Write your message (HTML supported)
4. Select recipients
5. Click "Send Emails"

**Example:**
```
Subject: Special Diwali Offer - 15% Off All Packages
Message: Celebrate Diwali with Grand Jyothi Construction! Get 15% off on all our construction packages. Offer valid until October 31st.
```

## Email Templates

### Blog Notification Template

Features:
- Company branding header
- Blog title and excerpt
- "Read Full Article" button
- Footer with unsubscribe info

### Construction Update Template

Features:
- Update title and message
- Project details box
- Status indicator
- Contact information

### Custom Notification Template

Features:
- Flexible content area
- Optional call-to-action button
- Customizable footer
- Professional styling

## Recipient Management

### Contact Sources

Recipients are automatically collected from:
- Contact form submissions
- Each submission adds email to recipient list
- Duplicates are automatically filtered

### Selecting Recipients

**Option 1: All Contacts**
- Check "Select All Contacts"
- Sends to everyone in database

**Option 2: Specific Recipients**
- Browse contact list
- Check individual recipients
- Can select multiple

### Recipient List Display

Shows:
- Contact name
- Email address
- Submission date
- Total count

## Email Logging

### Log Location
`logs/email.log`

### Log Format
```
[2024-11-08 17:30:45] [SUCCESS] To: john@example.com | Subject: New Blog Post
[2024-11-08 17:30:46] [FAILED] To: invalid@email | Subject: New Blog Post | Error: Invalid email
```

### Viewing Logs

Check logs via:
1. File manager
2. FTP client
3. Command line: `tail -f logs/email.log`

## Troubleshooting

### Emails Not Sending

**Check:**
1. `.env` configuration is correct
2. SMTP credentials are valid
3. Internet connection is active
4. Email addresses are valid
5. Check `logs/email.log` for errors

**Common Issues:**

**1. SMTP Authentication Failed**
- Verify username and password
- For Gmail, use App Password not regular password
- Check 2FA is enabled

**2. Connection Timeout**
- Verify SMTP host and port
- Check firewall settings
- Try different port (587 or 465)

**3. Emails Going to Spam**
- Add SPF record to DNS
- Add DKIM signature
- Use verified sender address
- Avoid spam trigger words

**4. PHP mail() Not Working**
- Check if sendmail is configured
- Verify PHP mail settings
- Consider using SMTP instead

### Testing Email Delivery

**Test with PHP mail():**
```php
<?php
$to = 'test@example.com';
$subject = 'Test Email';
$message = 'This is a test';
$headers = 'From: info@grandjyothi.com';

if (mail($to, $subject, $message, $headers)) {
    echo 'Email sent!';
} else {
    echo 'Email failed!';
}
```

**Test SMTP Connection:**
```bash
telnet smtp.gmail.com 587
```

## Best Practices

### 1. Email Frequency
- Don't send too frequently (max 1-2 per week)
- Respect user preferences
- Provide unsubscribe option

### 2. Content Quality
- Keep subject lines clear and concise
- Write valuable content
- Use proper formatting
- Proofread before sending

### 3. Recipient Management
- Regularly clean invalid emails
- Segment recipients by interest
- Respect opt-outs

### 4. Compliance
- Include physical address
- Provide unsubscribe link
- Honor unsubscribe requests
- Follow CAN-SPAM Act guidelines

### 5. Testing
- Always preview before sending
- Test on multiple email clients
- Check mobile responsiveness
- Verify all links work

## Email Statistics

The system tracks:
- Total contacts
- Emails sent
- Success rate
- Failed deliveries

View statistics in the Email Notifications panel.

## Advanced Usage

### Custom Email Templates

Edit `includes/mailer.php` to add new templates:

```php
'your_template' => '
    <div style="...">
        <h1>{{title}}</h1>
        <p>{{content}}</p>
    </div>
'
```

### Programmatic Sending

Send emails from code:

```php
require_once 'includes/mailer.php';

// Simple email
sendEmail('user@example.com', 'Subject', '<p>Message</p>');

// Bulk emails
$recipients = ['user1@example.com', 'user2@example.com'];
$results = sendBulkEmails($recipients, 'Subject', '<p>Message</p>');

// Using template
$html = getEmailTemplate('blog_notification', [
    'site_name' => 'Grand Jyothi',
    'title' => 'Blog Title',
    'excerpt' => 'Blog excerpt...',
    'link' => 'https://example.com/blog'
]);
sendEmail('user@example.com', 'New Blog', $html);
```

## Security Considerations

### 1. Protect Email Credentials
- Never commit `.env` to version control
- Use strong passwords
- Rotate credentials regularly

### 2. Rate Limiting
- System includes 0.1s delay between emails
- Prevents server overload
- Avoids spam filters

### 3. Input Validation
- All email addresses are validated
- HTML is sanitized
- SQL injection prevention

### 4. Logging
- All email activity is logged
- Helps track issues
- Audit trail for compliance

## Support

### Common Questions

**Q: Can I send attachments?**
A: Not currently supported. Consider linking to files instead.

**Q: How many emails can I send at once?**
A: No hard limit, but recommended max 500 per batch.

**Q: Can recipients reply to emails?**
A: Yes, replies go to `MAIL_FROM_ADDRESS`.

**Q: How do I add an unsubscribe link?**
A: Implement unsubscribe functionality and add link to templates.

### Getting Help

1. Check this guide
2. Review `logs/email.log`
3. Test email configuration
4. Contact system administrator

## Future Enhancements

Planned features:
- Email scheduling
- A/B testing
- Analytics dashboard
- Unsubscribe management
- Email templates editor
- Attachment support
- Email queue system

---

**Last Updated:** November 8, 2024
**Version:** 1.0
