<?php

function generateWarningLiabilitiesQuery($str_timeBegin, $str_timeEnd)
{
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

    return $query_warning_liabilities;
}
