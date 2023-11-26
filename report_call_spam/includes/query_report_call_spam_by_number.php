<?php

$query_report_call_spam_by_number = "SELECT DISTINCT Caller
FROM ReportCallSpamByNumber 
WHERE SL >= 5 
AND Day = '25' 
AND CustomerName != '' 
AND ContractCode LIKE '00488%' 
AND Callee NOT LIKE '842%' 
GROUP BY Callee ORDER BY SL DESC";
