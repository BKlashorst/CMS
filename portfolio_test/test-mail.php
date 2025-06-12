<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test email configuration
$to = "your-email@example.com"; // Vervang dit met je eigen email adres
$subject = "Test Email";
$message = "This is a test email from your CMS.";
$headers = "From: noreply@yourdomain.com\r\n";
$headers .= "Reply-To: noreply@yourdomain.com\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Try to send the email
if (mail($to, $subject, $message, $headers)) {
    echo "Test email sent successfully!";
} else {
    echo "Failed to send test email. Error: " . error_get_last()['message'];
}

// Display PHP mail configuration
echo "\n\nPHP Mail Configuration:";
echo "\nSMTP Server: " . ini_get('SMTP');
echo "\nSMTP Port: " . ini_get('smtp_port');
echo "\nSendmail Path: " . ini_get('sendmail_path');
?> 