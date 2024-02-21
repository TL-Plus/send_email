<?php
session_start(); // Start the session

// Include necessary files
require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/send_email/includes/send_telegram_message.php';
require '/var/www/html/export_ctc_by_contract/includes/export_list_numbers.php';


// Function to connect to the database and fetch data based on input values
function fetchDataFromDB($caller_object, $call_type, $contract_code, $day_start, $day_end, $Caller)
{
    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $_ENV['DB_DATABASE_VOICEREPORT']
    );

    date_default_timezone_set("Asia/Ho_Chi_Minh");

    // Lấy ngày, tháng, năm từ thời gian nhập vào
    $year = date('Y', strtotime($day_start));
    $month = date('m', strtotime($day_start));
    $day_from = date('d', strtotime($day_start));
    $day_to = date('d', strtotime($day_end));

    if ($month < 10) {
        $month = '0' . (int)$month;
    }
    if ($day_from < 10) {
        $day_from = '0' . (int)$day_from;
    }
    if ($day_to < 10) {
        $day_to = '0' . (int)$day_to;
    }

    if (!empty($Caller)) {
        $resultCallers = convertNumberSequence($Caller);
    }

    $htmlTable = '<table>';
    $htmlTable .= '<tr><th>Time</th><th>Time End</th><th>Minute</th><th>Caller</th><th>Callee</th><th>Caller Object</th><th>Callee Object</th><th>Duration</th><th>Call Type</th><th>Fixed Type</th><th>Cost</th></tr>';
    $rowCount = 0;

    for ($i = (int)$day_from; $i <= (int)$day_to; $i++) {
        $day = str_pad($i, 2, '0', STR_PAD_LEFT); // Đảm bảo ngày có 2 chữ số

        $query = "SELECT time, time_end, minute, Caller, Callee, caller_object, callee_object, duration, call_type, fixed_type, cost
        FROM cdr" . $year . $month . $day . "
        WHERE duration > 0 
        AND contract_code = '$contract_code'
        AND DAY(time) >= $day_from AND DAY(time) <= $day_to";

        // Kiểm tra và thêm điều kiện cho caller_object
        if (!empty($caller_object)) {
            $query .= " AND caller_object = '$caller_object'";
        }

        // Kiểm tra và thêm điều kiện cho call_type
        if (!empty($call_type)) {
            $query .= " AND call_type LIKE '$call_type'";
        }

        // Kiểm tra và thêm điều kiện cho resultCallers
        if (!empty($resultCallers)) {
            $query .= " AND Caller IN $resultCallers";
        }

        $query .= " ORDER BY time ASC";

        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row['time'] . '</td>';
            $htmlTable .= '<td>' . $row['time_end'] . '</td>';
            $htmlTable .= '<td>' . $row['minute'] . '</td>';
            $htmlTable .= '<td>' . $row['Caller'] . '</td>';
            $htmlTable .= '<td>' . $row['Callee'] . '</td>';
            $htmlTable .= '<td>' . $row['caller_object'] . '</td>';
            $htmlTable .= '<td>' . $row['callee_object'] . '</td>';
            $htmlTable .= '<td>' . $row['duration'] . '</td>';
            $htmlTable .= '<td>' . $row['call_type'] . '</td>';
            $htmlTable .= '<td>' . $row['fixed_type'] . '</td>';
            $htmlTable .= '<td>' . $row['cost'] . '</td>';
            $htmlTable .= '</tr>';
            $rowCount++;
        }
    }

    $htmlTable .= '</table>';

    // Store input values in the session
    $_SESSION['caller_object'] = $caller_object;
    $_SESSION['call_type'] = $call_type;
    $_SESSION['contract_code'] = $contract_code;
    $_SESSION['day_start'] = $day_start;
    $_SESSION['day_end'] = $day_end;
    $_SESSION['Caller'] = $Caller;

    // Return the HTML table string and row count
    return array('table' => $htmlTable, 'rowCount' => $rowCount);
}

function checkData()
{
    date_default_timezone_set("Asia/Ho_Chi_Minh");

    // Get values from the form
    $caller_object = $_POST['caller_object'];
    $call_type = $_POST['call_type'];
    $contract_code = $_POST['contract_code'];
    $day_start = $_POST['day_start'];
    $day_end = $_POST['day_end'];
    $Caller = $_POST['Caller'];

    // Call the function to fetch data
    $resultData = fetchDataFromDB($caller_object, $call_type, $contract_code, $day_start, $day_end, $Caller);

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . '</div>';

    // Display the result table on the web page
    echo $resultData['table'];

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . '</div>';
}

