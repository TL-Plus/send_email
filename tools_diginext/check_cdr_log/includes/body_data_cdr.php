<?php

require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/send_telegram_message.php';
require_once '/var/www/html/tools_diginext/includes/export_list_numbers.php';

// check cdr all query
function fetchDataFromDB($Caller, $Callee, $start_at, $end_at)
{
    date_default_timezone_set("Asia/Ho_Chi_Minh");

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $_ENV['DB_DATABASE_VOICEREPORT']
    );

    $rowCount = 0;
    $totalDuration = 0;

    $htmlTable = '<div class="table-responsive">';
    $htmlTable .= '<table class="table table-bordered">';
    $htmlTable .= '<thead><tr><th>Time</th><th>Time End</th><th>Caller</th><th>Callee</th><th>Duration</th><th>Caller IP</th><th>Callee IP</th><th>Caller GW</th><th>Callee GW</th><th>Call Type</th><th>Customer</th><th>Contract</th></tr></thead>';
    $htmlTable .= '<tbody>';

    $startDateTimestamp = strtotime($start_at);
    $endDateTimestamp = strtotime($end_at);

    $currentDateTimestamp = $startDateTimestamp;
    while ($currentDateTimestamp <= $endDateTimestamp) {
        $currentDate = date('Y-m-d', $currentDateTimestamp);
        $table_name = "cdr" . date('Ymd', $currentDateTimestamp);

        $query = "SELECT time, time_end, Caller, Callee, duration, 
                caller_ip, callee_ip, caller_gw, callee_gw, 
                 call_type, customer_name, contract_code
            FROM $table_name
            WHERE DATE(`time`) = '$currentDate'";

        // Check and add conditions for caller
        if (!empty($Caller) && substr($Caller, 0, 2) !== "19" && substr($Caller, 0, 2) !== "18") {
            $resultCallers = convertNumberSequence84($Caller);
            $query .= " AND Caller IN $resultCallers";
        }

        // Check and add conditions for caller
        if (!empty($Caller) && (substr($Caller, 0, 2) === "19" || substr($Caller, 0, 2) === "18")) {
            $query .= " AND Caller = '$Caller'";
        }

        // Check and add conditions for callee
        if (!empty($Callee) && substr($Callee, 0, 2) !== "19" && substr($Callee, 0, 2) !== "18") {
            $resultCallees = convertNumberSequence84($Callee);
            $query .= " AND Callee IN $resultCallees";
        }

        if (!empty($Callee) && (substr($Callee, 0, 2) === "19" || substr($Callee, 0, 2) === "18")) {
            $query .= " AND Callee = '$Callee'";
        }

        $query .= " ORDER BY time ASC";

        $result = $conn->query($query);

        while ($row = $result->fetch_assoc()) {
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $row['time'] . '</td>';
            $htmlTable .= '<td>' . $row['time_end'] . '</td>';
            $htmlTable .= '<td>' . $row['Caller'] . '</td>';
            $htmlTable .= '<td>' . $row['Callee'] . '</td>';
            $htmlTable .= '<td>' . $row['duration'] . '</td>';
            $htmlTable .= '<td>' . $row['caller_ip'] . '</td>';
            $htmlTable .= '<td>' . $row['callee_ip'] . '</td>';
            $htmlTable .= '<td>' . $row['caller_gw'] . '</td>';
            $htmlTable .= '<td>' . $row['callee_gw'] . '</td>';
            $htmlTable .= '<td>' . $row['call_type'] . '</td>';
            $htmlTable .= '<td>' . $row['customer_name'] . '</td>';
            $htmlTable .= '<td>' . $row['contract_code'] . '</td>';
            $htmlTable .= '</tr>';
            $rowCount++;
            $totalDuration += $row['duration'];
        }

        $currentDateTimestamp = strtotime('+1 day', $currentDateTimestamp);
    }

    $htmlTable .= '</tbody>';
    $htmlTable .= '</table>';
    $htmlTable .= '</div>';

    // Store input values in the session
    $_SESSION['Caller'] = $Caller;
    $_SESSION['Callee'] = $Callee;
    $_SESSION['start_at_cdr'] = $start_at;
    $_SESSION['end_at_cdr'] = $end_at;

    return array(
        'table' => $htmlTable,
        'rowCount' => $rowCount,
        'totalDuration' => $totalDuration,
        'start_date' => date('d-m-Y', $startDateTimestamp),
        'end_date' => date('d-m-Y', $endDateTimestamp),
    );
}

