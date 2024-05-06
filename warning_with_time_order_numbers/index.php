<?php
require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/send_email_for_days.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';
require '/var/www/html/send_email/warning_with_time_order_numbers/includes/update_status_email.php';

date_default_timezone_set("Asia/Ho_Chi_Minh");
$now_day = date('Y-m-d H:i:s');
$now_day_int = strtotime($now_day);
$time_export_excel = date('d/m/Y') . "_" . $now_day_int;

// Define Constants
define('WARNING_THRESHOLD', 5);
define('TERMINATION_THRESHOLD', 7);

// Define Excel header
$header = [
    'Customer Name', 'Customer Code', 'Customer Address', 'Customer Email', 'Customer Phone',
    'Saler Name', 'Saler Code', 'Order Number', 'Order Time', 'Status'
];

// Define $dbName
$dbName = $_ENV['DB_DATABASE_BILLING_DIGINEXT'];
$recipients = $_ENV['RECIPIENTS_TEST'];
$cc_recipients = $_ENV['CC_RECIPIENTS_TEST'];

function getTelegramRecipientsFromDatabase($dbName, $threshold, $orderNumberCondition)
{
    $telegramRecipients = [];

    // Execute your SQL query to fetch data from the database
    $query_api_full_code = "SELECT api.token AS BotToken, api.api AS UserId,
                        order_numbers.user_name AS UserName,
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
                'userName' => $row['UserName'],
                'userCode' => $row['UserCode'],
                'userEmail' => $row['UserEmail'],
            ];
        }
    }

    return $telegramRecipients;
}

function fetchOrderData($query, $dbName)
{
    $orderNumberDatas = [];

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $dbName
    );

    $result = $conn->query($query);

    $conn->close();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $orderNumberDatas[] = [
                'orderNumber' => $row['order_number'],
                'orderTime' => $row['order_time'],
            ];
        }
    }

    return $orderNumberDatas;
}

function processEmailsAndTelegrams($threshold, $dbName, $header, $fileNamePrefix, $title, $orderNumberCondition)
{
    $cc_recipients = $_ENV['CC_RECIPIENTS_TEST'];

    $telegramRecipients = getTelegramRecipientsFromDatabase($dbName, $threshold, $orderNumberCondition);

    // Define an array to store processed customer codes
    $processedUserCodes5Days = [];
    $processedUserCodes7Days = [];

    foreach ($telegramRecipients as $telegramRecipient) {
        $userCode = $telegramRecipient['userCode'];

        $email_bk = 'lan.lt@diginext.com.vn';
        $bot_token = '6615163970:AAFIK99bsBHl5OV5Keg2FECzwTAgjXbVpg0';
        $chat_id = '922377883';

        if ($threshold == 5) {
            // Build the query with the specific customer code
            $query_order_number_by_customer = "SELECT `customer_name`, `customer_code`, 
                `customer_address`, `customer_email`, `customer_phone`, `user_name`, 
                `user_code`, `order_number`, `order_time`, `status`
                FROM `order_numbers`
                WHERE DATEDIFF(NOW(), order_time) >= $threshold
                AND status = 'holding'
                AND status_email = 0
                $orderNumberCondition
                AND customer_code = '$userCode'
                ORDER BY order_time DESC";

            // Check if the customer code has been processed already
            if (!in_array($userCode, $processedUserCodes5Days)) {
                // Mark the customer code as processed
                $processedUserCodes5Days[] = $userCode;

                // Fetch order data for the customer
                $orderNumberDatas = fetchOrderData($query_order_number_by_customer, $dbName);

                // Create $FormValues array with necessary data
                $FormValues = [
                    'userName' => $telegramRecipient['userName'],
                    'userEmail' => $telegramRecipient['userEmail'],
                    'orderNumberDatas' => $orderNumberDatas,
                ];

                sendEmailForDays(
                    $query_order_number_by_customer,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    bodyEmailOrderNumber($FormValues),
                    $FormValues['userEmail'],
                    $cc_recipients
                );

                // Call function to send telegram message notification for warning
                sendTelegramMessageWithSql(
                    $query_order_number_by_customer,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    $telegramRecipient['botToken'],
                    $telegramRecipient['chatId']
                );

                sendEmailForDays(
                    $query_order_number_by_customer,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    bodyEmailOrderNumber($FormValues),
                    $email_bk,
                    $email_bk
                );

                // Call function to send telegram message notification for warning
                sendTelegramMessageWithSql(
                    $query_order_number_by_customer,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    $bot_token,
                    $chat_id
                );
            }

            // Call function to update status
            updateStatusEmail5Days($query_order_number_by_customer);
        } elseif ($threshold == 7) {
            // Build the query with the specific customer code
            $query_order_number_by_customer = "SELECT `customer_name`, `customer_code`, 
                `customer_address`, `customer_email`, `customer_phone`, `user_name`, 
                `user_code`, `order_number`, `order_time`, `status`
                FROM `order_numbers`
                WHERE DATEDIFF(NOW(), order_time) >= $threshold
                AND status = 'holding'
                AND status_email = 1
                $orderNumberCondition
                AND customer_code = '$userCode'
                ORDER BY order_time DESC";

            // Check if the customer code has been processed already
            if (!in_array($userCode, $processedUserCodes7Days)) {
                // Mark the customer code as processed
                $processedUserCodes7Days[] = $userCode;

                // Fetch order data for the customer
                $orderNumberDatas = fetchOrderData($query_order_number_by_customer, $dbName);

                // Create $FormValues array with necessary data
                $FormValues = [
                    'userName' => $telegramRecipient['userName'],
                    'userEmail' => $telegramRecipient['userEmail'],
                    'orderNumberDatas' => $orderNumberDatas,
                ];

                sendEmailForDays(
                    $query_order_number_by_customer,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    bodyEmailOrderNumber($FormValues),
                    $FormValues['userEmail'],
                    $cc_recipients
                );

                // Call function to send telegram message notification for warning
                sendTelegramMessageWithSql(
                    $query_order_number_by_customer,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    $telegramRecipient['botToken'],
                    $telegramRecipient['chatId']
                );

                sendEmailForDays(
                    $query_order_number_by_customer,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    bodyEmailOrderNumber($FormValues),
                    $email_bk,
                    $email_bk
                );

                // Call function to send telegram message notification for warning
                sendTelegramMessageWithSql(
                    $query_order_number_by_customer,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    $bot_token,
                    $chat_id
                );
            }

            // Call function to update status
            updateStatusEmail7Days($query_order_number_by_customer);
        }
    }
}

