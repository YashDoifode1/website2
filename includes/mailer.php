<?php
/**
 * Email Mailer System using PHPMailer
 * 
 * Handles sending emails using PHPMailer library
 */

declare(strict_types=1);

require_once __DIR__ . '/env.php';

// Load PHPMailer
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
    define('PHPMAILER_AVAILABLE', true);
} else {
    define('PHPMAILER_AVAILABLE', false);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Email configuration class
 */
class MailConfig
{
    public static function getConfig(): array
    {
        return [
            'driver' => env('MAIL_DRIVER', 'smtp'), // 'mail' or 'smtp'
            'host' => env('MAIL_HOST', 'smtp.gmail.com'),
            'port' => (int)env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME', ''),
            'password' => env('MAIL_PASSWORD', ''),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'), // 'tls' or 'ssl'
            'from_address' => env('MAIL_FROM_ADDRESS', 'info@grandjyothi.com'),
            'from_name' => env('MAIL_FROM_NAME', 'Grand Jyothi Construction'),
            'debug' => env('MAIL_DEBUG', false),
        ];
    }
}

/**
 * Create and configure PHPMailer instance
 * 
 * @return PHPMailer|null Configured PHPMailer instance or null if not available
 */
function createMailer(): ?PHPMailer
{
    if (!PHPMAILER_AVAILABLE) {
        error_log('PHPMailer not available. Run: composer install');
        return null;
    }
    
    $config = MailConfig::getConfig();
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        if ($config['debug']) {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        }
        
        if ($config['driver'] === 'smtp') {
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = $config['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['port'];
        } else {
            $mail->isMail();
        }
        
        // Recipients
        $mail->setFrom($config['from_address'], $config['from_name']);
        $mail->addReplyTo($config['from_address'], $config['from_name']);
        
        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        return $mail;
        
    } catch (Exception $e) {
        error_log('PHPMailer configuration error: ' . $e->getMessage());
        return null;
    }
}

/**
 * Send email using PHPMailer
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $message Email body (HTML)
 * @param string $altBody Plain text alternative body
 * @return bool Success status
 */
function sendEmail(string $to, string $subject, string $message, string $altBody = ''): bool
{
    // Validate email
    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email address: $to");
        return false;
    }
    
    // Fallback to simple mail if PHPMailer not available
    if (!PHPMAILER_AVAILABLE) {
        return sendMailFallback($to, $subject, $message);
    }
    
    $mail = createMailer();
    if (!$mail) {
        return sendMailFallback($to, $subject, $message);
    }
    
    try {
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = $altBody ?: strip_tags($message);
        
        $result = $mail->send();
        
        if ($result) {
            logEmailActivity($to, $subject, true);
        } else {
            logEmailActivity($to, $subject, false, 'Send failed');
        }
        
        return $result;
        
    } catch (Exception $e) {
        $error = $mail->ErrorInfo;
        error_log("PHPMailer Error: $error");
        logEmailActivity($to, $subject, false, $error);
        return false;
    }
}

/**
 * Fallback email function using PHP mail()
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $message Email body (HTML)
 * @return bool Success status
 */
function sendMailFallback(string $to, string $subject, string $message): bool
{
    $config = MailConfig::getConfig();
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . $config['from_name'] . ' <' . $config['from_address'] . '>',
        'Reply-To: ' . $config['from_address'],
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $headerString = implode("\r\n", $headers);
    $result = mail($to, $subject, $message, $headerString);
    
    logEmailActivity($to, $subject, $result, $result ? '' : 'PHP mail() failed');
    
    return $result;
}

/**
 * Send bulk emails
 * 
 * @param array $recipients Array of email addresses
 * @param string $subject Email subject
 * @param string $message Email body (HTML)
 * @return array ['success' => count, 'failed' => count, 'errors' => array]
 */
function sendBulkEmails(array $recipients, string $subject, string $message): array
{
    $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => []
    ];
    
    foreach ($recipients as $email) {
        if (sendEmail($email, $subject, $message)) {
            $results['success']++;
        } else {
            $results['failed']++;
            $results['errors'][] = $email;
        }
        
        // Small delay to avoid overwhelming server
        usleep(100000); // 0.1 second
    }
    
    return $results;
}

