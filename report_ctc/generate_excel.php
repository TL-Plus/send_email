<?php
require_once '/var/www/html/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/export_excel.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';

$query_report_ctc = "SELECT 
dcn202402.customer_name AS CustomerName,
dcn202402.user_name AS SalerName,
SUM(dcn202402.TotalCost) AS TotalCost,
NULL AS TotalCurrentCall,
(
    SELECT COUNT(ext_number) 
    FROM Billing_Diginext.report_number_block rnb
    WHERE rnb.customer_name = dcn202402.customer_name
      AND DATE(rnb.time_update) = CURDATE()
) AS BlockViettel,
(
    SELECT COUNT(ext_number) 
    FROM Billing_Diginext.report_number_blockMobi rnbMobi
    WHERE rnbMobi.customer_name = dcn202402.customer_name
      AND DATE(rnbMobi.time_update) = CURDATE()
) AS BlockMobifone
FROM
dcn202402
WHERE            
DATE(dcn202402.TimeUpdate) = CURDATE()
GROUP BY
dcn202402.customer_name
ORDER BY 
TotalCost DESC 
LIMIT 30;";

$header = [
  'CustomerName', 'SalerName', 'TotalCost', 'TotalCurrentCall', 'BlockViettel', 'BlockMobifone'
];

$dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN_DIGINEXT'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_REPORT_CCU'];
$userName = $_ENV['USERNAME'];
$password = $_ENV['PASSWORD'];

date_default_timezone_set("Asia/Ho_Chi_Minh");
$today = date('Y_m_d');
$excelFilePath = "/var/www/html/Report_CTC_$today.xlsx";
$currentTime = date('d-m-Y H:i');
$textMessage = "Dữ liệu Báo Cáo Cuộc Gọi Hệ Thống VOS DIGINEXT ngày: $currentTime đã được cập nhật xong!" . PHP_EOL
  . "Kính mời đội ngũ vận hành vào website: http://103.112.209.152/send_email/report_ctc/ để xem và cập nhật thêm dữ liệu!" . PHP_EOL
  . "Hãy đăng nhập với tài khoản sau đây:" . PHP_EOL
  . "Tài khoản: $userName" . PHP_EOL
  . "Mật khẩu: $password";

$exportSuccessful = exportToExcel($query_report_ctc, $dbName, $header, $excelFilePath);

if ($exportSuccessful) {
  sendTelegramMessage($textMessage, $botToken, $chatId);
} else {
  echo "Error exporting data to Excel.\n";
}