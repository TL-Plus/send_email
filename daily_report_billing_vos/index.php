<?php

require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/vendor/autoload.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';

use Google\Client as Google_Client;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Service\Sheets\ValueRange as Google_Service_Sheets_ValueRange;

// Configure the Google Clients
$client = new Google_Client();
$client->setApplicationName('Daily Report Billing-Vos');
$client->setScopes([Google_Service_Sheets::SPREADSHEETS]);

// credentials.json is the key file we downloaded while setting up our Google Sheets API
$path = '/var/www/html/send_email/credentials.json';
$client->setAuthConfig($path);

// Configure the Sheets Service
$service = new Google_Service_Sheets($client);

// The spreadsheet id can be found in the URL https://docs.google.com/spreadsheets/d/1hZ5df7fQpKRnYOrK7OMpwmiv0sWD4dYRbcazk3I_ZJQ/edit?pli=1#gid=0
$spreadsheetId = '1hZ5df7fQpKRnYOrK7OMpwmiv0sWD4dYRbcazk3I_ZJQ';

date_default_timezone_set("Asia/Ho_Chi_Minh");

$yesterday = date("Y-m-d", strtotime("-1 day"));
$table_name_cdr = "cdr" . date('Ymd', strtotime($yesterday));
$table_name_cdrdsip = "cdrdsip" . date('Ym', strtotime($yesterday));
$currentTime = date('H:i:s d-m-Y');
$current_month = date('m/Y');

// cdr
$query_cdr = "SELECT DATE(time) AS Time, COUNT(*) as TotalCallCDR, SUM(duration) as TotalDurationCDR 
        FROM $table_name_cdr
        WHERE callee_gw LIKE 'RT_0%' AND duration > 0 AND DATE(time) = '$yesterday'";

$result_cdr = connectAndQueryDatabase(
    $query_cdr,
    $_ENV['DB_HOSTNAME_DIGINEXT'],
    $_ENV['DB_USERNAME_DIGINEXT'],
    $_ENV['DB_PASSWORD_DIGINEXT'],
    $_ENV['DB_DATABASE_VOICEREPORT'],
);

if ($result_cdr->num_rows > 0) {
    $row = $result_cdr->fetch_assoc();

    $values = [
        [$yesterday, $row['TotalCallCDR'], $row['TotalDurationCDR']]
    ];
} else {
    $values = [
        [$yesterday, 0, 0]
    ];
}

$timeColumnIndex = 'A';
$response = $service->spreadsheets_values->get($spreadsheetId, "Billing-Vos $current_month!A:A");
$lastRow = count($response->getValues()) + 1;
$range = "Billing-Vos $current_month!A{$lastRow}";
$requestBody = new Google_Service_Sheets_ValueRange([
    'values' => $values
]);
$service->spreadsheets_values->append($spreadsheetId, $range, $requestBody, ['valueInputOption' => 'USER_ENTERED']);

// cdrdsip
$query_cdrdsip = "SELECT DATE(time) AS Time, COUNT(*) AS TotalCallCDRDSIP, SUM(duration) AS TotalDurationCDRDSIP 
            FROM $table_name_cdrdsip
            WHERE callee_gw LIKE 'RT_DIGISIP_VINAPHONE' AND duration > 0 
            AND DATE(time) = '$yesterday'
            GROUP BY DATE(time)";

$result_cdrdsip = connectAndQueryDatabase(
    $query_cdrdsip,
    $_ENV['DB_HOSTNAME_DIGINEXT'],
    $_ENV['DB_USERNAME_DIGINEXT'],
    $_ENV['DB_PASSWORD_DIGINEXT'],
    $_ENV['DB_DATABASE_VOICEREPORT'],
);

if ($result_cdrdsip->num_rows > 0) {
    $row = $result_cdrdsip->fetch_assoc();

    $values = [
        [$row['TotalCallCDRDSIP'], $row['TotalDurationCDRDSIP'], $yesterday]
    ];
} else {
    $values = [
        [0, 0, $yesterday]
    ];
}

$timeColumnIndex = 'L';
$response = $service->spreadsheets_values->get($spreadsheetId, "Billing-Vos $current_month!L:L");
$lastRow = count($response->getValues()) + 1;
$range = "Billing-Vos $current_month!L{$lastRow}";
$requestBody = new Google_Service_Sheets_ValueRange([
    'values' => $values
]);
$service->spreadsheets_values->append($spreadsheetId, $range, $requestBody, ['valueInputOption' => 'USER_ENTERED']);

// send telegram
$textMessage = "Dữ liệu Báo Cáo Billing-VOS DIGINEXT đã được cập nhật xong vào lúc: $currentTime!" . PHP_EOL
    . "Bạn hãy vào link: https://docs.google.com/spreadsheets/d/1hZ5df7fQpKRnYOrK7OMpwmiv0sWD4dYRbcazk3I_ZJQ/edit#gid=559014398 để xem và cập nhật thêm dữ liệu!" . PHP_EOL
    . "link chính: https://docs.google.com/spreadsheets/d/1Z3O7Hy_uxpPXjjikFoJwsgqisYwXasD6mLd4g3AiIFM/edit#gid=1198928396";

sendTelegramMessage(
    $textMessage,
    $_ENV['TELEGRAM_BOT_TOKEN_DIGINEXT'],
    $_ENV['TELEGRAM_CHAT_ID']
);
