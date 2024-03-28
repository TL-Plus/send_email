<?php

require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/vendor/autoload.php';
require_once '/var/www/html/send_email/includes/export_excel.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/report_ctc/includes/convert_excel_to_pdf.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';

session_start();
session_unset();
session_destroy();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user inputs
    $ccuValues = isset($_POST['ccu']) ? $_POST['ccu'] : '';
    $userName = isset($_POST['userName']) ? $_POST['userName'] : '';
    $ccuTotals = isset($_POST['ccuTotals']) ? $_POST['ccuTotals'] : '';

    $dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
    $botToken = $_ENV['TELEGRAM_BOT_TOKEN_DIGINEXT'];
    $chatId = $_ENV['TELEGRAM_CHAT_ID_REPORT_CCU_MAIN'];

    // Specify the path to your Excel file
    date_default_timezone_set("Asia/Ho_Chi_Minh");
    $today = date('Y_m_d');
    $currentTime = date('H\hi d-m-Y');
    $excelFile = "/var/www/html/report_ctc/files/Report_CTC_$today.xlsx";
    $pdfFile = "/var/www/html/report_ctc/files/Report_CTC_$today.pdf";
    $message = "Kính gửi Ban Lãnh Đạo." . PHP_EOL
        . "Kỹ thuật viên: $userName" . PHP_EOL
        . "Kính gửi - Báo Cáo Cuộc Gọi Hệ Thống VOS" . PHP_EOL
        . "Thời gian: " . $currentTime . "." . PHP_EOL;

    $convertSuccessful = convertExcelToPDF(
        $excelFile,
        $pdfFile,
        $userName,
        $ccuValues,
        $ccuTotals
    );

    if ($convertSuccessful) {
        sendTelegramMessagesWithFilePDF(
            $convertSuccessful,
            $message,
            $botToken,
            $chatId
        );

        sendTelegramMessagesWithFilePDF(
            $convertSuccessful,
            $message,
            $_ENV['TELEGRAM_BOT_TOKEN_DIGINEXT'],
            $_ENV['TELEGRAM_CHAT_ID']
        );
    }
}