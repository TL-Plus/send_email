<?php
require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/send_email_for_days.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';


// Define Constants TIme
date_default_timezone_set("Asia/Ho_Chi_Minh");

$currentDate = strtotime('now');
$twoMonthsAgo = strtotime('-2 months', $currentDate);

$twoMonthsAgoMonth = date('m', $twoMonthsAgo);
$twoMonthsAgoYear = date('Y', $twoMonthsAgo);

$str_timeBegin = "$twoMonthsAgoYear-$twoMonthsAgoMonth-01 00:00:00";
$str_timeEnd = date('Y-m-t 23:59:59', strtotime($str_timeBegin));

print_r($str_timeBegin);
print_r($str_timeEnd);
die;
// SQL query for warning liabilities
$query_warning_liabilities = "SELECT DISTINCT
        liabilities.from_year AS from_year,
        liabilities.to_year AS to_year,
        liabilities.from_month AS from_month,
        liabilities.to_month AS to_month,
        liabilities.customer_name,
        liabilities.contract_code,
        liabilities.addendum,
        liabilities.categories_code,
        liabilities.user_name,
        contracts_details.ext_number,
        contracts_details.status,
        contracts_details.activated_at,
        contracts_details.expiration_at
    FROM
        liabilities
    LEFT JOIN contracts_details ON contracts_details.contract_code = liabilities.contract_code
    WHERE
        liabilities.from_year = YEAR(NOW())
        AND liabilities.from_month = MONTH(DATE_SUB(NOW(), INTERVAL 2 MONTH))
        AND contracts_details.contract_code IN (
            SELECT liabilities.contract_code FROM liabilities
            WHERE liabilities.from_year = YEAR(NOW())
            AND liabilities.from_month = MONTH(DATE_SUB(NOW(), INTERVAL 2 MONTH))
        )
        AND (
            (contracts_details.activated_at <= '$str_timeEnd' AND contracts_details.status IN ('actived','pending'))
            OR
            (
                contracts_details.status IN ('liquidated','expired')
                AND contracts_details.expiration_at >= '$str_timeBegin'
                AND (
                    contracts_details.activated_at <= '$str_timeBegin'
                    OR
                    (
                        contracts_details.activated_at >= '$str_timeBegin'
                        AND contracts_details.activated_at <= '$str_timeEnd'
                    )
                )
            )
        ) LIMIT 10";

// Define Excel header
$header = [
    'From Year',
    'To Year',
    'From Month',
    'To Month',
    'Customer Name',
    'Contract Code',
    'Addendum',
    'Categories Code',
    'Sale',
    'Ext/Number',
    'Status',
    'Activated At',
    'Expiration At'
];

// Define $dbName, $recipients
$dbName = $_ENV['DB_DATABASE_BILLING_MAIN'];
$recipients = $_ENV['RECIPIENTS_TEST'];
$cc_recipients = $_ENV['CC_RECIPIENTS_TEST'];

$FormValues = [
    'twoMonthsAgoMonth' => $twoMonthsAgoMonth,
    'twoMonthsAgoYear' => $twoMonthsAgoYear,
];

// Prepare email details
$month_liabilities = date('Y_m', strtotime('-2 months'));
$attachment = "/var/www/html/send_email/files_export/Report_warning_liabilities_$month_liabilities.xlsx";
$subject = "[DIGINEXT] - BÁO CÁO CẢNH BÁO CÔNG NỢ THÁNG $twoMonthsAgoMonth/$twoMonthsAgoYear";
$botToken = "6585137930:AAEm1XLVeqtVgaZ6sZLSnXEaSVnnPeymgOk";
$chatId = "-4256631235";

sendTelegramMessagesWithSqlAndFileExcel(
    $query_warning_liabilities,
    $dbName,
    $header,
    $attachment,
    $subject,
    $botToken,
    $chatId
);

// Call function to send email notification for warning liabilities
// sendEmailForDaysMain(
//     $query_warning_liabilities,
//     $dbName,
//     $header,
//     $attachment,
//     $subject,
//     bodyWarningLiabilities($FormValues),
//     $recipients,
//     $cc_recipients
// );
