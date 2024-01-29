<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'send_email/includes/send_telegram_message.php';

$query_report_quantity_do_not_call_blacklist_for_last_weeks = "SELECT 
`cmd_code`, 
COUNT(DISTINCT `msisdn`) AS `total_msisdn`
FROM 
`BlackList`
WHERE 
`updated_at` >= CURDATE() - INTERVAL 1 WEEK AND
`updated_at` < CURDATE()  
GROUP BY `cmd_code`";

$header = [
    'cmd_code', 'total_msisdn'
];

// Define database name and Telegram chat ID
$dbName = $_ENV['DB_DATABASE_BLACKLIST'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
$chatId = $_ENV['TELEGRAM_CHAT_ID'];

date_default_timezone_set("Asia/Ho_Chi_Minh");

// Get the period for the report (start and end dates)
$report_period = date('Y_m_d', strtotime('last Monday', strtotime('now'))) . '_' . date('Y_m_d', strtotime('previous Sunday', strtotime('now')));
$attachment = "Report_quantity_DNC_Week_$report_period.xlsx";
$subject = "Báo cáo số lượng DNC trong tuần (Tuần $report_period)";

// Call the function to send a message via Telegram
sendTelegramMessageWithSql($query_report_quantity_do_not_call_blacklist_for_last_weeks, $dbName, $header, $attachment, $subject, $botToken, $chatId);

// if you want to use email instead of telegram. Call function sendEmailForDay()