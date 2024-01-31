<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'send_email/includes/send_telegram_message.php';


$query_report_warning_cdr_log = "SELECT *
FROM `CDRLog`
WHERE DATE(`TimeBegin`) = CURDATE()
    AND TIMESTAMPDIFF(HOUR, `TimeBegin`, NOW()) >= 1
    AND (
        (`Conditon` = 1)
        OR
        (`Conditon` = 0 AND `Count` > 0)
    )
ORDER BY `CDRLog`.`TimeBegin` DESC";

// Define Excel header
$header = [
    'ID', 'Server', 'TimeUpdate', 'TimeBegin', 'TimeEnd', 'Count', 'Condition'
];

// Define $dbName, $botToken, $chatId, $recipients
$dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN_CDR_LOG_DIGINEXT'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_CDR_LOG_DIGINEXT'];
$recipients = $_ENV['RECIPIENTS'];

date_default_timezone_set("Asia/Ho_Chi_Minh");

// today
$today = date('Y_m_d');
$currentTime = date('H:i d-m-Y');
$attachment = "Report_Warning_CDRLog_$today.xlsx";
$subject = "Report Warning CDRLog DIGINEXT ($currentTime)";

// Call the function to send a message via Telegram
sendTelegramMessageWithSql($query_report_warning_cdr_log, $dbName, $header, $attachment, $subject, $botToken, $chatId);

// if you want to use email instead of telegram. Call function sendEmailForDay()