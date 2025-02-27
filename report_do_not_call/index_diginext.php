<?php
require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/send_email/includes/send_email_for_days.php';
require '/var/www/html/send_email/includes/send_telegram_message.php';


// query_report_call_spam_by_number_contract_next
$query_report_do_not_call_blacklist = "SELECT 
`msisdn`, 
CONCAT(DATE_FORMAT(`updated_at`, '%Y-%m-%d'), '|', `cmd_code`) AS `day_cmd_code`,
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
  DATE(updated_at) = CURDATE() - INTERVAL 1 DAY
ORDER BY 
  `BlackList`.`updated_at` ASC";

// Define Excel header
$header = [
  'msisdn', 'day_cmd_code', 'telco', 'shortcode', 'info', 'mo_time', 'cmd_code', 'error_code', 'error_desc', 'updated_at', 'created_at'
];

// Define $dbName, $botToken, $chatId, $recipients
$dbName = $_ENV['DB_DATABASE_BLACKLIST'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN_RETURN_OTP'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_RETURN_OTP'];

date_default_timezone_set("Asia/Ho_Chi_Minh");

// yesterday
$yesterday = date('Y_m_d', strtotime('-1 days'));
$attachment = "/var/www/html/send_email/files_export/Report_DNC_$yesterday.xlsx";
$subject = "[DIGINEXT] - Report DNC ($yesterday)";

// Call the function to send a message via Telegram
sendTelegramMessageWithSqlMain($query_report_do_not_call_blacklist, $dbName, $header, $attachment, $subject, $botToken, $chatId);

if (!file_exists($attachment)) {
  $day = date('d-m-Y', strtotime('-1 days'));
  $error_message = "[DIGINEXT] - Yesterday $day No Report DNC.";
  sendTelegramMessage($error_message, $botToken, $chatId);
}

// if you want to use email instead of telegram. Call function sendEmailForDay()