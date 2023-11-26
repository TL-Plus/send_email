<?php
require 'send_email/includes/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/email_notifications.php';
require 'includes/query_report_call_spam_diginext.php';
require 'email_functions.php';

$today = date('Y_m_d');

// Call function to send email notification warning payment
sendEmailForDays($query_report_call_spam_diginext, "Report_call_spam_DIGINEXT_'$today'.xlsx", "Report Call Spam DIGINEXT ($today)", RECIPIENTS);
