<?php

namespace app\services;

use app\services\interfaces\IEmailServ;
use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

/** Service for sending transactional emails through SMTP (e.g. Gmail). */
final readonly class EmailServ implements IEmailServ
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $fromAddress;
    private string $fromName;
    private string $encryption;

    public function __construct()
    {
        $this->host = $_ENV['MAIL_HOST'];
        $this->port = (int) $_ENV['MAIL_PORT'];
        $this->username = $_ENV['MAIL_USERNAME'];
        $this->password = $_ENV['MAIL_PASSWORD'];
        $this->fromAddress = $_ENV['MAIL_FROM_ADDRESS'];
        $this->fromName = $_ENV['MAIL_FROM_NAME'];
        $this->encryption = strtolower($_ENV['MAIL_ENCRYPTION']);
    }

    public function sendRegistrationConfirmation(string $toEmail, string $toName): bool
    {
        $subject = 'Your account has been created';
        $body = sprintf(
            '<p>Hi %s,</p><p>Welcome to %s. Your account was successfully created.</p><p>You can now log in and start exploring events.</p>',
            htmlspecialchars($toName, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($_ENV['SITE_NAME'] ?? 'Haarlem Festival', ENT_QUOTES, 'UTF-8')
        );

        return $this->sendEmail($toEmail, $toName, $subject, $body);
    }

    public function sendPasswordReset(string $toEmail, string $toName, string $resetUrl): bool
    {
        $safeUrl = htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8');
        $subject = 'Reset your password';
        $body = sprintf(
            '<p>Hi %s,</p><p>We received a request to reset your password.</p><p><a href="%s">Click here to reset your password</a></p><p>This link expires in 1 hour.</p><p>If you did not request this, you can ignore this email.</p>',
            htmlspecialchars($toName, ENT_QUOTES, 'UTF-8'),
            $safeUrl
        );

        return $this->sendEmail($toEmail, $toName, $subject, $body);
    }

    public function sendOrderConfirmation(string $toEmail, string $toName, string $subject, string $body): bool
    {
        return $this->sendEmail($toEmail, $toName, $subject, $body);
    }

    private function sendEmail(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        if (!class_exists(PHPMailer::class)) {
            error_log('PHPMailer package not installed. Run composer install/update.');
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->Port = $this->port;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->CharSet = 'UTF-8';

            if ($this->encryption === 'tls')
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            $mail->setFrom($this->fromAddress, $this->fromName);
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>', '</div>'], ["\n", "\n", "\n", "\n", "\n"], $htmlBody));

            $mail->send();
            return true;
        } catch (MailerException $e) {
            error_log('Email send failed: ' . $e->getMessage());
            return false;
        }
    }
}
