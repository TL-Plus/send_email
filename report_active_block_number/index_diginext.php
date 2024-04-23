<?php
require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/send_email/includes/send_telegram_message.php';


// query_report_active_number
$query_report_active_number = "SELECT DISTINCT time_update, ext_number, contract_code, customer_name, user_name 
FROM report_number_active
WHERE time_update >= CURDATE() - INTERVAL 1 WEEK 
AND time_update < CURDATE()
ORDER BY time_update ASC;";

// query_report_block_number
$query_report_block_number = "SELECT DISTINCT time_update, ext_number, contract_code, customer_name, user_name 
FROM report_number_block
WHERE time_update >= CURDATE() - INTERVAL 1 WEEK 
AND time_update < CURDATE()
ORDER BY time_update ASC;";

// Define Excel header
$header = [
  'Time Update', 'Ext/Number', 'Contract Code', 'Customer Name', 'Saler Name'
];

// Define $dbName, $botToken, $chatId
$dbName = $_ENV['DB_DATABASE_BILLING_DIGINEXT'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN_RETURN_OTP'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_RETURN_OTP'];

date_default_timezone_set("Asia/Ho_Chi_Minh");

// Get the period for the report (start and end dates)
$report_period_start = date('Y-m-d', strtotime('last Monday', strtotime('now')));
$report_period_end = date('Y-m-d', strtotime('previous Sunday', strtotime('now')));
$report_period = "$report_period_start/$report_period_end";

$attachment_active = "/var/www/html/send_email/files_export/Report_active_number_Viettel.xlsx";
$subject_active = "[DIGINEXT] - BÁO CÁO SỐ MỞ Viettel ($report_period)";

$attachment_block = "/var/www/html/send_email/files_export/Report_block_number_Viettel.xlsx";
$subject_block = "[DIGINEXT] - BÁO CÁO SỐ KHÓA Viettel ($report_period)";

// Call the function to send a message via Telegram
sendTelegramMessageWithSql($query_report_active_number, $dbName, $header, $attachment_active, $subject_active, $botToken, $chatId);
sendTelegramMessageWithSql($query_report_block_number, $dbName, $header, $attachment_block, $subject_block, $botToken, $chatId);
