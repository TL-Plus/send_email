<?php
// SQL query first (for 17-day warning)
$query_888_fixed_17_day = "SELECT DISTINCT Customers.Name AS CustomerName, Customers.ContactPhone AS CustomerPhone, Customers.Email AS CustomerEmail, 
        ContractManagement.CustomerCode, ContractDetails.ContractCode, ContractDetails.Number AS Number, 
        ContractDetails.DateStarted, ContractDetails.DateEnded, ContractDetails.StatusISDN, ContractManagement.SalerCode,
        Salers.Name AS SalerName, Salers.PhoneNumber AS SalerPhone, Salers.Email AS SalerEmail
        FROM `ContractDetails`
        LEFT JOIN Customers ON Customers.Code = ContractDetails.CustomerCode
        LEFT JOIN ContractManagement ON ContractManagement.CustomerCode = Customers.Code
        LEFT JOIN Salers ON Salers.Code = Customers.SalerCode
        WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ContractDetails.DateEnded) >= 17 * 24 * 60 * 60 AND StatusISDN='2'";

// SQL query second (for termination on the 19th day)
$query_888_fixed_19_day = "SELECT DISTINCT Customers.Name AS CustomerName, Customers.ContactPhone AS CustomerPhone, Customers.Email AS CustomerEmail, 
        ContractManagement.CustomerCode, ContractDetails.ContractCode, ContractDetails.Number AS Number, 
        ContractDetails.DateStarted, ContractDetails.DateEnded, ContractDetails.StatusISDN, ContractManagement.SalerCode,
        Salers.Name AS SalerName, Salers.PhoneNumber AS SalerPhone, Salers.Email AS SalerEmail
        FROM `ContractDetails`
        LEFT JOIN Customers ON Customers.Code = ContractDetails.CustomerCode
        LEFT JOIN ContractManagement ON ContractManagement.CustomerCode = Customers.Code
        LEFT JOIN Salers ON Salers.Code = Customers.SalerCode
        WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(ContractDetails.DateEnded) >= 19 * 24 * 60 * 60 AND StatusISDN='3'";
