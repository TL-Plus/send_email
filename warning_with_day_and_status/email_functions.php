<?php
require 'includes/export_excel.php';
require_once 'includes/query_execute.php';
require_once 'send_email/includes/config.php';

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

function sendEmailForDaysFixed($query1, $query2, $attachment, $subject, $recipients)
{
    try {
        $numbers = array();
        $result = executeQuery($query1);

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
            $detailsQuery = str_replace('{numbers}', implode(',', $numbers), $query2);

            exportToExcel($detailsQuery, $attachment);
            sendEmailNotification("/root/{$attachment}", $subject, "Excel Files for {$subject}", $recipients);
        } else {
            echo 'No valid numbers found.';
        }
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
