<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'includes/update_status_email.php';


// Define Constants
define('WARNING_THRESHOLD', 5);
define('TERMINATION_THRESHOLD', 7);

// Define Excel header
$header = [
    'CustomerName', 'CustomerCode', 'CustomerAddress', 'CustomerEmail', 'CustomerPhone',
    'SalerName', 'SalerCode', 'OrderNumber', 'OrderTime', 'Status'
];

// Define $dbName, $recipients
$dbName = $_ENV['DB_DATABASE_BILLING_DIGINEXT'];
$recipients = $_ENV['RECIPIENTS'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
$chatId = $_ENV['TELEGRAM_CHAT_ID'];

function processEmailsAndTelegrams($threshold, $dbName, $header, $fileNamePrefix, $title, $recipients, $botToken, $chatId, $orderNumberCondition)
{
    $query = "SELECT `customer_name`, `customer_code`, 
            `customer_address`, `customer_email`, `customer_phone`, `user_name`, 
            `user_code`, `order_number`, `order_time`, `status`
        FROM `order_numbers`
        WHERE DATEDIFF(NOW(), order_time) >= $threshold
        AND status = 'holding'
        AND status_email = 0
        $orderNumberCondition";

    // Call function to send email notification for warning
    sendEmailForDays($query, $dbName, $header, "{$fileNamePrefix}{$threshold}_days.xlsx", "$title ($threshold-Days)", $recipients);

    // Call function to send telegram message notification for warning
    sendTelegramMessageWithSql($query, $dbName, $header, "{$fileNamePrefix}{$threshold}_days.xlsx", "$title ($threshold-Days)", $botToken, $chatId);

    // Call function to update status email
    updateStatusEmail($query);
}

// Process DVGTGT emails
$orderNumberConditionDVGTGT = "AND (order_number LIKE '1900%' OR order_number LIKE '1800%')";
processEmailsAndTelegrams(WARNING_THRESHOLD, $dbName, $header, 'Report_warning_dvgtgt_', 'Report Warning DVGTGT', $recipients, $botToken, $chatId, $orderNumberConditionDVGTGT);

processEmailsAndTelegrams(TERMINATION_THRESHOLD, $dbName, $header, 'Report_termination_dvgtgt_', 'Report Termination DVGTGT', $recipients, $botToken, $chatId, $orderNumberConditionDVGTGT);

// Process 888 Fixed emails
$orderNumberCondition888Fixed = "AND order_number LIKE '2%' AND order_number LIKE '%888%'";
processEmailsAndTelegrams(WARNING_THRESHOLD, $dbName, $header, 'Report_warning_888_fixed_', 'Report Warning 888 Fixed', $recipients, $botToken, $chatId, $orderNumberCondition888Fixed);

processEmailsAndTelegrams(TERMINATION_THRESHOLD, $dbName, $header, 'Report_termination_888_fixed_', 'Report Termination 888 Fixed', $recipients, $botToken, $chatId, $orderNumberCondition888Fixed);