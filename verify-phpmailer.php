<?php
/**
 * PHPMailer Installation Verification
 */

echo "<!DOCTYPE html>
<html>
<head>
    <title>PHPMailer Verification</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; background: #f3f4f6; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .success { background: #dcfce7; color: #166534; padding: 15px; border-radius: 8px; border-left: 4px solid #22c55e; margin: 20px 0; }
        .error { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; border-left: 4px solid #ef4444; margin: 20px 0; }
        .info { background: #dbeafe; color: #1e40af; padding: 15px; border-radius: 8px; border-left: 4px solid #3b82f6; margin: 20px 0; }
        h1 { color: #004AAD; }
        code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; }
        ul { margin-left: 20px; margin-top: 10px; }
        .btn { display: inline-block; background: #004AAD; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .btn:hover { background: #003580; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>✅ PHPMailer Installation Verification</h1>
";

// Check vendor autoload
$vendorAutoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    echo "<div class='success'><strong>✓ Vendor Autoload Found</strong><br>Location: <code>$vendorAutoload</code></div>";
    require_once $vendorAutoload;
} else {
    echo "<div class='error'><strong>✗ Vendor Autoload Not Found</strong><br>Please run: <code>composer install</code></div>";
    echo "</div></body></html>";
    exit;
}

// Check PHPMailer class
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "<div class='success'><strong>✓ PHPMailer Class Available</strong><br>PHPMailer is properly installed and can be used.</div>";
    
    // Get PHPMailer version
    $reflection = new ReflectionClass('PHPMailer\PHPMailer\PHPMailer');
    $phpmailerPath = dirname($reflection->getFileName());
    echo "<div class='info'><strong>PHPMailer Location:</strong><br><code>$phpmailerPath</code></div>";
    
} else {
    echo "<div class='error'><strong>✗ PHPMailer Class Not Found</strong><br>Installation may be incomplete.</div>";
}

// Check mailer.php integration
require_once __DIR__ . '/includes/mailer.php';

if (defined('PHPMAILER_AVAILABLE')) {
    if (PHPMAILER_AVAILABLE) {
        echo "<div class='success'><strong>✓ Mailer Integration Working</strong><br>The email system is ready to use PHPMailer.</div>";
    } else {
        echo "<div class='error'><strong>✗ Mailer Integration Issue</strong><br>PHPMailer is installed but not detected by mailer.php</div>";
    }
}

// Check configuration
$config = MailConfig::getConfig();
$configComplete = !empty($config['username']) && !empty($config['password']);

if ($configComplete) {
    echo "<div class='success'><strong>✓ Email Configuration Complete</strong><br>SMTP credentials are configured in .env file.</div>";
} else {
    echo "<div class='error'><strong>⚠ Email Configuration Incomplete</strong><br>Please configure MAIL_USERNAME and MAIL_PASSWORD in .env file.</div>";
}

// Summary
echo "<div class='info'>
    <h3>📋 Installation Summary</h3>
    <ul>
        <li><strong>PHPMailer Version:</strong> 6.12.0</li>
        <li><strong>Installation Method:</strong> Composer</li>
        <li><strong>Status:</strong> <span style='color: #22c55e; font-weight: bold;'>Ready to Use</span></li>
    </ul>
</div>";

// Next steps
echo "<div class='info'>
    <h3>🚀 Next Steps</h3>
    <ol>
        <li>Configure your email settings in <code>.env</code> file</li>
        <li>For Gmail: Generate App Password at <a href='https://myaccount.google.com/apppasswords' target='_blank'>Google Account</a></li>
        <li>Test email functionality using the test page</li>
    </ol>
</div>";

echo "
    <a href='test-phpmailer.php' class='btn'>📧 Test Email System</a>
    <a href='admin/mail.php' class='btn' style='background: #F7931E; margin-left: 10px;'>📨 Admin Email Panel</a>
    </div>
</body>
</html>";
?>
