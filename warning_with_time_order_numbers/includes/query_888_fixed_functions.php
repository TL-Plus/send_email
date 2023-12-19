<?php

// SQL query first (for 5-day warning)
$query_888_fixed_5_day = "SELECT `customer_name`, `customer_code`, 
`customer_address`, `customer_email`, `customer_phone`, `user_name`, 
`user_code`, `order_number`, `order_time`, `status`
FROM `order_numbers`
WHERE DATEDIFF(NOW(), order_time) >= 5
AND status = 'holding'
AND status_email = 0
AND order_number LIKE '2%'
AND order_number LIKE '%888%'";

// SQL query second (for termination on the 7th day)
$query_888_fixed_7_day = "SELECT `customer_name`, `customer_code`, 
`customer_address`, `customer_email`, `customer_phone`, `user_name`, 
`user_code`, `order_number`, `order_time`, `status`
FROM `order_numbers`
WHERE DATEDIFF(NOW(), order_time) >= 7
AND status = 'holding'
AND status_email = 0
AND order_number LIKE '2%'
AND order_number LIKE '%888%'";