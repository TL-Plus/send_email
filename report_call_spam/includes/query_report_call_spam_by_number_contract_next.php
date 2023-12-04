<?php

// today
$query_report_call_spam_by_number_contract_next = "SELECT 
Day, CustomerName, ContractCode, Caller, Callee, SL 
FROM `ReportCallSpamByNumberContractNext`
WHERE SL > 10
AND Day = DATE_FORMAT(CURDATE(), '%d')
AND CustomerName != '' 
AND Callee NOT LIKE '842%' 
ORDER BY SL DESC";
