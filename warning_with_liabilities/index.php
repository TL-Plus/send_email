<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';


// Define Constants TIme
$str_timeBegin = '2023-10-01 00:00:00';
$str_timeEnd = '2023-10-31 23:59:59';

// SQL query for warning liabilities
$query_warning_liabilities = "SELECT DISTINCT
        Liabilities.Years,
        Liabilities.Month,
        Customers.Name AS CustomerName,
        Liabilities.ContractCode,
        ContractDetails.Number,
        ContractDetails.StatusISDN,
        ContractDetails.DateStarted,
        ContractDetails.DateEnded
    FROM
        Liabilities
    LEFT JOIN Customers ON Customers.Code = Liabilities.CustomerCode
    LEFT JOIN ContractDetails ON ContractDetails.ContractCode = Liabilities.ContractCode
    WHERE
        Liabilities.Years = YEAR(NOW())
        AND Liabilities.Month = MONTH(DATE_SUB(NOW(), INTERVAL 2 MONTH))
        AND ContractDetails.ContractCode IN (
            SELECT Liabilities.ContractCode FROM Liabilities
            WHERE Liabilities.Years = YEAR(NOW())
            AND Liabilities.Month = MONTH(DATE_SUB(NOW(), INTERVAL 2 MONTH))
        )
        AND (
            (ContractDetails.DateStarted <= '$str_timeEnd' AND ContractDetails.StatusISDN IN ('1','2'))
            OR
            (
                ContractDetails.StatusISDN IN ('3','5')
                AND ContractDetails.DateEnded >= '$str_timeBegin'
                AND (
                    ContractDetails.DateStarted <= '$str_timeBegin'
                    OR
                    (
                        ContractDetails.DateStarted >= '$str_timeBegin'
                        AND ContractDetails.DateStarted <= '$str_timeEnd'
                    )
                )
            )
        )";

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
sendEmailForDay($query_warning_liabilities, $dbName, $header, $attachment, $subject,  $recipients);