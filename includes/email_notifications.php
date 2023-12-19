<?php
require 'vendor/autoload.php';
require_once 'send_email/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function sendEmailNotification($attachmentPath, $subject, $body, $recipients)
{
    $mail = new PHPMailer();

    try {
        // Server settings
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];

        $mail->setFrom($_ENV['SENDER_EMAIL']);

        // Add each recipient to the email
        $recipients = explode(',', $_ENV['RECIPIENTS']);
        foreach ($recipients as $recipient) {
            $mail->addAddress(trim($recipient));
        }

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->addAttachment($attachmentPath);

        if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
        }

        echo "The email message was sent.\n";
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}