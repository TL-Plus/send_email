<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'includes/query_warning_liabilities.php';



// Generate the SQL query for warning liabilities
$query_warning_liabilities = generateWarningLiabilitiesQuery($_ENV['TIME_BEGIN'], $_ENV['TIME_END']);

// Define Excel header
$header = [
    'Years', 'Month', 'CustomerName', 'ContractCode', 'Number', 'StatusISDN', 'DateStarted', 'DateEnd'
];

// Define $dbName, $recipients
$dbName = $_ENV['DB_DATABASE_DIGITEL'];
$recipients = $_ENV['RECIPIENTS'];

// Prepare email details
$month_liabilities = date('Y_m', strtotime('-2 months'));
$attachment = "Report_warning_liabilities_$month_liabilities.xlsx";
$subject = "Report Warning Liabilities ($month_liabilities)";

// Call function to send email notification for warning liabilities
sendEmailForDays($query_warning_liabilities, $dbName, $header, $attachment, $subject,  $recipients);