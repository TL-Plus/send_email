<?php
require 'send_email/config.php';
require 'send_email/includes/database_connection.php';
require 'send_email/includes/send_email_for_days.php';
require 'send_email/includes/send_telegram_message.php';
require 'includes/update_status_email.php';


// Define Constants
define('WARNING_THRESHOLD', 5);
define('TERMINATION_THRESHOLD', 7);

// Define Excel header
$header = [
    'CustomerName', 'CustomerCode', 'CustomerAddress', 'CustomerEmail', 'CustomerPhone',
    'SalerName', 'SalerCode', 'OrderNumber', 'OrderTime', 'Status'
];

// Define $dbName, $recipients
$dbName = $_ENV['DB_DATABASE_BILLING_DIGINEXT'];

function getTelegramRecipientsFromDatabase($dbName, $threshold, $orderNumberCondition)
{
    $telegramRecipients = [];

    // Execute your SQL query to fetch data from the database
    $query_api_full_code = "SELECT api.token AS BotToken, api.api AS UserId,
                        api.full_code AS UserCode,
                        local_users.email AS UserEmail
                        FROM `order_numbers`
                        JOIN api ON api.full_code = order_numbers.user_code
                        JOIN local_users ON local_users.user_code = order_numbers.user_code
                        WHERE DATEDIFF(NOW(), order_numbers.order_time) >= $threshold
                        AND order_numbers.status_email = 0
                        AND order_numbers.status = 'holding'
                        $orderNumberCondition
                        GROUP BY api.full_code";

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $dbName
    );

    $result = $conn->query($query_api_full_code);

    $conn->close();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $telegramRecipients[] = [
                'botToken' => $row['BotToken'],
                'chatId' => $row['UserId'],
                'userCode' => $row['UserCode'],
                'userEmail' => $row['UserEmail'],
            ];
        }
    }

    return $telegramRecipients;
}

function processEmailsAndTelegrams($threshold, $dbName, $header, $fileNamePrefix, $title, $orderNumberCondition)
{
    $telegramRecipients = getTelegramRecipientsFromDatabase($dbName, $threshold, $orderNumberCondition);

    // Define an array to store processed customer codes
    $processedUserCodes = [];

    foreach ($telegramRecipients as $telegramRecipient) {
        $userCode = $telegramRecipient['userCode'];

        // Build the query with the specific customer code
        $query_order_number_by_customer = "SELECT `customer_name`, `customer_code`, 
                `customer_address`, `customer_email`, `customer_phone`, `user_name`, 
                `user_code`, `order_number`, `order_time`, `status`
                FROM `order_numbers`
                WHERE DATEDIFF(NOW(), order_time) >= $threshold
                AND status = 'holding'
                AND status_email = 0
                $orderNumberCondition
                AND customer_code = '$userCode'";

        // Check if the customer code has been processed already
        if (!in_array($userCode, $processedUserCodes)) {
            // Mark the customer code as processed
            $processedUserCodes[] = $userCode;

            sendEmailForDays(
                $query_order_number_by_customer,
                $dbName,
                $header,
                "{$fileNamePrefix}{$threshold}_days.xlsx",
                "$title ($threshold-Days)",
                $telegramRecipient['userEmail']
            );

            // Call function to send telegram message notification for warning
            sendTelegramMessageWithSql(
                $query_order_number_by_customer,
                $dbName,
                $header,
                "{$fileNamePrefix}{$threshold}_days.xlsx",
                "$title ($threshold-Days)",
                $telegramRecipient['botToken'],
                $telegramRecipient['chatId']
            );
        }

        // Call function to update status email
        updateStatusEmail($query);
    }
}

// Process DVGTGT emails
$orderNumberConditionDVGTGT = "AND (order_number LIKE '1900%' OR order_number LIKE '1800%')";
processEmailsAndTelegrams(
    WARNING_THRESHOLD,
    $dbName,
    $header,
    'Report_warning_dvgtgt_',
    'Report Warning DVGTGT',
    $orderNumberConditionDVGTGT
);

processEmailsAndTelegrams(
    TERMINATION_THRESHOLD,
    $dbName,
    $header,
    'Report_termination_dvgtgt_',
    'Report Termination DVGTGT',
    $orderNumberConditionDVGTGT
);

// Process 888 Fixed emails
$orderNumberCondition888Fixed = "AND order_number LIKE '2%' AND order_number LIKE '%888%'";
processEmailsAndTelegrams(
    WARNING_THRESHOLD,
    $dbName,
    $header,
    'Report_warning_888_fixed_',
    'Report Warning 888 Fixed',
    $orderNumberCondition888Fixed
);

processEmailsAndTelegrams(
    TERMINATION_THRESHOLD,
    $dbName,
    $header,
    'Report_termination_888_fixed_',
    'Report Termination 888 Fixed',
    $orderNumberCondition888Fixed
);