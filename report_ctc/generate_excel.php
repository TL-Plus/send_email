<?php
require_once '/var/www/html/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/export_excel.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';

date_default_timezone_set("Asia/Ho_Chi_Minh");

$now_day = date('Y-m-d H:i:s');
$year = date('Y', strtotime($now_day));
$month = date('m', strtotime($now_day));

$table_name = "dcn" . $year . $month;

$query_report_ctc = "SELECT 
$table_name.customer_name AS CustomerName,
$table_name.user_name AS SalerName,
SUM($table_name.TotalCost) AS TotalCost,
NULL AS TotalCurrentCall,
(
    SELECT COUNT(ext_number) 
    FROM Billing_Diginext.report_number_block rnb
    WHERE rnb.customer_name = $table_name.customer_name
      AND DATE(rnb.time_update) = CURDATE()
) AS BlockViettel,
(
    SELECT COUNT(ext_number) 
    FROM Billing_Diginext.report_number_blockMobi rnbMobi
    WHERE rnbMobi.customer_name = $table_name.customer_name
      AND DATE(rnbMobi.time_update) = CURDATE()
) AS BlockMobifone
FROM
$table_name
WHERE            
DATE($table_name.TimeUpdate) = CURDATE()
GROUP BY
$table_name.customer_name
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

$today = date('Y_m_d');
$excelFilePath = "/var/www/html/report_ctc/files/Report_CTC_$today.xlsx";
$currentTime = date('d-m-Y H:i');
$textMessage = "Dữ liệu Báo Cáo Cuộc Gọi Hệ Thống VOS DIGINEXT ngày: $currentTime đã được cập nhật xong!" . PHP_EOL
  . "Kính mời đội ngũ vận hành vào website: http://103.112.209.152/report_ctc/ để xem và cập nhật thêm dữ liệu!" . PHP_EOL
  . "Hãy đăng nhập với tài khoản sau đây:" . PHP_EOL
  . "Tài khoản: $userName" . PHP_EOL
  . "Mật khẩu: $password";

$exportSuccessful = exportToExcel($query_report_ctc, $dbName, $header, $excelFilePath);

if ($exportSuccessful) {
  sendTelegramMessage($textMessage, $botToken, $chatId);

  sendTelegramMessage(
    $textMessage,
    $_ENV['TELEGRAM_BOT_TOKEN_DIGINEXT'],
    $_ENV['TELEGRAM_CHAT_ID']
  );
} else {
  echo "Error exporting data to Excel.\n";
}