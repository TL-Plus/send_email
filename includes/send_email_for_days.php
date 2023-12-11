<?php
require_once 'send_email/config.php';
require 'export_excel.php';
require 'email_notifications.php';

// Function to send email notification
function sendEmailForDays($sql, $dbName, $header, $attachment, $subject, $recipients)
{
    try {
        exportToExcel($sql, $dbName, $header, $attachment);
        sendEmailNotification("/root/{$attachment}", $subject, "Excel Files for {$subject}", $recipients);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
