<?php

require 'export_list_numbers.php';

$query_report_call_spam_diginext = "SELECT DISTINCT 
customer_name AS CustomerName, 
contract_code AS ContractCode, 
ext_number AS Caller
FROM `dcn202311` WHERE ext_number in $result_list_numbers 
GROUP BY ext_number
ORDER BY `CustomerName` DESC";
