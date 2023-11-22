<?php

// Lấy danh sách số đủ yêu cầu từ truy vấn ban đầu (17-day warning)
$query_888_fixed_17_day_part1 = "SELECT DISTINCT ContractDetails.ContractCode, ContractDetails.Number, ContractDetails.DateStarted, ContractDetails.DateEnded, ContractDetails.StatusISDN, ContractDetails.CustomerCode
FROM `ContractDetails` WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ContractDetails.DateEnded) >= 17 * 24 * 60 * 60 AND ContractDetails.StatusISDN='2'";

// Lấy thông tin chi tiết và danh sách hợp đồng từ các số đã lấy được
$query_888_fixed_17_day_part2 = "SELECT DISTINCT Customers.Name AS CustomerName, Customers.ContactPhone AS CustomerPhone, Customers.Email AS CustomerEmail, 
ContractManagement.CustomerCode, ContractDetails.ContractCode, ContractDetails.Number AS Number,
ContractDetails.DateStarted, ContractDetails.DateEnded, ContractDetails.StatusISDN, ContractManagement.SalerCode,
Salers.Name AS SalerName, Salers.PhoneNumber AS SalerPhone, Salers.Email AS SalerEmail
FROM ($query_888_fixed_17_day_part1) AS ContractDetails
LEFT JOIN Customers ON Customers.Code = ContractDetails.CustomerCode
LEFT JOIN ContractManagement ON ContractManagement.CustomerCode = Customers.Code
LEFT JOIN Salers ON Salers.Code = Customers.SalerCode";


// Lấy danh sách số đủ yêu cầu từ truy vấn ban đầu (termination on the 19th day)
$query_888_fixed_19_day_part1 = "SELECT DISTINCT ContractDetails.ContractCode, ContractDetails.Number, ContractDetails.DateStarted, ContractDetails.DateEnded, ContractDetails.StatusISDN, ContractDetails.CustomerCode
FROM `ContractDetails` WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ContractDetails.DateEnded) >= 19 * 24 * 60 * 60 AND ContractDetails.StatusISDN='3'";

// Lấy thông tin chi tiết và danh sách hợp đồng từ các số đã lấy được
$query_888_fixed_19_day_part2 = "SELECT DISTINCT Customers.Name AS CustomerName, Customers.ContactPhone AS CustomerPhone, Customers.Email AS CustomerEmail, 
ContractManagement.CustomerCode, ContractDetails.ContractCode, ContractDetails.Number AS Number,
ContractDetails.DateStarted, ContractDetails.DateEnded, ContractDetails.StatusISDN, ContractManagement.SalerCode,
Salers.Name AS SalerName, Salers.PhoneNumber AS SalerPhone, Salers.Email AS SalerEmail
FROM ($query_888_fixed_19_day_part1) AS ContractDetails
LEFT JOIN Customers ON Customers.Code = ContractDetails.CustomerCode
LEFT JOIN ContractManagement ON ContractManagement.CustomerCode = Customers.Code
LEFT JOIN Salers ON Salers.Code = Customers.SalerCode";
