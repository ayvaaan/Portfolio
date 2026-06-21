<?php
// Simple mail wrapper that uses PHPMailer if available, otherwise falls back to mail().
// Requires composer install of phpmailer/phpmailer for SMTP support.

function send_notification_email($to, $subject, $body, $from = null) {
    $smtpHost = getenv('SMTP_HOST') ?: '';
    $smtpPort = getenv('SMTP_PORT') ?: 587;
    $smtpUser = getenv('SMTP_USER') ?: '';
    $smtpPass = getenv('SMTP_PASS') ?: '';
    $smtpSecure = getenv('SMTP_SECURE') ?: 'tls'; // tls or ssl

    // If PHPMailer is installed, use it for SMTP
    if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
        try {
            require_once __DIR__ . '/../../vendor/autoload.php';
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $smtpHost ?: 'localhost';
            $mail->Port = $smtpPort;
            if ($smtpUser) {
                $mail->SMTPAuth = true;
                $mail->Username = $smtpUser;
                $mail->Password = $smtpPass;
            }
            if ($smtpSecure === 'ssl') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($smtpSecure === 'tls') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->setFrom($from ?: ($smtpUser ?: 'no-reply@localhost'));
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('PHPMailer error: ' . $e->getMessage());
            // fallback to mail()
        }
    }

    // Fallback to PHP mail()
    $headers = 'From: ' . ($from ?: 'no-reply@localhost') . "\r\n" . 'Reply-To: ' . ($from ?: 'no-reply@localhost') . "\r\n";
    return mail($to, $subject, $body, $headers);
}

?>
