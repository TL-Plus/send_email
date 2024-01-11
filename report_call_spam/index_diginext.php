<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'send_email/includes/send_telegram_message.php';


// query_report_call_spam_by_number_contract_next
$query_report_call_spam_by_number_contract_next = "SELECT 
Day, CustomerName, ContractCode, Caller, Callee, SL 
FROM `ReportCallSpamByNumberContractNext`
WHERE SL > 10
AND Day = DATE_FORMAT(CURDATE(), '%d')
AND CustomerName != '' 
AND Callee NOT LIKE '842%' 
ORDER BY SL DESC";

// Define Excel header
$header = [
    'Day', 'CustomerName', 'ContractCode', 'Caller', 'Callee', 'SL'
];

// Define $dbName, $botToken, $chatId, $recipients
$dbName = $_ENV['DB_DATABASE_REPORT'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
$chatId = $_ENV['TELEGRAM_CHAT_ID'];
$recipients = $_ENV['RECIPIENTS'];

// today
$today = date('Y_m_d');
$attachment = "Report_Call_Spam_By_Number_Contract_DIGINEXT_$today.xlsx";
$subject = "Report Call Spam By Number Contract DIGINEXT ($today)";

// Call the function to send a message via Telegram
sendTelegramMessageWithSql($query_report_call_spam_by_number_contract_next, $dbName, $header, $attachment, $subject, $botToken, $chatId);

if (!file_exists($attachment)) {
    $day = date('d-m-Y');
    $error_message = "Today $day No Report Call Spam By Number Contract DIGINEXT.";
    sendTelegramMessage($error_message, $botToken, $chatId);
}

// if you want to use email instead of telegram
// Call function to send email notification
// sendEmailForDays($query_report_call_spam_by_number_contract_next, $dbName, $header, $attachment, $subject, $recipients);