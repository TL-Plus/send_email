<?php
require_once 'send_email/config.php';
require_once 'export_excel.php';
require_once 'email_notifications.php';


// Function to send email notification
function sendEmailForDays($sql, $dbName, $header, $attachment, $subject, $recipients)
{
    try {
        $exportSuccessful = exportToExcel($sql, $dbName, $header, $attachment);

        // Check if export was successful before sending email
        if ($exportSuccessful) {
            sendEmailNotification("/root/{$attachment}", $subject, "Excel Files for {$subject}", $recipients);
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}