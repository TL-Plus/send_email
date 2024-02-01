<?php
require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/send_email/includes/send_telegram_message.php';

function processCDRLogs($dbName, $botToken, $chatId)
{
    $query_report_quantity_DNC_blacklist_for_last_weeks = "SELECT 
        `cmd_code`, 
        COUNT(DISTINCT `msisdn`) AS `total_msisdn`
        FROM 
        `BlackList`
        WHERE 
        `updated_at` >= CURDATE() - INTERVAL 1 WEEK AND
        `updated_at` < CURDATE()  
        GROUP BY `cmd_code`";

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $dbName,
    );

    $result = $conn->query($query_report_quantity_DNC_blacklist_for_last_weeks);

    $conn->close();

    if ($result && $result->num_rows > 0) {
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $currentTime = date('H:i:s d-m-Y');

        // Get the period for the report (start and end dates)
        $report_period_start = date('Y-m-d', strtotime('last Monday', strtotime('now')));
        $report_period_end = date('Y-m-d', strtotime('previous Sunday', strtotime($report_period_start)));
        $report_period = "$report_period_start/$report_period_end";

        $textMessage = "Báo cáo số lượng DNC trong tuần trước ($report_period)\n"
            . "Thời gian kiểm tra: $currentTime\n\n";

        while ($row = $result->fetch_assoc()) {
            $cmdCode = $row['cmd_code'];
            $total = $row['total_msisdn'];

            // Concatenate data to the existing message
            $textMessage .= "cmd_code: $cmdCode\n"
                . "total_msisdn: $total\n\n";
        }

        // Send the text message
        sendTelegramMessage($textMessage, $botToken, $chatId);
    }
}

// Define $dbName, $botToken, $chatId
$dbName = $_ENV['DB_DATABASE_BLACKLIST'];
$botToken = $_ENV['TELEGRAM_BOT_TOKEN_RETURN_OTP'];
$chatId = $_ENV['TELEGRAM_CHAT_ID_RETURN_OTP'];

// Process CDR Logs
processCDRLogs($dbName, $botToken, $chatId);