// Process DVGTGT emails
$orderNumberConditionDVGTGT = "AND (order_number LIKE '1900%' OR order_number LIKE '1800%')";
processEmailsAndTelegrams(
    WARNING_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_warning_dvgtgt',
    '[DIGINEXT] - BÁO CÁO CẢNH BÁO ĐẶT SỐ DVGTGT SẮP HẾT HẠN',
    $orderNumberConditionDVGTGT
);

processEmailsAndTelegrams(
    TERMINATION_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_termination_dvgtgt',
    '[DIGINEXT] - BÁO CÁO CẢNH BÁO ĐẶT SỐ DVGTGT ĐÃ HẾT HẠN',
    $orderNumberConditionDVGTGT
);

// Process 888 Fixed emails
$orderNumberCondition888Fixed = "AND order_number LIKE '2%' AND order_number LIKE '%888%'";
processEmailsAndTelegrams(
    WARNING_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_warning_888_fixed',
    '[DIGINEXT] - BÁO CÁO CẢNH BÁO ĐẶT SỐ CỐ ĐỊNH 888 SẮP HẾT HẠN',
    $orderNumberCondition888Fixed
);

processEmailsAndTelegrams(
    TERMINATION_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_termination_888_fixed',
    '[DIGINEXT] - BÁO CÁO CẢNH BÁO ĐẶT SỐ CỐ ĐỊNH 888 ĐÃ HẾT HẠN',
    $orderNumberCondition888Fixed
);

// Process 555 Fixed emails
$orderNumberCondition888Fixed = "AND order_number LIKE '2%' AND order_number LIKE '%555%' AND provider='DIGINEXT'";
processEmailsAndTelegrams(
    WARNING_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_warning_555_fixed',
    '[DIGINEXT] - BÁO CÁO CẢNH BÁO ĐẶT SỐ CỐ ĐỊNH 555 SẮP HẾT HẠN',
    $orderNumberCondition888Fixed
);

processEmailsAndTelegrams(
    TERMINATION_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_termination_555_fixed',
    '[DIGINEXT] - BÁO CÁO CẢNH BÁO ĐẶT SỐ CỐ ĐỊNH 555 ĐÃ HẾT HẠN',
    $orderNumberCondition888Fixed
);
