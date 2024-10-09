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
define('WARNING_THRESHOLD', 7);
define('TERMINATION_THRESHOLD', 14);

// Define Excel header
$header = [
    'Tên',
    'Địa chỉ',
    'Email',
    'Diện thoại',
    'Kinh doanh',
    'Số đặt',
    'Thời gian',
    'Trạng thái'
];

// Define $dbName
$dbName = $_ENV['DB_DATABASE_BILLING_MAIN'];

function getTelegramRecipientsAndSalesEmails($dbName, $threshold, $orderNumberCondition)
{
    $telegramRecipients = [];

    // Execute your SQL query to fetch data from the database
    $query_api_full_code = "SELECT api.token AS BotToken, api.api AS UserId,
                        order_numbers.user_name AS UserName,
                        local_users.user_code AS UserCode,
                        local_users.email AS UserEmail
                        FROM `order_numbers`
                        LEFT JOIN api ON api.full_code = order_numbers.user_code
                        LEFT JOIN local_users ON local_users.user_code = order_numbers.user_code
                        WHERE order_numbers.status = 'holding'
                        AND order_numbers.IsShow = 1
                        $orderNumberCondition
                        GROUP BY local_users.user_code";

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_MAIN'],
        $_ENV['DB_USERNAME_MAIN'],
        $_ENV['DB_PASSWORD_MAIN'],
        $dbName
    );

    $result = $conn->query($query_api_full_code);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $userCode = $row['UserCode'];
            $primaryEmail = $row['UserEmail'];

            $salesEmails = [];
            $get_mail_sales = "SELECT email 
                   FROM local_users 
                   WHERE status = 1 AND parent_id IN (SELECT id 
                                       FROM local_users 
                                       WHERE user_code = '$userCode') 
                   AND JSON_CONTAINS(role, '\"Sale\"')";

            $salesResult = $conn->query($get_mail_sales);

            if ($salesResult && $salesResult->num_rows > 0) {
                while ($salesRow = $salesResult->fetch_assoc()) {
                    if (!empty($salesRow['email'])) {
                        $salesEmails[] = $salesRow['email'];
                    }
                }
            }

            $combinedEmails = $primaryEmail;
            if (!empty($salesEmails)) {
                $combinedEmails .= ',' . implode(',', $salesEmails);
            }

            $telegramRecipients[] = [
                'botToken' => '6585137930:AAEm1XLVeqtVgaZ6sZLSnXEaSVnnPeymgOk',  //$row['BotToken'],
                'chatId' => '-4108286784', //$row['UserId'],
                'userName' => $row['UserName'],
                'userCode' => $row['UserCode'],
                'userEmail' => $combinedEmails,
            ];
        }
    }

    $conn->close();

    return $telegramRecipients;
}


