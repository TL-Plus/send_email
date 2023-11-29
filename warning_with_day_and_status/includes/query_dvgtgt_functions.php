<?php

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
WHERE DATEDIFF(NOW(), ContractDetails.DateEnded) >= 17
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
WHERE DATEDIFF(NOW(), ContractDetails.DateEnded) >= 19
AND ContractDetailsDVGTGT.StatusISDN='3'
AND ContractDetailsDVGTGT.ContractCode='00069/2023/1900/DIGITEL'";
