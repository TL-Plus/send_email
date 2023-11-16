<?php
// SQL query first (for 17-day warning)
$query1 = "SELECT Customers.Name AS CustomerName, Customers.ContactPhone AS CustomerPhone, Customers.Email AS CustomerEmail, 
        ContractManagementDVGTGT.CustomerCode, ContractManagementDVGTGT.ContractCode, ContractDetailsDVGTGT.Number AS Number, 
        ContractDetailsDVGTGT.DateStarted, ContractDetailsDVGTGT.DateEnded, ContractDetailsDVGTGT.StatusISDN, ContractManagementDVGTGT.SalerCode,
        Salers.Name AS SalerName, Salers.PhoneNumber AS SalerPhone, Salers.Email AS SalerEmail
        FROM `ContractDetailsDVGTGT`
        LEFT JOIN Customers ON Customers.Code = ContractDetailsDVGTGT.CustomerCode
        LEFT JOIN ContractManagementDVGTGT ON ContractManagementDVGTGT.CustomerCode = Customers.Code
        LEFT JOIN Salers ON Salers.Code = Customers.SalerCode
        WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(DateEnded) >= 17 * 24 * 60 * 60 AND StatusISDN='2'";

// SQL query second (for termination on the 19th day)
$query2 = "SELECT Customers.Name AS CustomerName, Customers.ContactPhone AS CustomerPhone, Customers.Email AS CustomerEmail, 
        ContractManagementDVGTGT.CustomerCode, ContractManagementDVGTGT.ContractCode, ContractDetailsDVGTGT.Number AS Number, 
        ContractDetailsDVGTGT.DateStarted, ContractDetailsDVGTGT.DateEnded, ContractDetailsDVGTGT.StatusISDN, ContractManagementDVGTGT.SalerCode,
        Salers.Name AS SalerName, Salers.PhoneNumber AS SalerPhone, Salers.Email AS SalerEmail
        FROM `ContractDetailsDVGTGT`
        LEFT JOIN Customers ON Customers.Code = ContractDetailsDVGTGT.CustomerCode
        LEFT JOIN ContractManagementDVGTGT ON ContractManagementDVGTGT.CustomerCode = Customers.Code
        LEFT JOIN Salers ON Salers.Code = Customers.SalerCode
        WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(DateEnded) >= 19 * 24 * 60 * 60 AND StatusISDN='3'";