function handleExport()
{
    date_default_timezone_set("Asia/Ho_Chi_Minh");

    $now_day = date('Y-m-d H:i:s');
    $now_day_int = strtotime(date('Y-m-d H:i:s'));
    $time_export_excel = date('d/m/Y');
    $day_export = date('d-m-Y');
    $time_export_excel = $time_export_excel . "_" . $now_day_int;

    // Retrieve input values from the session
    $caller_object = $_SESSION['caller_object'];
    $call_type = $_SESSION['call_type'];
    $contract_code = $_SESSION['contract_code'];
    $day_start = $_SESSION['day_start'];
    $day_end = $_SESSION['day_end'];
    $Caller = $_SESSION['Caller'];

    // Lấy ngày, tháng, năm từ thời gian nhập vào
    $year = date('Y', strtotime($day_start));
    $month = date('m', strtotime($day_start));
    $day_from = date('d', strtotime($day_start));
    $day_to = date('d', strtotime($day_end));

    if ($month < 10) {
        $month = '0' . (int)$month;
    }
    if ($day_from < 10) {
        $day_from = '0' . (int)$day_from;
    }
    if ($day_to < 10) {
        $day_to = '0' . (int)$day_to;
    }

    if (!empty($Caller)) {
        $resultCallers = convertNumberSequence($Caller);
    }

    $queries = array(); // Mảng lưu trữ các câu truy vấn

    // Tạo câu truy vấn cho mỗi ngày trong khoảng thời gian
    for ($i = (int)$day_from; $i <= (int)$day_to; $i++) {
        $day = str_pad($i, 2, '0', STR_PAD_LEFT); // Đảm bảo ngày có 2 chữ số

        $query = "SELECT time, time_end, minute, Caller, Callee, caller_object, callee_object, duration, call_type, fixed_type, cost
        FROM cdr" . $year . $month . $day . "
        WHERE duration > 0 
        AND contract_code = '$contract_code'
        AND DAY(time) >= $day_from AND DAY(time) <= $day_to";

        // Kiểm tra và thêm điều kiện cho caller_object
        if (!empty($caller_object)) {
            $query .= " AND caller_object = '$caller_object'";
        }

        // Kiểm tra và thêm điều kiện cho call_type
        if (!empty($call_type)) {
            $query .= " AND call_type LIKE '$call_type'";
        }

        // Kiểm tra và thêm điều kiện cho resultCallers
        if (!empty($resultCallers)) {
            $query .= " AND Caller IN $resultCallers";
        }

        $query .= " ORDER BY time ASC";

        $queries[] = $query;
    }

    $header = [
        'Time', 'Time End', 'Minute', 'Caller', 'Callee', 'Caller Object', 'Callee Object', 'Duration', 'Call Type', 'Fixed Type', 'Cost'
    ];

    $dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
    $botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
    $chatId = $_ENV['TELEGRAM_CHAT_ID'];

    $attachment = '/var/www/html/export_ctc_by_contract/files/' . str_replace("/", "_", $time_export_excel) . '_CTC_' . str_replace("/", "_", $contract_code) . '_' . $year . $month . '_' . $day_from . '_' . $day_to . '.xlsx';
    $subject = "Báo cáo chi tiết cước hợp đồng: $contract_code - Thời gian thực hiện: $now_day";

    // Export dữ liệu từ các câu truy vấn và kết hợp vào một tệp Excel
    $exportStatus = exportToExcels($queries, $dbName, $header, $attachment);

    if (strpos($exportStatus, 'successfully') !== false) {
        $telegramStatus = sendTelegramMessages($attachment, $subject, $botToken, $chatId);

        // Remove the session data after successful Telegram message send
        unset($_SESSION['caller_object']);
        unset($_SESSION['call_type']);
        unset($_SESSION['contract_code']);
        unset($_SESSION['day_start']);
        unset($_SESSION['day_end']);
        unset($_SESSION['Caller']);

        // Display the Telegram message status
        echo '<div class="alert alert-success my-3" role="alert">' . $telegramStatus . '</div>';
    } else {
        echo '<div class="alert alert-warning my-3" role="alert">' . $exportStatus . '</div>';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['check_data'])) {
        checkData();
    } elseif (isset($_POST['export_excel'])) {
        handleExport();
    }
}