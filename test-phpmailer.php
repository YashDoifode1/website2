<?php
/**
 * PHPMailer Test Script
 * 
 * Test email functionality with PHPMailer
 */

require_once __DIR__ . '/includes/mailer.php';

// Check if PHPMailer is available
if (!PHPMAILER_AVAILABLE) {
    echo '<div style="padding: 20px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin: 20px;">';
    echo '<h2>⚠️ PHPMailer Not Installed</h2>';
    echo '<p>PHPMailer library is not available. Please install it:</p>';
    echo '<ol>';
    echo '<li>Run: <code>composer install</code></li>';
    echo '<li>Or use the installation script: <code>install-phpmailer.bat</code></li>';
    echo '<li>Or see <code>PHPMAILER_SETUP.md</code> for manual installation</li>';
    echo '</ol>';
    echo '<p><strong>Note:</strong> The system will fallback to PHP mail() function.</p>';
    echo '</div>';
}

// Configuration check
$config = MailConfig::getConfig();
$configOk = !empty($config['username']) && !empty($config['password']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHPMailer Test</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #004AAD;
            margin-bottom: 10px;
        }
        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .status-success {
            background: #dcfce7;
            color: #166534;
            border-left: 4px solid #22c55e;
        }
        .status-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }
        .status-warning {
            background: #fef3c7;
            color: #92400e;
            border-left: 4px solid #f59e0b;
        }
        .status-info {
            background: #dbeafe;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #374151;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            background: #004AAD;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #003580;
        }
        .config-info {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .config-item:last-child {
            border-bottom: none;
        }
        code {
            background: #f3f4f6;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 PHPMailer Test</h1>
        <p style="color: #6b7280; margin-bottom: 20px;">Test your email configuration</p>

        <?php if (!$configOk): ?>
        <div class="status status-warning">
            <strong>⚠️ Configuration Incomplete</strong>
            <p>Please configure your email settings in the <code>.env</code> file:</p>
            <ul style="margin-top: 10px; margin-left: 20px;">
                <li>MAIL_USERNAME</li>
                <li>MAIL_PASSWORD</li>
            </ul>
        </div>
        <?php endif; ?>

        <div class="config-info">
            <h3 style="margin-bottom: 10px;">Current Configuration</h3>
            <div class="config-item">
                <span>PHPMailer Status:</span>
                <strong style="color: <?= PHPMAILER_AVAILABLE ? '#22c55e' : '#ef4444' ?>">
                    <?= PHPMAILER_AVAILABLE ? '✓ Installed' : '✗ Not Installed' ?>
                </strong>
            </div>
            <div class="config-item">
                <span>Driver:</span>
                <strong><?= $config['driver'] ?></strong>
            </div>
            <div class="config-item">
                <span>SMTP Host:</span>
                <strong><?= $config['host'] ?></strong>
            </div>
            <div class="config-item">
                <span>SMTP Port:</span>
                <strong><?= $config['port'] ?></strong>
            </div>
            <div class="config-item">
                <span>Encryption:</span>
                <strong><?= $config['encryption'] ?></strong>
            </div>
            <div class="config-item">
                <span>From Address:</span>
                <strong><?= $config['from_address'] ?></strong>
            </div>
            <div class="config-item">
                <span>Username Configured:</span>
                <strong style="color: <?= !empty($config['username']) ? '#22c55e' : '#ef4444' ?>">
                    <?= !empty($config['username']) ? '✓ Yes' : '✗ No' ?>
                </strong>
            </div>
            <div class="config-item">
                <span>Password Configured:</span>
                <strong style="color: <?= !empty($config['password']) ? '#22c55e' : '#ef4444' ?>">
                    <?= !empty($config['password']) ? '✓ Yes' : '✗ No' ?>
                </strong>
            </div>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $to = trim($_POST['to'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $message = trim($_POST['message'] ?? '');

            if (empty($to) || empty($subject) || empty($message)) {
                echo '<div class="status status-error">';
                echo '<strong>❌ Error</strong>';
                echo '<p>All fields are required.</p>';
                echo '</div>';
            } elseif (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                echo '<div class="status status-error">';
                echo '<strong>❌ Error</strong>';
                echo '<p>Invalid email address.</p>';
                echo '</div>';
            } else {
                $html = '<div style="font-family: Arial, sans-serif; padding: 20px;">';
                $html .= '<h2 style="color: #004AAD;">Test Email from PHPMailer</h2>';
                $html .= '<p>' . nl2br(htmlspecialchars($message)) . '</p>';
                $html .= '<hr style="margin: 20px 0; border: none; border-top: 1px solid #e5e7eb;">';
                $html .= '<p style="color: #6b7280; font-size: 14px;">Sent via Grand Jyothi Construction Website</p>';
                $html .= '</div>';

                $result = sendEmail($to, $subject, $html);

                if ($result) {
                    echo '<div class="status status-success">';
                    echo '<strong>✅ Success!</strong>';
                    echo '<p>Email sent successfully to <strong>' . htmlspecialchars($to) . '</strong></p>';
                    echo '<p style="margin-top: 10px; font-size: 14px;">Check the recipient\'s inbox (and spam folder).</p>';
                    echo '</div>';
                } else {
                    echo '<div class="status status-error">';
                    echo '<strong>❌ Failed</strong>';
                    echo '<p>Email could not be sent. Please check:</p>';
                    echo '<ul style="margin-top: 10px; margin-left: 20px;">';
                    echo '<li>Email configuration in .env file</li>';
                    echo '<li>SMTP credentials are correct</li>';
                    echo '<li>For Gmail: Use App Password with 2FA</li>';
                    echo '<li>Check logs/email.log for details</li>';
                    echo '</ul>';
                    echo '</div>';
                }
            }
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="to">Recipient Email *</label>
                <input type="email" id="to" name="to" placeholder="recipient@example.com" required>
            </div>

            <div class="form-group">
                <label for="subject">Subject *</label>
                <input type="text" id="subject" name="subject" placeholder="Test Email" value="PHPMailer Test" required>
            </div>

            <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" rows="6" placeholder="Enter your test message..." required>This is a test email sent from PHPMailer.

If you receive this email, your email configuration is working correctly!</textarea>
            </div>

            <button type="submit">📧 Send Test Email</button>
        </form>

        <div class="status status-info" style="margin-top: 30px;">
            <strong>ℹ️ Setup Instructions</strong>
            <ol style="margin-top: 10px; margin-left: 20px;">
                <li>Install PHPMailer: Run <code>composer install</code> or <code>install-phpmailer.bat</code></li>
                <li>Configure <code>.env</code> file with your email settings</li>
                <li>For Gmail: Generate App Password at <a href="https://myaccount.google.com/apppasswords" target="_blank">Google Account</a></li>
                <li>Enable 2-Factor Authentication first</li>
                <li>Use App Password (not regular password) in .env</li>
            </ol>
            <p style="margin-top: 10px;">See <code>PHPMAILER_SETUP.md</code> for detailed instructions.</p>
        </div>
    </div>
</body>
</html>