function fetchOrderData($query, $dbName)
{
    $orderNumberDatas = [];

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_MAIN'],
        $_ENV['DB_USERNAME_MAIN'],
        $_ENV['DB_PASSWORD_MAIN'],
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
    $cc_recipients = $_ENV['CC_RECIPIENTS'];

    $telegramRecipients = getTelegramRecipientsAndSalesEmails($dbName, $threshold, $orderNumberCondition);

    // Define an array to store processed customer codes
    $processedUserCodes = [];
    $processOrderNumbers7Days = [];
    $processOrderNumbers14Days = [];

    foreach ($telegramRecipients as $telegramRecipient) {
        $userCode = $telegramRecipient['userCode'];

        // Check if the customer code has been processed already
        if (!in_array($userCode, $processedUserCodes)) {
            // Mark the customer code as processed
            $processedUserCodes[] = $userCode;

            if ($threshold == WARNING_THRESHOLD) {
                // Build the query with the specific customer code
                $query_order_number_by_customer_7_days = "SELECT `customer_name`, 
                `customer_address`, `customer_email`, `customer_phone`, 
                `user_name`, `order_number`, `order_time`, `status`
                FROM `order_numbers`
                WHERE DATEDIFF(NOW(), order_time) >= $threshold
                AND status = 'holding'
                AND status_email = 0
                AND note = ''
                AND IsShow = 1
                $orderNumberCondition
                AND customer_code = '$userCode'
                ORDER BY order_time DESC";

                // Fetch order data for the customer
                $orderNumberDatas = fetchOrderData(
                    $query_order_number_by_customer_7_days,
                    $dbName
                );

                $FormValues = [
                    'userName' => $telegramRecipient['userName'],
                    'userEmail' => $telegramRecipient['userEmail'],
                    'orderNumberDatas' => $orderNumberDatas,
                    'note' => 'sắp',
                ];

                sendEmailForDaysMain(
                    $query_order_number_by_customer_7_days,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    bodyEmailOrderNumber($FormValues),
                    $FormValues['userEmail'],
                    $cc_recipients
                );

                sendTelegramMessageWithSqlMain(
                    $query_order_number_by_customer_7_days,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    $telegramRecipient['botToken'],
                    $telegramRecipient['chatId']
                );

                foreach ($orderNumberDatas as $orderNumberData) {
                    $orderNumber = $orderNumberData['orderNumber'];

                    if (!in_array($orderNumber, $processOrderNumbers7Days)) {
                        $processOrderNumbers7Days[] = $orderNumber;

                        // Call function to update status
                        updateStatusEmail7Days($orderNumber, $threshold, $orderNumberCondition, $userCode);
                        // updateStatusEmail7DaysDIGITEL($orderNumber, $threshold);
                    }
                }
            } elseif ($threshold == TERMINATION_THRESHOLD) {
                // Build the query with the specific customer code
                $query_order_number_by_customer_14_days = "SELECT `customer_name`, 
                `customer_address`, `customer_email`, `customer_phone`, 
                `user_name`, `order_number`, `order_time`, `status`
                FROM `order_numbers`
                WHERE DATEDIFF(NOW(), order_time) >= $threshold 
                    AND status = 'holding'
                    AND status_email = 1
                    AND note = ''
                    AND IsShow = 1
                    $orderNumberCondition
                    AND customer_code = '$userCode'
                ORDER BY order_time DESC";

                // Fetch order data for the customer
                $orderNumberDatas = fetchOrderData(
                    $query_order_number_by_customer_14_days,
                    $dbName
                );

                $FormValues = [
                    'userName' => $telegramRecipient['userName'],
                    'userEmail' => $telegramRecipient['userEmail'],
                    'orderNumberDatas' => $orderNumberDatas,
                    'note' => 'đã',
                ];

                sendEmailForDaysMain(
                    $query_order_number_by_customer_14_days,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    bodyEmailOrderNumber($FormValues),
                    $FormValues['userEmail'],
                    $cc_recipients
                );

                sendTelegramMessageWithSqlMain(
                    $query_order_number_by_customer_14_days,
                    $dbName,
                    $header,
                    "{$fileNamePrefix}_{$userCode}_{$threshold}_days.xlsx",
                    "$title ($threshold NGÀY)",
                    $telegramRecipient['botToken'],
                    $telegramRecipient['chatId']
                );

                foreach ($orderNumberDatas as $orderNumberData) {
                    $orderNumber = $orderNumberData['orderNumber'];

                    if (!in_array($orderNumber, $processOrderNumbers14Days)) {
                        $processOrderNumbers14Days[] = $orderNumber;

                        // Call function to update status
                        updateStatusEmail14Days($orderNumber, $threshold, $orderNumberCondition, $userCode);
                        // updateStatusEmail14DaysDIGITEL($orderNumber, $threshold);
                    }
                }
            }
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
    '[DIGI] - THÔNG BÁO ĐẶT SỐ DVGTGT SẮP HẾT HẠN',
    $orderNumberConditionDVGTGT
);

processEmailsAndTelegrams(
    TERMINATION_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_termination_dvgtgt',
    '[DIGI] - THÔNG BÁO ĐẶT SỐ DVGTGT ĐÃ HẾT HẠN',
    $orderNumberConditionDVGTGT
);

// Process 888 Fixed emails
$orderNumberCondition888Fixed = "AND order_number LIKE '2%' 
    AND (
        (SUBSTRING(order_number, 1, 2) IN ('24', '28') AND SUBSTRING(order_number, 3, 3) = '888') 
        OR 
        (SUBSTRING(order_number, 1, 2) NOT IN ('24', '28') AND SUBSTRING(order_number, 4, 3) = '888')
    )";
processEmailsAndTelegrams(
    WARNING_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_warning_888_fixed',
    '[DIGI] - THÔNG BÁO ĐẶT SỐ CỐ ĐỊNH 888 SẮP HẾT HẠN',
    $orderNumberCondition888Fixed
);

processEmailsAndTelegrams(
    TERMINATION_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_termination_888_fixed',
    '[DIGI] - THÔNG BÁO ĐẶT SỐ CỐ ĐỊNH 888 ĐÃ HẾT HẠN',
    $orderNumberCondition888Fixed
);

// Process 555 Fixed emails
$orderNumberCondition555Fixed = "AND order_number LIKE '2%' 
    AND (
        (SUBSTRING(order_number, 1, 2) IN ('24', '28') AND SUBSTRING(order_number, 3, 3) = '555') 
        OR 
        (SUBSTRING(order_number, 1, 2) NOT IN ('24', '28') AND SUBSTRING(order_number, 4, 3) = '555')
    )";
processEmailsAndTelegrams(
    WARNING_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_warning_555_fixed',
    '[DIGI] - THÔNG BÁO ĐẶT SỐ CỐ ĐỊNH 555 SẮP HẾT HẠN',
    $orderNumberCondition555Fixed
);

processEmailsAndTelegrams(
    TERMINATION_THRESHOLD,
    $dbName,
    $header,
    '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_termination_555_fixed',
    '[DIGI] - THÔNG BÁO ĐẶT SỐ CỐ ĐỊNH 555 ĐÃ HẾT HẠN',
    $orderNumberCondition555Fixed
);

// // Process 688 Fixed emails
// $orderNumberCondition688Fixed = "AND order_number LIKE '2%' 
//     AND (
//         (SUBSTRING(order_number, 1, 2) IN ('24', '28') AND SUBSTRING(order_number, 3, 3) = '688') 
//         OR 
//         (SUBSTRING(order_number, 1, 2) NOT IN ('24', '28') AND SUBSTRING(order_number, 4, 3) = '688')
//     )";
// processEmailsAndTelegrams(
//     WARNING_THRESHOLD,
//     $dbName,
//     $header,
//     '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_warning_688_fixed',
//     '[DIGI] - THÔNG BÁO ĐẶT SỐ CỐ ĐỊNH 688 SẮP HẾT HẠN',
//     $orderNumberCondition688Fixed
// );

// processEmailsAndTelegrams(
//     TERMINATION_THRESHOLD,
//     $dbName,
//     $header,
//     '/var/www/html/send_email/files_export/' . str_replace("/", "_", $time_export_excel) . '_report_termination_688_fixed',
//     '[DIGI] - THÔNG BÁO ĐẶT SỐ CỐ ĐỊNH 688 ĐÃ HẾT HẠN',
//     $orderNumberCondition688Fixed
// );
