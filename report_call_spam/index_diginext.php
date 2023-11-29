<?php
require 'send_email/includes/config.php';
require 'send_email/includes/database_connection.php';
require 'includes/query_report_call_spam_by_number_contract_next.php';
require 'send_email/includes/send_email_for_days.php';

// Define Excel header
$header = [
    'TimeAction', 'Day', 'CustomerName', 'ContractCode', 'Caller', 'Callee', 'SL'
];

// Prepare email details
$yesterday = date('Y_m_d', strtotime('-1 days'));
$attachment = "Report_call_spam_DIGINEXT_$yesterday.xlsx";
$subject = "Report Call Spam DIGINEXT ($yesterday)";

// Call function to send email notification warning payment
sendEmailForDays($query_report_call_spam_by_number_contract_next, $header, $attachment, $subject, RECIPIENTS);
