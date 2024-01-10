<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'send_email/includes/send_telegram_message.php';


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
$botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
$chatId = $_ENV['TELEGRAM_CHAT_ID'];
$recipients = $_ENV['RECIPIENTS'];

// today
$today = date('Y_m_d');
$attachment = "Report_DNC_$today.xlsx";
$subject = "Report DNC DIGINEXT ($today)";

// Call the function to send a message via Telegram
if (file_exists($attachment)) {
  sendTelegramMessageWithSql($query_report_do_not_call_blacklist, $dbName, $header, $attachment, $subject, $botToken, $chatId);
} else {
  $day = date('d-m-Y');
  $error_message = "Yesterday $day No Report DNC DIGINEXT.";
  sendTelegramMessage($error_message, $botToken, $chatId);
}

// if you want to use email instead of telegram
// Call function to send email notification
// sendEmailForDays($query_report_do_not_call_blacklist, $dbName, $header, $attachment, $subject, $recipients);