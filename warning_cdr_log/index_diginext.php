<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'send_email/includes/send_telegram_message.php';


// query_report_call_spam_by_number_contract_next
$query_report_warning_cdr_log = "SELECT * FROM `CDRLog`
WHERE DATE(`TimeBegin`) = CURDATE() 
AND `Count` > 0 
AND `Conditon` = 1
AND TIMESTAMPDIFF(HOUR, `TimeBegin`, NOW()) >= 1
ORDER BY `CDRLog`.`TimeBegin` DESC";

// Define Excel header
$header = [
    'ID', 'Server', 'TimeUpdate', 'TimeBegin', 'TimeEnd', 'Count', 'Condition'
];

// Define $dbName, $botToken, $chatId, $recipients
$dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN_DIGINEXT'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_DIGINEXT'];
$recipients = $_ENV['RECIPIENTS'];

// today
$today = date('Y_m_d');
$attachment = "Report_Warning_CDRLog_DIGINEXT_$today.xlsx";
$subject = "Report Warning CDRLog DIGINEXT ($today)";

// Call the function to send a message via Telegram
sendTelegramMessageWithSql($query_report_warning_cdr_log, $dbName, $header, $attachment, $subject, $botToken, $chatId);

// if you want to use email instead of telegram
// Call function to send email notification
// sendEmailForDays($query_report_warning_cdr_log, $dbName, $header, $attachment, $subject, $recipients);