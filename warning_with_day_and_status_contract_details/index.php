<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'includes/send_email_for_days_fixed.php';


// Define Excel header
$header = [
    'CustomerName', 'CustomerPhone', 'CustomerEmail', 'CustomerCode', 'ContractCode', 'Number',
    'DateStarted', 'DateEnded', 'StatusISDN', 'SalerCode', 'SalerName', 'SalerPhone', 'SalerEmail'
];

// Define $dbName, $recipients
$dbName = $_ENV['DB_DATABASE_DIGITEL'];
$recipients = $_ENV['RECIPIENTS'];


// SQL query first (for 17-day warning)
$query_dvgtgt_17_day = "SELECT DISTINCT 
Customers.Name AS CustomerName, Customers.ContactPhone AS CustomerPhone, Customers.Email AS CustomerEmail, 
ContractManagementDVGTGT.CustomerCode, ContractDetailsDVGTGT.ContractCode, ContractDetailsDVGTGT.Number AS Number, 
ContractDetailsDVGTGT.DateStarted, ContractDetailsDVGTGT.DateEnded, ContractDetailsDVGTGT.StatusISDN, 
ContractManagementDVGTGT.SalerCode, Salers.Name AS SalerName, Salers.PhoneNumber AS SalerPhone, Salers.Email AS SalerEmail
FROM `ContractDetailsDVGTGT`
LEFT JOIN Customers ON Customers.Code = ContractDetailsDVGTGT.CustomerCode
LEFT JOIN ContractManagementDVGTGT ON ContractManagementDVGTGT.CustomerCode = Customers.Code
LEFT JOIN Salers ON Salers.Code = Customers.SalerCode
WHERE DATEDIFF(NOW(), ContractDetailsDVGTGT.DateEnded) >= 17
AND ContractDetailsDVGTGT.StatusISDN='2'";

// SQL query second (for termination on the 19th day)
$query_dvgtgt_19_day = "SELECT DISTINCT 
Customers.Name AS CustomerName, Customers.ContactPhone AS CustomerPhone, Customers.Email AS CustomerEmail, 
ContractManagementDVGTGT.CustomerCode, ContractDetailsDVGTGT.ContractCode, ContractDetailsDVGTGT.Number AS Number, 
ContractDetailsDVGTGT.DateStarted, ContractDetailsDVGTGT.DateEnded, ContractDetailsDVGTGT.StatusISDN, 
ContractManagementDVGTGT.SalerCode, Salers.Name AS SalerName, Salers.PhoneNumber AS SalerPhone, Salers.Email AS SalerEmail
FROM `ContractDetailsDVGTGT`
LEFT JOIN Customers ON Customers.Code = ContractDetailsDVGTGT.CustomerCode
LEFT JOIN ContractManagementDVGTGT ON ContractManagementDVGTGT.CustomerCode = Customers.Code
LEFT JOIN Salers ON Salers.Code = Customers.SalerCode
WHERE DATEDIFF(NOW(), ContractDetailsDVGTGT.DateEnded) >= 19
AND ContractDetailsDVGTGT.StatusISDN='3'
AND ContractDetailsDVGTGT.ContractCode='00069/2023/1900/DIGITEL'";

// Call function to send email notification for 17-day warning
sendEmailForDay($query_dvgtgt_17_day, $dbName, $header, 'Report_contracts_warning_dvgtgt_17_days.xlsx', 'Report Contracts Warning DVGTGT (17 Days)',  $recipients);

// Call function to send email notification for termination on the 19th day
sendEmailForDay($query_dvgtgt_19_day, $dbName, $header, 'Report_on_contracts_termination_dvgtgt_19_days.xlsx', 'Report on Contracts Termination DVGTGT (19 Days)',  $recipients);

// Get a list of numbers that meet the requirements from the initial query (17-day warning)
$query_888_fixed_17_day_part1 = "SELECT DISTINCT 
ContractDetails.ContractCode, ContractDetails.Number, ContractDetails.DateStarted, 
ContractDetails.DateEnded, ContractDetails.StatusISDN, ContractDetails.CustomerCode
FROM `ContractDetails` 
WHERE DATEDIFF(NOW(), ContractDetails.DateEnded) >= 17
AND ContractDetails.StatusISDN='2'";

// Get detailed information and a list of contracts from the obtained numbers
$query_888_fixed_17_day_part2 = "SELECT DISTINCT 
Customers.Name AS CustomerName, Customers.ContactPhone AS CustomerPhone, Customers.Email AS CustomerEmail, 
ContractManagement.CustomerCode, ContractDetails.ContractCode, ContractDetails.Number AS Number,
ContractDetails.DateStarted, ContractDetails.DateEnded, ContractDetails.StatusISDN, ContractManagement.SalerCode,
Salers.Name AS SalerName, Salers.PhoneNumber AS SalerPhone, Salers.Email AS SalerEmail
FROM ($query_888_fixed_17_day_part1) AS ContractDetails
LEFT JOIN Customers ON Customers.Code = ContractDetails.CustomerCode
LEFT JOIN ContractManagement ON ContractManagement.CustomerCode = Customers.Code
LEFT JOIN Salers ON Salers.Code = Customers.SalerCode";

// Get a list of numbers that meet the requirements from the initial query (termination on the 19th day)
$query_888_fixed_19_day_part1 = "SELECT DISTINCT 
ContractDetails.ContractCode, ContractDetails.Number, ContractDetails.DateStarted, 
ContractDetails.DateEnded, ContractDetails.StatusISDN, ContractDetails.CustomerCode
FROM `ContractDetails` 
WHERE DATEDIFF(NOW(), ContractDetails.DateEnded) >= 19
AND ContractDetails.StatusISDN='3'
AND ContractDetails.ContractCode='00488/2023/DIGIVOICE'";

// Get detailed information and a list of contracts from the obtained numbers
$query_888_fixed_19_day_part2 = "SELECT DISTINCT 
Customers.Name AS CustomerName, Customers.ContactPhone AS CustomerPhone, Customers.Email AS CustomerEmail, 
ContractManagement.CustomerCode, ContractDetails.ContractCode, ContractDetails.Number AS Number,
ContractDetails.DateStarted, ContractDetails.DateEnded, ContractDetails.StatusISDN, ContractManagement.SalerCode,
Salers.Name AS SalerName, Salers.PhoneNumber AS SalerPhone, Salers.Email AS SalerEmail
FROM ($query_888_fixed_19_day_part1) AS ContractDetails
LEFT JOIN Customers ON Customers.Code = ContractDetails.CustomerCode
LEFT JOIN ContractManagement ON ContractManagement.CustomerCode = Customers.Code
LEFT JOIN Salers ON Salers.Code = Customers.SalerCode";

// Call function to send email notification for 17-day warning
sendEmailForDayFixed($query_888_fixed_17_day_part1, $query_888_fixed_17_day_part2, $dbName, $header, 'Report_contracts_warning_888_fixed_17_days.xlsx', 'Report Contracts Warning 888 Fixed (17-Days)',  $recipients);

// Call function to send email notification for 19-day warning
sendEmailForDayFixed($query_888_fixed_19_day_part1, $query_888_fixed_19_day_part2, $dbName, $header, 'Report_on_contracts_termination_888_fixed_19_days.xlsx', 'Report on Contracts Termination 888 Fixed (19 Days)',  $recipients);