# Grand Jyothi Construction Website

A lightweight construction company website built with **Core PHP 8+**, **MySQL**, and **Pico.css**.

## 🚀 Features

- **Public Website**
  - Home page with hero banner, featured services, projects, and testimonials
  - About page with company information and values
  - Services page listing all construction services
  - Projects portfolio with completed works
  - Team page showcasing team members
  - Testimonials from satisfied clients
  - Contact form with validation

- **Admin Panel**
  - Secure login system with password hashing
  - Dashboard with statistics and quick links
  - CRUD operations for:
    - Services
    - Projects
    - Team Members
    - Testimonials
  - View and manage contact form messages
  - Session-based authentication

## 📋 Requirements

- PHP 8.0 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- PDO extension enabled

## 🛠️ Installation

1. **Clone or download the project**
   ```bash
   cd c:/xampp/htdocs/clone/
   ```

2. **Create the database**
   - Open phpMyAdmin or MySQL client
   - Import the database schema:
     ```bash
     mysql -u root -p < database/schema.sql
     ```
   - Or manually create the database and run the SQL from `database/schema.sql`

3. **Configure database connection**
   - Open `includes/db.php`
   - Update database credentials if needed:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'constructioninnagpur');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     ```

4. **Set up file permissions**
   - Ensure the `assets/images/` directory is writable for image uploads

5. **Access the website**
   - Public site: `http://localhost/constructioninnagpur/`
   - Admin panel: `http://localhost/constructioninnagpur/admin/`

## 🔐 Default Admin Credentials

- **Username:** admin
- **Password:** admin123

**⚠️ IMPORTANT:** Change the default password immediately after first login!

To change the password, run this SQL query with your new password:
```sql
UPDATE admin_users 
SET password_hash = '$2y$10$YOUR_NEW_HASH_HERE' 
WHERE username = 'admin';
```

Generate a new hash in PHP:
```php
echo password_hash('your_new_password', PASSWORD_DEFAULT);
```

## 📁 Project Structure

```
constructioninnagpur/
├── index.php                 # Home page
├── about.php                 # About page
├── services.php              # Services listing
├── projects.php              # Projects portfolio
├── team.php                  # Team members
├── testimonials.php          # Client testimonials
├── contact.php               # Contact form
│
├── admin/                    # Admin panel
│   ├── index.php            # Login page
│   ├── dashboard.php        # Admin dashboard
│   ├── services.php         # Manage services
│   ├── projects.php         # Manage projects
│   ├── team.php             # Manage team
│   ├── testimonials.php     # Manage testimonials
│   ├── messages.php         # View contact messages
│   ├── logout.php           # Logout handler
│   └── includes/
│       ├── admin_header.php # Admin header
│       └── admin_footer.php # Admin footer
│
├── includes/                 # Shared includes
│   ├── db.php               # Database connection
│   ├── auth.php             # Authentication system
│   ├── header.php           # Public header
│   └── footer.php           # Public footer
│
├── assets/                   # Static assets
│   ├── images/              # Image uploads
│   ├── css/                 # Custom CSS (optional)
│   └── js/                  # Custom JS (optional)
│
├── database/
│   └── schema.sql           # Database schema
│
└── README.md                # This file
```

## 🎨 Customization

### Changing Colors
Edit the CSS variables in `includes/header.php`:
```css
:root {
    --primary: #ff6b35;
    --primary-hover: #e55a2b;
}
```

### Adding Custom Styles
Create a custom CSS file in `assets/css/` and include it in the header.

### Feather Icons
The project uses Feather Icons. Browse available icons at: https://feathericons.com/

## 🔒 Security Features

- **Password Hashing:** Uses PHP's `password_hash()` and `password_verify()`
- **Prepared Statements:** All database queries use PDO prepared statements
- **XSS Protection:** Output sanitization with `htmlspecialchars()`
- **Session Management:** Secure session handling for admin authentication
- **Input Validation:** Server-side validation for all forms

## 📝 Database Tables

- `services` - Construction services offered
- `projects` - Completed projects portfolio
- `team` - Team members information
- `testimonials` - Client testimonials
- `contact_messages` - Contact form submissions
- `admin_users` - Admin user accounts

## 🚀 Deployment

1. Upload files to your web server
2. Import the database schema
3. Update database credentials in `includes/db.php`
4. Set proper file permissions
5. Change default admin password
6. Test all functionality

## 📧 Support

For issues or questions, please contact the development team.

## 📄 License

This project is built for Grand Jyothi Construction. All rights reserved.

## 🙏 Credits

- **Pico.css** - https://picocss.com
- **Feather Icons** - https://feathericons.com
- **PHP** - https://php.net

---

**Built with ❤️ using Core PHP, MySQL, and Pico.css**
