<?php

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
