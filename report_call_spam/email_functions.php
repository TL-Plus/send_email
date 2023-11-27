<?php
require 'includes/export_excel.php';
require 'includes/export_excel_diginext.php';
require_once 'send_email/includes/config.php';

// Function to send email notification
function sendEmailForDaysDiginext($query, $attachment, $subject, $recipients)
{
    try {
        exportToExcelDiginext($query, $attachment);
        sendEmailNotification("/root/{$attachment}", $subject, "Excel Files for {$subject}", $recipients);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
