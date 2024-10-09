<?php

require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';

date_default_timezone_set("Asia/Ho_Chi_Minh");

function processCDRLogs($dbName, $botToken, $chatId)
{
    $processedCDRLogs = [];

    $query_report_warning_cdr_log = "SELECT * FROM `history_send_mail_notifications`  
        WHERE send = 0
        ORDER BY time_update DESC";

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_MAIN'],
        $_ENV['DB_USERNAME_MAIN'],
        $_ENV['DB_PASSWORD_MAIN'],
        $dbName,
    );

    $result = $conn->query($query_report_warning_cdr_log);

    $conn->close();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $logID = $row['id'];

            // Check if the CDR log has been processed already
            if (!in_array($logID, $processedCDRLogs)) {
                // Mark the CDR log as processed
                $processedCDRLogs[] = $logID;

                $provider = $row['provider'];
                $time_update = $row['time_update'];
                $vos_name = $row['vos_name'];
                $customer_name = $row['customer_name'];
                $contract_code = $row['contract_code'];
                $addendum = $row['addendum'];
                $categories_code = $row['categories_code'];
                $user_name = $row['user_name'];
                $log = $row['log'];

                $currentTime = date('Y-m-d H:i:s');

                // Create $FormValues array with necessary data
                $textMessage = "Thông báo tại thời điểm : $currentTime\n";
                $textMessage .= "$provider CẢNH BÁO HỆ THỐNG CHƯA THỰC HIỆN GỬI THÔNG BÁO BIẾN ĐỘNG SỐ DƯ \n\n";
                $textMessage .= "time update : " . $time_update . " \n";
                $textMessage .= "VOS : " . $vos_name . " \n";
                $textMessage .= "Khách hàng : " . $customer_name . " \n";
                $textMessage .= "Hợp đồng : " . $contract_code . " \n";
                $textMessage .= "Phụ lục : " . $addendum . " \n";
                $textMessage .= "Dịch vụ : " . $categories_code . " \n";
                $textMessage .= "Sale : " . $user_name . " \n";
                $textMessage .= "log : " . $log . "";

                // Send the text message
                sendTelegramMessage($textMessage, $botToken, $chatId);
            }
        }
    }
}

// Define $dbName, $botToken, $chatId
$dbName = $_ENV['DB_DATABASE_BILLING_MAIN'];
$chatId = "-4260439667";
$botToken = "6585137930:AAEm1XLVeqtVgaZ6sZLSnXEaSVnnPeymgOk";

// Process CDR Logs
processCDRLogs($dbName, $botToken, $chatId);
