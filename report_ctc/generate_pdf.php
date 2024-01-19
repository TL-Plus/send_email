<?php
require_once '/var/www/html/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/export_excel.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/report_ctc/includes/convert_excel_to_pdf.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user inputs
    $ccuValues = $_POST['ccu'];
    $userName = $_POST['userName'];
    $ccuTotals = $_POST['ccuTotals'];

    $dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
    $botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
    $chatId = $_ENV['TELEGRAM_CHAT_ID'];

    // Specify the path to your Excel file
    $today = date('Y_m_d');
    $excelFile = "/var/www/html/Report_CTC_$today.xlsx";
    $pdfFile = "Report_CTC_$today.pdf";
    $message = "Kính gửi Ban Lãnh Đạo." . PHP_EOL
        . "Kỹ thuật viên: $userName" . PHP_EOL
        . "Kính gửi - Báo Cáo Cuộc Gọi Hệ Thống VOS" . PHP_EOL
        . "Thời gian: " . date('H\hi d-m-Y') . "." . PHP_EOL;

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
    }
}