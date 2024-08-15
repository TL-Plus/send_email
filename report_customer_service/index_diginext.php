<?php
require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/send_email/includes/send_email_for_days.php';
require '/var/www/html/send_email/includes/send_telegram_message.php';


date_default_timezone_set("Asia/Ho_Chi_Minh");

$now_day = date('Y-m-d H:i:s');
$year = date('Y', strtotime($now_day));
$month = date('m', strtotime($now_day));

$table_name = "dcn" . $year . $month;

$query_report_customer = " SELECT 
    DATE_SUB(CURDATE(), INTERVAL 1 DAY) AS Day,
    $table_name.customer_name AS CustomerName,
    $table_name.customer_code AS CustomerCode,
    $table_name.contract_code AS ContractCode,
    $table_name.user_name AS SalerName,
    FORMAT(SUM($table_name.TotalCost), 0) AS TotalCost,
    (
        SELECT COUNT(ext_number)
        FROM billing.report_number_block rnb
        WHERE rnb.customer_code = $table_name.customer_code
        AND DATE(rnb.time_update) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
    ) AS BlockViettel
FROM
    $table_name
WHERE            
    DATE($table_name.TimeUpdate) = DATE_SUB(CURDATE(), INTERVAL 1 DAY) 
    AND $table_name.company_code = 'DIGINEXT'
GROUP BY
    $table_name.customer_name, $table_name.customer_code
HAVING 
    SUM($table_name.TotalCost) > 5000000
ORDER BY 
    SUM($table_name.TotalCost) DESC";

$header = [
    'Day',
    'Customer Name',
    'Customer Code',
    'Contract Code',
    'Saler Name',
    'Total Cost',
    'Block Viettel'
];

$dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN_RETURN_OTP'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_RETURN_OTP'];

$yesterday = date('Y_m_d', strtotime('-1 day'));
$yesterday_2 = date('d-m-Y', strtotime('-1 day'));
$attachment = "/var/www/html/send_email/files_export/Diginext_Report_Customer_$yesterday.xlsx";
$subject = "[DIGINEXT] - BÁO CÁO THỐNG KÊ DỊCH VỤ CỦA KHÁCH HÀNG ($yesterday_2)";

sendTelegramMessageWithSqlMain($query_report_customer, $dbName, $header, $attachment, $subject, $botToken, $chatId);
