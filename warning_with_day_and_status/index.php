<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'includes/query_dvgtgt_functions.php';
require 'includes/query_888_fixed_functions.php';
require 'send_email/includes/send_email_for_days.php';
require 'send_email_for_days_fixed.php';

// Define Excel header
$header = [
    'CustomerName', 'CustomerPhone', 'CustomerEmail', 'CustomerCode', 'ContractCode', 'Number',
    'DateStarted', 'DateEnded', 'StatusISDN', 'SalerCode', 'SalerName', 'SalerPhone', 'SalerEmail'
];

// Call function to send email notification for 17-day warning
sendEmailForDays($query_dvgtgt_17_day, $header, 'Report_warning_17_days_dvgtgt.xlsx', 'Report 17-Day Warning (DVGTGT)',  $_ENV['RECIPIENTS']);

// Call function to send email notification for termination on the 19th day
sendEmailForDays($query_dvgtgt_19_day, $header, 'Report_termination_19_days_dvgtgt.xlsx', 'Report Termination on 19th Day (DVGTGT)',  $_ENV['RECIPIENTS']);

// Call function to send email notification for 17-day warning
sendEmailForDaysFixed($query_888_fixed_17_day_part1, $query_888_fixed_17_day_part2, $header, 'Report_warning_17_days_888_fixed.xlsx', 'Report 17-Day Warning (888 Fixed)',  $_ENV['RECIPIENTS']);

// Call function to send email notification for 19-day warning
sendEmailForDaysFixed($query_888_fixed_19_day_part1, $query_888_fixed_19_day_part2, $header, 'Report_warning_19_days_888_fixed.xlsx', 'Report 19-Day Warning (888 Fixed)',  $_ENV['RECIPIENTS']);
