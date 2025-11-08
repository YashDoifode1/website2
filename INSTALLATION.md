# Installation Guide

## Quick Start Guide for Grand Jyothi Construction Website

### Step 1: Database Setup

1. **Start XAMPP**
   - Start Apache and MySQL services

2. **Create Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Click "New" to create a new database
   - Database name: `constructioninnagpur`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

3. **Import Schema**
   - Select the `constructioninnagpur` database
   - Click "Import" tab
   - Choose file: `database/schema.sql`
   - Click "Go"
   - Wait for success message

### Step 2: Configure Database Connection

1. Open `includes/db.php`
2. Verify these settings match your setup:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'constructioninnagpur');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Leave empty for XAMPP default
   ```

### Step 3: Access the Website

1. **Public Website**
   - URL: `http://localhost/constructioninnagpur/`
   - Browse all pages: Home, About, Services, Projects, Team, Testimonials, Contact

2. **Admin Panel**
   - URL: `http://localhost/constructioninnagpur/admin/`
   - Username: `admin`
   - Password: `admin123`

### Step 4: Test the System

1. **Test Public Pages**
   - Navigate through all pages
   - Submit a test contact form
   - Verify data displays correctly

2. **Test Admin Panel**
   - Login with default credentials
   - View dashboard statistics
   - Try adding/editing/deleting records in each section
   - Check contact messages

### Step 5: Customize Content

1. **Add Your Content**
   - Login to admin panel
   - Add your actual services
   - Add your real projects
   - Add team members
   - Add testimonials

2. **Upload Images**
   - Place images in `assets/images/` folder
   - Reference them by filename in admin panel
   - Recommended image sizes:
     - Projects: 800x600px
     - Team photos: 300x300px (square)
     - Service icons: Use Feather icon names

### Step 6: Security (IMPORTANT!)

1. **Change Admin Password**
   ```sql
   -- Generate new password hash in PHP:
   <?php echo password_hash('your_new_password', PASSWORD_DEFAULT); ?>
   
   -- Update in database:
   UPDATE admin_users 
   SET password_hash = 'YOUR_NEW_HASH' 
   WHERE username = 'admin';
   ```

2. **Update Configuration**
   - Edit `config.php` with your actual contact details
   - Update `includes/db.php` if needed

3. **Production Settings**
   - In `config.php`, change:
     ```php
     error_reporting(0);
     ini_set('display_errors', '0');
     ```

### Troubleshooting

**Database Connection Error**
- Verify MySQL is running in XAMPP
- Check database credentials in `includes/db.php`
- Ensure database `constructioninnagpur` exists

**Admin Login Not Working**
- Clear browser cache and cookies
- Verify admin user exists in database
- Check `admin_users` table has the default user

**Images Not Displaying**
- Ensure images are uploaded to `assets/images/`
- Check file permissions (should be readable)
- Verify filename matches exactly (case-sensitive)

**Contact Form Not Working**
- Check database connection
- Verify `contact_messages` table exists
- Check PHP error logs

### File Permissions

For Linux/Mac servers:
```bash
chmod 755 constructioninnagpur/
chmod 755 constructioninnagpur/assets/
chmod 777 constructioninnagpur/assets/images/
```

### Next Steps

1. ✅ Database imported successfully
2. ✅ Admin login working
3. ✅ All pages accessible
4. ⏳ Add your actual content
5. ⏳ Upload your images
6. ⏳ Change admin password
7. ⏳ Update contact information
8. ⏳ Test contact form
9. ⏳ Deploy to production server

### Support

If you encounter any issues:
1. Check PHP error logs
2. Verify database connection
3. Clear browser cache
4. Check file permissions
5. Review README.md for detailed documentation

---

**Congratulations! Your website is ready to use! 🎉**