function checkData()
{
    try {
        $Caller = $_POST['Caller'];
        $Callee = $_POST['Callee'];
        $start_at = $_POST['start_at_cdr'];
        $end_at = $_POST['end_at_cdr'];

        $resultData = fetchDataFromDB($Caller, $Callee, $start_at, $end_at);

        echo '<div class="total-row text-center">CDR - Start At: ' . $resultData['start_date'] . ' - End At: ' . $resultData['end_date'] . '</div>';
        echo '<div class="total-row text-center">Total Call: ' . $resultData['rowCount'] . ' - Total Duration: ' . $resultData['totalDuration'] . '</div>';
        echo $resultData['table'];
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}

function handleExport()
{
    date_default_timezone_set("Asia/Ho_Chi_Minh");

    $now_day = date('Y-m-d H:i:s');
    $now_day_int = strtotime($now_day);
    $time_export_excel = date('d/m/Y') . "_" . $now_day_int;

    // Retrieve input values either from $_SESSION or $_POST
    $Caller = $_POST['Caller'];
    $Callee = $_POST['Callee'];
    $start_at = $_POST['start_at_cdr'];
    $end_at = $_POST['end_at_cdr'];

    $startDateTimestamp = strtotime($start_at);
    $endDateTimestamp = strtotime($end_at);

    $queries = [];

    $currentDateTimestamp = $startDateTimestamp;
    while ($currentDateTimestamp <= $endDateTimestamp) {
        $currentDate = date('Y-m-d', $currentDateTimestamp);
        $table_name = "cdr" . date('Ymd', $currentDateTimestamp);

        $query = "SELECT time, time_end, Caller, Callee, duration, 
                caller_ip, callee_ip, caller_gw, callee_gw, 
                call_type, customer_name, contract_code
            FROM $table_name
            WHERE DATE(`time`) = '$currentDate'";

        // Check and add conditions for caller
        if (!empty($Caller) && substr($Caller, 0, 2) !== "19" && substr($Caller, 0, 2) !== "18") {
            $resultCallers = convertNumberSequence84($Caller);
            $query .= " AND Caller IN $resultCallers";
        }

        // Check and add conditions for caller
        if (!empty($Caller) && (substr($Caller, 0, 2) === "19" || substr($Caller, 0, 2) === "18")) {
            $query .= " AND Caller = '$Caller'";
        }

        // Check and add conditions for callee
        if (!empty($Callee) && substr($Callee, 0, 2) !== "19" && substr($Callee, 0, 2) !== "18") {
            $resultCallees = convertNumberSequence84($Callee);
            $query .= " AND Callee IN $resultCallees";
        }

        if (!empty($Callee) && (substr($Callee, 0, 2) === "19" || substr($Callee, 0, 2) === "18")) {
            $query .= " AND Callee = '$Callee'";
        }

        $query .= " ORDER BY time ASC";

        $queries[] = $query;

        $currentDateTimestamp = strtotime('+1 day', $currentDateTimestamp);
    }

    $header = [
        'Time',
        'Time End',
        'Caller',
        'Callee',
        'Duration',
        'Caller IP',
        'Callee IP',
        'Caller GW',
        'Callee GW',
        'Call Type',
        'Customer',
        'Contract',
    ];

    $dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
    $botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
    $chatId = $_ENV['TELEGRAM_CHAT_ID'];

    if (!empty($Caller) && !empty($Callee)) {
        $attachment = '/var/www/html/tools_diginext/files/check_cdr_log/' . str_replace("/", "_", $time_export_excel) . '_report_cdr_log_caller_' . $Caller . '_callee_' . $Callee . '.xlsx';
    } elseif (!empty($Caller)) {
        $attachment = '/var/www/html/tools_diginext/files/check_cdr_log/' . str_replace("/", "_", $time_export_excel) . '_report_cdr_log_caller_' . $Caller . '.xlsx';
    } elseif (!empty($Callee)) {
        $attachment = '/var/www/html/tools_diginext/files/check_cdr_log/' . str_replace("/", "_", $time_export_excel) . '_report_cdr_log_callee_' . $Callee . '.xlsx';
    } else {
        $attachment = '/var/www/html/tools_diginext/files/check_cdr_log/' . str_replace("/", "_", $time_export_excel) . '_report_cdr_log.xlsx';
    }

    $subject = "Báo cáo thông tin cuộc gọi" . PHP_EOL
        . "Thời gian thực hiện: $now_day";

    $exportStatus = exportToExcels($queries, $dbName, $header, $attachment);

    if (strpos($exportStatus, 'successfully') !== false) {
        $telegramStatus = sendTelegramMessages($attachment, $subject, $botToken, $chatId);

        unset($_SESSION['Caller'], $_SESSION['Callee'], $_SESSION['start_at_cdr'], $_SESSION['end_at_cdr']);

        // Display the Telegram message status
        echo '<div class="alert alert-success my-3" role="alert">' . $telegramStatus . '</div>';
    } else {
        echo '<div class="alert alert-warning my-3" role="alert">' . $exportStatus . '</div>';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['check_data_cdr'])) {
        checkData();
    } elseif (isset($_POST['export_excel'])) {
        handleExport();
    }
}
