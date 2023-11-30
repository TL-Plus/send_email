<?php

// yesterday
$query_report_call_spam_by_number_contract_next_bk = "SELECT 
TimeAction, Day, CustomerName, ContractCode, Caller, Callee, SL 
FROM `ReportCallSpamByNumberContractNextBK`
WHERE SL > 10 
AND Day = DATE_FORMAT(CURDATE() - INTERVAL 1 DAY, '%d')
AND CustomerName != '' 
AND Callee NOT LIKE '842%' 
ORDER BY SL DESC";
