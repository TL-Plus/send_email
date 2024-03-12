<?php

require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';

use Google\Client as Google_Client;
use Google\Service\Sheets as Google_Service_Sheets;
use Google\Service\Sheets\ValueRange as Google_Service_Sheets_ValueRange;

// Configure the Google Client
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

// $yesterday = date("Y-m-11");
$yesterday = date("Y-m-d", strtotime("-1 day"));
$table_name_cdr = "cdr" . date('Ymd', strtotime($yesterday));
$table_name_cdrdsip = "cdrdsip" . date('Ym', strtotime($yesterday));
$currentTime = date('H:i:s d-m-Y');
$current_month = date('m/Y');

// Truy vấn dữ liệu từ cơ sở dữ liệu
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
    // Iterate over each row of data and insert into Google Sheets
    while ($row = $result_cdr->fetch_assoc()) {
        $values = [
            [$yesterday, $row['TotalCallCDR'], $row['TotalDurationCDR']]
        ]; // Sửa đổi tên cột

        // Tìm vị trí của cột time trong bảng Google Sheets (giả sử là cột A)
        $timeColumnIndex = 'A';

        // Lấy số hàng cuối cùng của bảng
        $response = $service->spreadsheets_values->get($spreadsheetId, "Billing-Vos $current_month!A:A");
        $lastRow = count($response->getValues()) + 1;

        // Đặt phạm vi chèn dữ liệu
        $range = "Billing-Vos $current_month!A{$lastRow}";

        // Gửi yêu cầu chèn dữ liệu
        $requestBody = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $service->spreadsheets_values->append($spreadsheetId, $range, $requestBody, ['valueInputOption' => 'USER_ENTERED']);
    }
} else {
    echo "0 results";
}

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
    // Iterate over each row of data and insert into Google Sheets
    while ($row = $result_cdrdsip->fetch_assoc()) {
        $values = [
            [$row['TotalCallCDRDSIP'], $row['TotalDurationCDRDSIP'], $yesterday]
        ]; // Sửa đổi tên cột

        // Tìm vị trí của cột time trong bảng Google Sheets (giả sử là cột A)
        $timeColumnIndex = 'L';

        // Lấy số hàng cuối cùng của bảng
        $response = $service->spreadsheets_values->get($spreadsheetId, "Billing-Vos $current_month!L:L");
        $lastRow = count($response->getValues()) + 1;

        // Đặt phạm vi chèn dữ liệu
        $range = "Billing-Vos $current_month!L{$lastRow}";

        // Gửi yêu cầu chèn dữ liệu
        $requestBody = new Google_Service_Sheets_ValueRange([
            'values' => $values
        ]);
        $service->spreadsheets_values->append($spreadsheetId, $range, $requestBody, ['valueInputOption' => 'USER_ENTERED']);
    }
} else {
    echo "0 results";
}

$textMessage = "Dữ liệu Báo Cáo Billing-VOS DIGINEXT đã được cập nhật xong vào lúc: $currentTime!" . PHP_EOL
  . "Bạn hãy vào link: https://docs.google.com/spreadsheets/d/1hZ5df7fQpKRnYOrK7OMpwmiv0sWD4dYRbcazk3I_ZJQ/edit#gid=559014398 để xem và cập nhật thêm dữ liệu!" . PHP_EOL
  . "link chính: https://docs.google.com/spreadsheets/d/1Z3O7Hy_uxpPXjjikFoJwsgqisYwXasD6mLd4g3AiIFM/edit#gid=1198928396";

sendTelegramMessage(
    $textMessage,
    $_ENV['TELEGRAM_BOT_TOKEN_DIGINEXT'],
    $_ENV['TELEGRAM_CHAT_ID']
  );



// // Get the first day of the month
// $firstDayOfMonth = date("Y-m-01");
// // Get yesterday's date
// $endDate = date("Y-m-d", strtotime("-1 day"));

// $currentDate = $firstDayOfMonth;
// $allRows = [];

// // Loop through each day from the first day of the month to yesterday
// while ($currentDate <= $endDate) {
//     $table_name = "cdr" . date('Ymd', strtotime($currentDate));

//     // Truy vấn dữ liệu từ cơ sở dữ liệu cho mỗi ngày
//     $query = "SELECT DATE(`time`) AS Time, COUNT(*) as TotalCall, SUM(duration) as TotalDuration 
//             FROM $table_name
//             WHERE callee_gw LIKE 'RT_0%' AND duration > 0 AND DATE(`time`) = '$currentDate'";

//     $result = connectAndQueryDatabase(
//         $query,
//         $_ENV['DB_HOSTNAME_DIGINEXT'],
//         $_ENV['DB_USERNAME_DIGINEXT'],
//         $_ENV['DB_PASSWORD_DIGINEXT'],
//         $_ENV['DB_DATABASE_VOICEREPORT'],
//     );

//     if ($result->num_rows > 0) {
//         // Fetch each row of data and append to $allRows array
//         while ($row = $result->fetch_assoc()) {
//             $allRows[] = [
//                 $currentDate, // Date
//                 $row['TotalCall'], // TotalCall
//                 $row['TotalDuration'] // TotalDuration
//             ];
//         }
//     }

//     // Move to the previous day
//     $currentDate = date("Y-m-d", strtotime("-1 day", strtotime($currentDate)));
// }

// // Check if there is any data to insert
// if (!empty($allRows)) {
//     // Đặt phạm vi chèn dữ liệu
//     $range = "Trang tính2!A1"; // Assuming the data will be inserted starting from cell A1

//     // Gửi yêu cầu chèn dữ liệu
//     $requestBody = new Google_Service_Sheets_ValueRange([
//         'values' => $allRows
//     ]);
//     $service->spreadsheets_values->append($spreadsheetId, $range, $requestBody, ['valueInputOption' => 'USER_ENTERED']);
// } else {
//     echo "0 results";
// } 