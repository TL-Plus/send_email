<?php
require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/send_email/includes/send_telegram_message.php';


// // query_report_active_number
// $query_report_active_number = "SELECT DISTINCT time_update, ext_number, contract_code, customer_name, user_name 
// FROM report_number_active
// WHERE time_update >= CURDATE() - INTERVAL 1 WEEK 
// AND time_update < CURDATE()
// ORDER BY time_update ASC;";

// // query_report_block_number
// $query_report_block_number = "SELECT DISTINCT time_update, ext_number, contract_code, customer_name, user_name 
// FROM report_number_block
// WHERE time_update >= CURDATE() - INTERVAL 1 WEEK 
// AND time_update < CURDATE()
// ORDER BY time_update ASC;";

// // query_report_active_number_next
// $query_report_active_number_next = "SELECT DISTINCT time_update, ext_number, contract_code, customer_name, user_name 
// FROM report_number_active_next
// WHERE time_update >= CURDATE() - INTERVAL 1 WEEK 
// AND time_update < CURDATE()
// ORDER BY time_update ASC;";

// // query_report_block_number_next
// $query_report_block_number_next = "SELECT DISTINCT time_update, ext_number, contract_code, customer_name, user_name 
// FROM report_number_block_next
// WHERE time_update >= CURDATE() - INTERVAL 1 WEEK 
// AND time_update < CURDATE()
// ORDER BY time_update ASC;";

// query_report_active_number
$query_report_active_number_viettel = "SELECT DISTINCT time_update, ext_number, contract_code, customer_name, user_name, service
FROM (
    SELECT time_update, ext_number, contract_code, customer_name, user_name, service 
    FROM report_number_active
    WHERE time_update >= CURDATE() - INTERVAL 1 WEEK 
    AND time_update < CURDATE()
) AS combined_data
ORDER BY time_update ASC;
";

// query_report_block_number
$query_report_block_number_viettel = "SELECT DISTINCT time_update, ext_number, contract_code, customer_name, user_name, service 
FROM (
    SELECT time_update, ext_number, contract_code, customer_name, user_name, service 
    FROM report_number_block
    WHERE time_update >= CURDATE() - INTERVAL 1 WEEK 
    AND time_update < CURDATE()
) AS combined_data
ORDER BY time_update ASC;
";

// query_report_active_number
$query_report_active_number_mobi = "SELECT DISTINCT time_update, ext_number, contract_code, customer_name, user_name, service
FROM (
    SELECT time_update, ext_number, contract_code, customer_name, user_name, service 
    FROM report_number_activeMobi
    WHERE time_update >= CURDATE() - INTERVAL 1 WEEK 
    AND time_update < CURDATE()
) AS combined_data
ORDER BY time_update ASC;
";

// query_report_block_number
$query_report_block_number_mobi = "SELECT DISTINCT time_update, ext_number, contract_code, customer_name, user_name, service 
FROM (
    SELECT time_update, ext_number, contract_code, customer_name, user_name, service 
    FROM report_number_blockMobi
    WHERE time_update >= CURDATE() - INTERVAL 1 WEEK 
    AND time_update < CURDATE()
) AS combined_data
ORDER BY time_update ASC;
";

// Define Excel header
$header = [
    'Time Update', 'Ext/Number', 'Contract Code', 'Customer Name', 'Saler Name', 'Service'
];

// Define $dbName, $botToken, $chatId
$dbName = $_ENV['DB_DATABASE_BILLING_MAIN'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN_RETURN_OTP'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_RETURN_OTP'];

// $botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
// $chatId = $_ENV['TELEGRAM_CHAT_ID'];

date_default_timezone_set("Asia/Ho_Chi_Minh");

// Get the period for the report (start and end dates)
$report_period_start = date('Y-m-d', strtotime('last Monday', strtotime('now')));
$report_period_end = date('Y-m-d', strtotime('previous Sunday', strtotime('now')));
$report_period = "$report_period_start/$report_period_end";

$attachment_active_viettel = "/var/www/html/send_email/files_export/Report_active_number_Viettel.xlsx";
$subject_active_viettel = "[DIGI] - BÁO CÁO SỐ MỞ VIETTEL ($report_period)";

$attachment_block_viettel = "/var/www/html/send_email/files_export/Report_block_number_Viettel.xlsx";
$subject_block_viettel = "[DIGI] - BÁO CÁO SỐ KHÓA VIETTEL ($report_period)";

$attachment_active_mobi = "/var/www/html/send_email/files_export/Report_active_number_Mobi.xlsx";
$subject_active_mobi = "[DIGI] - BÁO CÁO SỐ MỞ MOBI ($report_period)";

$attachment_block_mobi = "/var/www/html/send_email/files_export/Report_block_number_Mobi.xlsx";
$subject_block_mobi = "[DIGI] - BÁO CÁO SỐ KHÓA MOBI ($report_period)";

// Call the function to send a message via Telegram
sendTelegramMessageWithSqlMain($query_report_active_number_viettel, $dbName, $header, $attachment_active_viettel, $subject_active_viettel, $botToken, $chatId);
sendTelegramMessageWithSqlMain($query_report_block_number_viettel, $dbName, $header, $attachment_block_viettel, $subject_block_viettel, $botToken, $chatId);
sendTelegramMessageWithSqlMain($query_report_active_number_mobi, $dbName, $header, $attachment_active_mobi, $subject_active_mobi, $botToken, $chatId);
sendTelegramMessageWithSqlMain($query_report_block_number_mobi, $dbName, $header, $attachment_block_mobi, $subject_block_mobi, $botToken, $chatId);
