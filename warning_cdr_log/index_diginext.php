<?php

require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';

date_default_timezone_set("Asia/Ho_Chi_Minh");

function processCDRLogs($dbName, $botToken, $chatId)
{
    $processedCDRLogs = [];

    // DATE(`TimeBegin`) = CURDATE() AND

    $query_report_warning_cdr_log = "SELECT * FROM `CDRLog`
        WHERE  TIMESTAMPDIFF(HOUR, `TimeBegin`, NOW()) >= 1
            AND (
                (`Conditon` = 1)
                OR
                (`Conditon` = 0 AND `Count` > 0)
            )
        ORDER BY `TimeBegin` DESC";

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $dbName,
    );

    $result = $conn->query($query_report_warning_cdr_log);

    $conn->close();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $logID = $row['ID'];

            // Check if the CDR log has been processed already
            if (!in_array($logID, $processedCDRLogs)) {
                // Mark the CDR log as processed
                $processedCDRLogs[] = $logID;

                $server = $row['Server'];
                $timeUpdate = $row['TimeUpdate'];
                $timeBegin = $row['TimeBegin'];
                $timeEnd = $row['TimeEnd'];
                $count = $row['Count'];
                $condition = $row['Conditon'];

                $currentTime = date('Y-m-d H:i:s');

                $textMessage = "Time Check : $currentTime\n";
                $textMessage .= "SERVER : " . $server . " \n";
                $textMessage .= "TimeUpdate : " . $timeUpdate . " \n";
                $textMessage .= "TimeBegin : " . $timeBegin . " \n";
                $textMessage .= "TimeEnd : " . $timeEnd . " \n";
                $textMessage .= "Count : " . $count . " \n";
                $textMessage .= "Condition : " . $condition . " \n";


                // Send the text message
                sendTelegramMessage($textMessage, $botToken, $chatId);
            }
        }
    }
}

// Define $dbName, $botToken, $chatId
$dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN_CDR_LOG_DIGINEXT'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_CDR_LOG_DIGINEXT'];

// Process CDR Logs
processCDRLogs($dbName, $botToken, $chatId);
