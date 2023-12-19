<?php
require_once 'send_email/config.php';
require_once 'send_email/includes/database_connection.php';
require_once 'includes/query_dvgtgt_functions.php';
require_once 'includes/query_888_fixed_functions.php';
require 'send_email_for_days_and_update_status_email.php';


// Define Excel header
$header = [
    'CustomerName', 'CustomerCode', 'CustomerAddress', 'CustomerEmail', 'CustomerPhone',
    'SalerName', 'SalerCode', 'OrderNumber', 'OrderTime', 'Status'
];

// Define $dbName, $recipients
$dbName = $_ENV['DB_DATABASE_BILLING_DIGINEXT'];
$recipients = $_ENV['RECIPIENTS'];

// // Call function to send email notification for 5-day warning (DVGTGT)
sendEmailForDaysAndUpdateStatusEmail($query_dvgtgt_5_day, $dbName, $header, 'Report_warning_5_days_dvgtgt.xlsx', 'Report 5-Day Warning (DVGTGT)', $recipients);

// Call function to send email notification for termination on the 7th day (DVGTGT)
sendEmailForDaysAndUpdateStatusEmail($query_dvgtgt_7_day, $dbName, $header, 'Report_termination_7_days_dvgtgt.xlsx', 'Report Termination on 7th Day (DVGTGT)', $recipients);

// Call function to send email notification for 5-day warning (888 Fixed)
sendEmailForDaysAndUpdateStatusEmail($query_888_fixed_5_day, $dbName, $header, 'Report_warning_5_days_888_fixed.xlsx', 'Report 5-Day Warning (888 Fixed)', $recipients);

// Call function to send email notification for termination on the 7th day (888 Fixed)
sendEmailForDaysAndUpdateStatusEmail($query_888_fixed_7_day, $dbName, $header, 'Report_termination_7_days_888_fixed.xlsx', 'Report Termination on 7th Day (888 Fixed)', $recipients);