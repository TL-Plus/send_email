<?php
require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/send_email/includes/send_email_for_days.php';
require '/var/www/html/send_email/includes/send_telegram_message.php';


// query_report_call_spam_by_number_contract_next
$query_report_call_spam_by_number_contract_next = "SELECT 
Day, CustomerName, CustomerCode, ContractCode, SalerCode, Caller, Callee, SL 
FROM `ReportCallSpamByNumberContractNext`
WHERE SL > 10
AND Day = DATE_FORMAT(CURDATE(), '%d')
AND CustomerName != '' 
AND Callee NOT LIKE '842%' 
ORDER BY SL DESC";

// Define Excel header
$header = [
    'Day', 'Customer Name', 'Customer Code', 'Contract Code', 'Saler Code', 'Caller', 'Callee', 'SL'
];

// Define $dbName, $botToken, $chatId, $recipients
$dbName = $_ENV['DB_DATABASE_REPORT'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN_RETURN_OTP'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_RETURN_OTP'];

date_default_timezone_set("Asia/Ho_Chi_Minh");

// today
$today = date('Y_m_d');
$today_2 = date('d-m-Y');
$attachment = "/var/www/html/send_email/files_export/Diginext_Spamcall_Contract_$today.xlsx";
$subject = "[DIGINEXT] - BÁO CÁO KHÁCH HÀNG SPAMCALL ($today_2)";

// Call the function to send a message via Telegram
sendTelegramMessageWithSql($query_report_call_spam_by_number_contract_next, $dbName, $header, $attachment, $subject, $botToken, $chatId);

if (!file_exists($attachment)) {
    $day = date('d-m-Y');
    $error_message = "[DIGINEXT] - HÔM NAY $day KHÔNG CÓ BÁO CÁO KHÁCH HÀNG SPAMCALL.";
    sendTelegramMessage($error_message, $botToken, $chatId);
}

// if you want to use email instead of telegram. Call function sendEmailForDay()