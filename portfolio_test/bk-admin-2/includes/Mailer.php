<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class Mailer {
    private $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'your-email@gmail.com'; // Vervang dit met je Gmail adres
        $this->mailer->Password = 'your-app-password'; // Vervang dit met je Gmail app wachtwoord
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        $this->mailer->CharSet = 'UTF-8';
    }

    public function sendContactForm($to, $name, $email, $phone, $message) {
        try {
            // Recipients
            $this->mailer->setFrom('noreply@yourdomain.com', 'Contact Form');
            $this->mailer->addAddress($to);
            $this->mailer->addReplyTo($email, $name);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Nieuw contactformulier bericht';
            
            // Email body
            $body = "
                <h2>Nieuw bericht van het contactformulier</h2>
                <p><strong>Naam:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                " . (!empty($phone) ? "<p><strong>Telefoon:</strong> {$phone}</p>" : "") . "
                <p><strong>Bericht:</strong></p>
                <p>" . nl2br(htmlspecialchars($message)) . "</p>
            ";
            
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer Error: " . $e->getMessage());
            return false;
        }
    }
} 