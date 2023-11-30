<?php
require 'send_email/includes/config.php';
require 'send_email/includes/database_connection.php';
require 'includes/query_report_call_spam_by_number_contract_next.php';
require 'send_email/includes/send_email_for_days.php';
require 'send_email/includes/send_telegram_message.php';

// Define Excel header
$header = [
    'TimeAction', 'Day', 'CustomerName', 'ContractCode', 'Caller', 'Callee', 'SL'
];

// yesterday
$yesterday = date('Y_m_d', strtotime('-1 days'));
$attachment = "Report_call_spam_DIGINEXT_$yesterday.xlsx";
$subject = "Report Call Spam DIGINEXT ($yesterday)";

// if you want to use email instead of telegram
// Call function to send email notification warning payment yesterday
// sendEmailForDays($query_report_call_spam_by_number_contract_next_bk, $header, $attachment, $subject, RECIPIENTS);

// Call the function to send a message via Telegram
sendTelegramMessage($query_report_call_spam_by_number_contract_next_bk, $header, $attachment, $subject, TELEGRAM_CHAT_ID);