/**
 * Get email template
 * 
 * @param string $template Template name
 * @param array $data Template variables
 * @return string HTML content
 */
function getEmailTemplate(string $template, array $data = []): string
{
    $templates = [
        'blog_notification' => '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;">
                <div style="background-color: #004AAD; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
                    <h1 style="margin: 0; font-size: 28px;">{{site_name}}</h1>
                    <p style="margin: 10px 0 0 0; font-size: 16px;">New Blog Post</p>
                </div>
                <div style="background-color: white; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2 style="color: #004AAD; margin-top: 0;">{{title}}</h2>
                    <p style="color: #64748b; line-height: 1.6;">{{excerpt}}</p>
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{{link}}" style="background-color: #F7931E; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">Read Full Article</a>
                    </div>
                    <p style="color: #94a3b8; font-size: 14px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                        You received this email because you subscribed to updates from {{site_name}}.
                    </p>
                </div>
            </div>
        ',
        
        'construction_update' => '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;">
                <div style="background-color: #004AAD; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
                    <h1 style="margin: 0; font-size: 28px;">{{site_name}}</h1>
                    <p style="margin: 10px 0 0 0; font-size: 16px;">Construction Update</p>
                </div>
                <div style="background-color: white; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2 style="color: #004AAD; margin-top: 0;">{{title}}</h2>
                    <p style="color: #64748b; line-height: 1.6;">{{message}}</p>
                    {{#if image}}
                    <div style="margin: 20px 0;">
                        <img src="{{image}}" alt="{{title}}" style="max-width: 100%; border-radius: 8px;">
                    </div>
                    {{/if}}
                    <div style="background-color: #f1f5f9; padding: 15px; border-radius: 5px; margin: 20px 0;">
                        <p style="margin: 0; color: #475569;"><strong>Project:</strong> {{project_name}}</p>
                        <p style="margin: 10px 0 0 0; color: #475569;"><strong>Status:</strong> {{status}}</p>
                    </div>
                    <p style="color: #94a3b8; font-size: 14px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                        For more information, contact us at {{contact_email}} or {{contact_phone}}.
                    </p>
                </div>
            </div>
        ',
        
        'custom_notification' => '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f9fafb;">
                <div style="background-color: #004AAD; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
                    <h1 style="margin: 0; font-size: 28px;">{{site_name}}</h1>
                    <p style="margin: 10px 0 0 0; font-size: 16px;">{{subtitle}}</p>
                </div>
                <div style="background-color: white; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2 style="color: #004AAD; margin-top: 0;">{{title}}</h2>
                    <div style="color: #64748b; line-height: 1.6;">{{content}}</div>
                    {{#if button_text}}
                    <div style="text-align: center; margin: 30px 0;">
                        <a href="{{button_link}}" style="background-color: #F7931E; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;">{{button_text}}</a>
                    </div>
                    {{/if}}
                    <p style="color: #94a3b8; font-size: 14px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                        {{footer_text}}
                    </p>
                </div>
            </div>
        '
    ];
    
    if (!isset($templates[$template])) {
        return '';
    }
    
    $html = $templates[$template];
    
    // Simple template replacement
    foreach ($data as $key => $value) {
        $html = str_replace('{{' . $key . '}}', $value, $html);
    }
    
    // Remove unused placeholders
    $html = preg_replace('/\{\{[^}]+\}\}/', '', $html);
    
    return $html;
}

/**
 * Log email activity
 * 
 * @param string $to Recipient
 * @param string $subject Subject
 * @param bool $success Success status
 * @param string $error Error message if any
 * @return void
 */
function logEmailActivity(string $to, string $subject, bool $success, string $error = ''): void
{
    $logFile = __DIR__ . '/../logs/email.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $status = $success ? 'SUCCESS' : 'FAILED';
    $errorMsg = $error ? " | Error: $error" : '';
    
    $logEntry = sprintf(
        "[%s] [%s] To: %s | Subject: %s%s\n",
        $timestamp,
        $status,
        $to,
        $subject,
        $errorMsg
    );
    
    error_log($logEntry, 3, $logFile);
}
