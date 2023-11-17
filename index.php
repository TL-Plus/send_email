<?php
require 'includes/config.php';
require 'includes/database_connection.php';
require 'includes/email_functions.php';
require 'includes/query_functions.php';
require 'email_notifications.php';

$recipients = RECIPIENTS;

// Call function to send email notification for 17-day warning
sendEmailForDays($query1, 'report_warning_17_days.xlsx', '17-Day Warning - Excel File', $recipients);

// Call function to send email notification for termination on the 19th day
sendEmailForDays($query2, 'report_termination_19_days.xlsx', 'Termination on 19th Day - Excel File', $recipients);
