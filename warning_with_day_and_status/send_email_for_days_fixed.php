<?php
require_once 'send_email/includes/config.php';
require_once 'send_email/includes/export_excel.php';
require_once 'send_email/includes/email_notifications.php';

// Function to send email notification
function sendEmailForDaysFixed($sql1, $sql2, $header, $attachment, $subject, $recipients)
{
    try {
        $numbers = array();
        $result = connectAndQueryDatabase($sql1, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

        // Check if $result is an object before proceeding
        if (is_object($result)) {
            // Fetch each row and store the 'Number' in the array
            while ($row = $result->fetch_assoc()) {
                $numbers[] = $row['Number'];
            }
            // Free the result set
            $result->free();
        } else {
            // Handle the case where $result is not an object (e.g., it's an array)
            echo 'Error: Invalid result type.';
            return;
        }

        // Check if $numbers array is not empty before proceeding
        if (!empty($numbers)) {
            $detailsQuery = str_replace('{numbers}', implode(',', $numbers), $sql2);

            exportToExcel($detailsQuery, $header, $attachment);
            sendEmailNotification("/root/{$attachment}", $subject, "Excel Files for {$subject}", $recipients);
        } else {
            echo 'No valid numbers found.';
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}