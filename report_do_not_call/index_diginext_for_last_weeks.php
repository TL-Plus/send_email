<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'send_email/includes/send_telegram_message.php';


// Define the query to retrieve data for the previous week
$query_report_do_not_call_blacklist_for_last_weeks = "SELECT 
    `msisdn`, 
    `telco`, 
    `shortcode`, 
    `info`, 
    `mo_time`, 
    `cmd_code`, 
    `error_code`, 
    `error_desc`, 
    `updated_at`, 
    `created_at` 
FROM 
    `BlackList`
WHERE 
    `updated_at` >= CURDATE() - INTERVAL 1 WEEK AND
    `updated_at` < CURDATE() 
ORDER BY `BlackList`.`updated_at` ASC";

// Define Excel header
$header = [
    'msisdn', 'telco', 'shortcode', 'info', 'mo_time', 'cmd_code', 'error_code', 'error_desc', 'updated_at', 'created_at'
];

// Define database name and Telegram chat ID
$dbName = $_ENV['DB_DATABASE_BLACKLIST'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
$chatId = $_ENV['TELEGRAM_CHAT_ID'];

// Get the period for the report (start and end dates)
$report_period = date('Y_m_d', strtotime('last Monday', strtotime('now'))) . '_' . date('Y_m_d', strtotime('previous Sunday', strtotime('now')));
$attachment = "Report_DNC_Week_$report_period.xlsx";
$subject = "Report DNC DIGINEXT (Week $report_period)";

// Call the function to send a message via Telegram
sendTelegramMessageWithSql($query_report_do_not_call_blacklist_for_last_weeks, $dbName, $header, $attachment, $subject, $botToken, $chatId);

// If you want to use email instead of Telegram, uncomment the line below
// sendEmailForDays($query_report_do_not_call_blacklist, $dbName, $header, $attachment, $subject, RECIPIENTS);