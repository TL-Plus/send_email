<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'send_email/includes/send_telegram_message.php';


// query_report_call_spam_by_number_contract_next
$query_report_do_not_call_blacklist = "SELECT `msisdn`, `telco`, 
`shortcode`, `info`, `mo_time`, `cmd_code`, 
`error_code`, `error_desc`, `updated_at`, `created_at` 
FROM `BlackList`
WHERE DAY(updated_at) = DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%d')
ORDER BY `BlackList`.`updated_at` ASC";

// Define Excel header
$header = [
    'msisdn', 'telco', 'shortcode', 'info', 'mo_time', 'cmd_code', 'error_code', 'error_desc', 'updated_at', 'created_at'
];

// Define $dbName, $chatId
$dbName = $_ENV['DB_DATABASE_BLACKLIST'];
$chatId = $_ENV['TELEGRAM_CHAT_ID'];

// yesterday
$yesterday = date('Y_m_d', strtotime('-1 days'));
$attachment = "Report_DNC_$yesterday.xlsx";
$subject = "Report DNC DIGINEXT ($yesterday)";

// Call the function to send a message via Telegram
sendTelegramMessage($query_report_do_not_call_blacklist, $dbName, $header, $attachment, $subject, $chatId);

// if you want to use email instead of telegram
// Call function to send email notification warning payment yesterday
// sendEmailForDays($query_report_call_spam_by_number_contract_next_bk, $dbName, $header, $attachment, $subject, RECIPIENTS);