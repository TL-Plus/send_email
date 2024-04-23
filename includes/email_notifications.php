<?php
require_once '/var/www/html/send_email/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function sendEmailNotification($attachmentPath, $subject, $body, $recipients, $cc_recipients)
{
    $mail = new PHPMailer();

    try {
        // Server settings
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $sender_email = $_ENV['SENDER_EMAIL'];

        $mail->setFrom($sender_email);

        $mail->From = $sender_email;
        $mail->FromName = 'CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT';

        // Add each recipient to the email
        $recipients = explode(',', $recipients);
        foreach ($recipients as $recipient) {
            $mail->addAddress(trim($recipient));
        }

        $cc_recipients = explode(',', $cc_recipients);
        foreach ($cc_recipients as $cc_recipient) {
            $mail->AddCC(trim($cc_recipient));
        }

        $mail->AddReplyTo($sender_email, "BILLING DIGINEXT");
        $mail->WordWrap = 50;

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;
        $mail->addAttachment($attachmentPath);

        if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
        }

        echo "The email message was sent.\n";
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

function sendEmailNotificationHolidaySchedule($subject, $body, $recipients, $imagePath)
{
    $mail = new PHPMailer();

    try {
        // Server settings
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Port = $_ENV['SMTP_PORT'];
        $mail->Username = $_ENV['SMTP_USERNAME'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $sender_email = $_ENV['SENDER_EMAIL'];

        $mail->setFrom($sender_email);

        $mail->From = $sender_email;
        $mail->FromName = 'CÔNG TY CỔ PHẦN TẬP ĐOÀN DIGINEXT';

        // Add each recipient to the email
        $recipients = explode(',', $recipients);
        foreach ($recipients as $recipient) {
            $mail->addAddress(trim($recipient));
        }

        $mail->AddReplyTo($sender_email, "BILLING DIGINEXT");
        $mail->WordWrap = 50;

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = $body;

        $mail->addEmbeddedImage($imagePath, 'holiday-schedule', 'holiday-schedule.jpg');

        if (!$mail->send()) {
            throw new Exception('Mailer Error: ' . $mail->ErrorInfo);
        }

        echo "The email message was sent.\n";
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
