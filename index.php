<?php
require 'includes/config.php';
require 'includes/database_connection.php';
require 'includes/email_notifications.php';
require 'includes/query_dvgtgt_functions.php';
require 'includes/query_888_fixed_functions.php';
require 'includes/query_execute.php';
require 'email_functions.php';


// Call function to send email notification for 17-day warning
sendEmailForDays($query_dvgtgt_17_day, 'report_warning_17_days_dvgtgt.xlsx', '17-Day Warning - Excel File (DVGTGT)', RECIPIENTS);

// Call function to send email notification for termination on the 19th day
sendEmailForDays($query_dvgtgt_19_day, 'report_termination_19_days_dvgtgt.xlsx', 'Termination on 19th Day - Excel File (DVGTGT)', RECIPIENTS);

// Call function to send email notification for 17-day warning
sendEmailForDaysFixed($query_888_fixed_17_day_part1, $query_888_fixed_17_day_part2, 'report_warning_17_days_888_fixed.xlsx', '17-Day Warning - Excel File (888 Fixed)', RECIPIENTS);

// Call function to send email notification for 19-day warning
sendEmailForDaysFixed($query_888_fixed_19_day_part1, $query_888_fixed_19_day_part2, 'report_warning_19_days_888_fixed.xlsx', '19-Day Warning - Excel File (888 Fixed)', RECIPIENTS);
