<?php
require 'send_email/includes/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'includes/query_warning_liabilities.php';


// Generate the SQL query for warning liabilities
$str_timeBegin = '2023-09-01 00:00:00';
$str_timeEnd = '2023-09-30 23:59:59';
$query_warning_liabilities = generateWarningLiabilitiesQuery($str_timeBegin, $str_timeEnd);

// Define Excel header
$header = [
    'Years', 'Month', 'CustomerName', 'ContractCode', 'Number', 'StatusISDN', 'DateStarted', 'DateEnd'
];

// Prepare email details
$month_liabilities = date('Y_m', strtotime('-2 months'));
$attachment = "Report_warning_liabilities_$month_liabilities.xlsx";
$subject = "Report Warning Liabilities ($month_liabilities)";

// Call function to send email notification for warning liabilities
sendEmailForDays($query_warning_liabilities, $header, $attachment, $subject, RECIPIENTS);
