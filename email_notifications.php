<?php
require 'includes/export_excel.php';
require_once 'includes/config.php';

// Function to send email notification
function sendEmailForDays($query, $attachment, $subject, $recipients)
{
    try {
        exportToExcel($query, $attachment);
        sendEmailNotification("/root/{$attachment}", $subject, "Excel Files for {$subject}", $recipients);
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
