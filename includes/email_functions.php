<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once 'config.php';

function sendEmailNotification($attachmentPath, $subject, $body, $recipients)
{
    $mail = new PHPMailer();

    try {
        // Server settings
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = SMTP_HOST;
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Port = SMTP_PORT;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;

        $mail->setFrom(SENDER_EMAIL);

        // Add each recipient to the email
        $recipients = explode(',', RECIPIENTS);
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

        echo 'The email message was sent.';
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}