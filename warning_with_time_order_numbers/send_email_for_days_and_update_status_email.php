<?php

require_once 'send_email/config.php';
require_once 'send_email/includes/export_excel.php';
require_once 'send_email/includes/email_notifications.php';
require_once 'includes/update_status_email.php';


// Function to send email notification and update status_email
function sendEmailForDaysAndUpdateStatusEmail($sql, $dbName, $header, $attachment, $subject, $recipients)
{
    try {
        $exportSuccessful = exportToExcel($sql, $dbName, $header, $attachment);

        // Check if export was successful before sending email
        if ($exportSuccessful) {
            sendEmailNotification("/root/{$attachment}", $subject, "Excel Files for {$subject}", $recipients);
            updateStatusEmail($sql);
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}