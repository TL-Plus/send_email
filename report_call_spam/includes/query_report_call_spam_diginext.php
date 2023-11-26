<?php

require 'export_list_numbers.php';

$query_report_call_spam_diginext = "SELECT DISTINCT 
customer_name AS CustomerName, 
contract_code AS ContracCode, 
ext_number AS Number 
FROM `dcn202311` WHERE ext_number in $result_list_numbers GROUP BY ext_number";
