<?php

// Include necessary files
require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/convert_to_zip_file.php';
require '/var/www/html/send_email/includes/send_telegram_message.php';
require '/var/www/html/tools_diginext/includes/export_list_numbers.php';


// Function to connect to the database and fetch data based on input values
function fetchDataFromDB($contract_code, $Caller, $caller_object, $call_type, $day_start, $day_end)
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
    $totalCost = 0;
    $infoCustomers = [];

    // Fetch data and generate the HTML table
    $htmlTable = '<div class="table-responsive">';
    $htmlTable .= '<table class="table table-bordered">';
    $htmlTable .= '<thead><tr><th>Time</th><th>Time End</th><th>Minute</th><th>Caller</th><th>Callee</th><th>Caller Object</th><th>Callee Object</th><th>Duration</th><th>Call Type</th><th>Fixed Type</th><th>Cost</th></tr></thead>';
    $htmlTable .= '<tbody>';

    $startDateTimestamp = strtotime($day_start);
    $endDateTimestamp = strtotime($day_end);

    $currentDateTimestamp = $startDateTimestamp;
    while ($currentDateTimestamp <= $endDateTimestamp) {
        $currentDate = date('Y-m-d', $currentDateTimestamp);
        $table_name = "cdr" . date('Ymd', $currentDateTimestamp);

        $query = "SELECT time, time_end, minute, 
                Caller, Callee, caller_object, callee_object, 
                duration, call_type, fixed_type, cost, 
                customer_name, contract_code, user_name
            FROM $table_name
            WHERE duration > 0 
            AND contract_code = '$contract_code'
            AND DATE(`time`) = '$currentDate'";

        // Check and add conditions for caller
        if (!empty($Caller)) {
            $resultCallers = convertNumberSequence84($Caller);
            $query .= " AND Caller IN $resultCallers";
        }

        // Check and add conditions for caller_object
        if (!empty($caller_object)) {
            $query .= " AND caller_object = '$caller_object'";
        }

        // Check and add conditions for call_type
        if (!empty($call_type)) {
            $query .= " AND call_type LIKE '$call_type'";
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
            $totalDuration += $row['duration'];
            $totalCost += $row['cost'];

            $infoCustomers[] = [
                'customerName' => $row['customer_name'],
                'contractCode' => $row['contract_code'],
                'userName' => $row['user_name'],
            ];
        }

        // Move to the next date
        $currentDateTimestamp = strtotime('+1 day', $currentDateTimestamp);
    }

    $htmlTable .= '</tbody>';
    $htmlTable .= '</table>';
    $htmlTable .= '</div>';

    // Store input values in the session
    $_SESSION['caller_object'] = $caller_object;
    $_SESSION['call_type'] = $call_type;
    $_SESSION['contract_code'] = $contract_code;
    $_SESSION['day_start'] = $day_start;
    $_SESSION['day_end'] = $day_end;
    $_SESSION['Caller'] = $Caller;

    // Return the HTML table string and row count
    return array(
        'table' => $htmlTable,
        'infoCustomers' => $infoCustomers,
        'totalDuration' => $totalDuration,
        'totalCost' => $totalCost,
        'rowCount' => $rowCount,
        'start_date' => date('d-m-Y', $startDateTimestamp),
        'end_date' => date('d-m-Y', $endDateTimestamp),
    );
}

function checkData()
{
    // Get values from the form
    $caller_object = $_POST['caller_object'];
    $call_type = $_POST['call_type'];
    $contract_code = $_POST['contract_code'];
    $day_start = $_POST['day_start'];
    $day_end = $_POST['day_end'];
    $caller = $_POST['Caller'];

    // Call the function to fetch data
    $resultData = fetchDataFromDB($contract_code, $caller, $caller_object, $call_type, $day_start, $day_end);

    echo '<div class="total-row mt-3">Start Day: ' . $resultData['start_date']. ' - End Day: ' . $resultData['end_date'] . '</div>';
    
    // Display customer, saler and contract
    echo '<div class="total-row mt-3">Customer: ' . $resultData['infoCustomers'][0]['customerName'] . '</div>';
    echo '<div class="total-row mt-3">Contract Code: ' . $resultData['infoCustomers'][0]['contractCode'] . '</div>';
    echo '<div class="total-row mt-3">Sale: ' . $resultData['infoCustomers'][0]['userName'] . '</div>';

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . ' - Total Call: ' . $resultData['rowCount'] . ' - Total Duration: ' . $resultData['totalDuration'] . ' - Total Cost: ' . $resultData['totalCost'] . '</div>';

    // Display the result table on the web page
    echo $resultData['table'];

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . ' - Total Call: ' . $resultData['rowCount'] . ' - Total Duration: ' . $resultData['totalDuration'] . ' - Total Cost: ' . $resultData['totalCost'] . '</div>';
}

function handleExport()
{
    date_default_timezone_set("Asia/Ho_Chi_Minh");

    $now_day = date('Y-m-d H:i:s');
    $now_day_int = strtotime($now_day);
    $time_export_excel = date('d/m/Y') . "_" . $now_day_int;

    // Retrieve input values either from $_SESSION or $_POST
    $caller_object = $_SESSION['caller_object'] ?? $_POST['caller_object'];
    $call_type = $_SESSION['call_type'] ?? $_POST['call_type'];
    $contract_code = $_SESSION['contract_code'] ?? $_POST['contract_code'];
    $day_start = $_SESSION['day_start'] ?? $_POST['day_start'];
    $day_end = $_SESSION['day_end'] ?? $_POST['day_end'];
    $Caller = $_SESSION['Caller'] ?? $_POST['Caller'];

    $startDateTimestamp = strtotime($day_start);
    $endDateTimestamp = strtotime($day_end);

    $queries = [];

    $currentDateTimestamp = $startDateTimestamp;
    while ($currentDateTimestamp <= $endDateTimestamp) {
        $currentDate = date('Y-m-d', $currentDateTimestamp);
        $table_name = "cdr" . date('Ymd', $currentDateTimestamp);

        $query = "SELECT time, time_end, minute, 
                Caller, Callee, caller_object, callee_object, 
                duration, call_type, fixed_type, cost, 
                customer_name, contract_code, user_name
            FROM $table_name
            WHERE duration > 0 
            AND contract_code = '$contract_code'
            AND DATE(`time`) = '$currentDate'";

        // Check and add conditions for caller
        if (!empty($Caller)) {
            $resultCallers = convertNumberSequence84($Caller);
            $query .= " AND Caller IN $resultCallers";
        }

        // Check and add conditions for caller_object
        if (!empty($caller_object)) {
            $query .= " AND caller_object = '$caller_object'";
        }

        // Check and add conditions for call_type
        if (!empty($call_type)) {
            $query .= " AND call_type LIKE '$call_type'";
        }

        $query .= " ORDER BY time ASC";
        $queries[] = $query;

        $currentDateTimestamp = strtotime('+1 day', $currentDateTimestamp);
    }

    $header = [
        'Time', 'Time End', 'Minute', 'Caller', 'Callee', 'Caller Object', 'Callee Object', 'Duration', 'Call Type', 'Fixed Type', 'Cost', 'Customer Name', 'Contract Code', 'Saler Name'
    ];

    $dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
    $botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
    $chatId = $_ENV['TELEGRAM_CHAT_ID'];

    $excelFile = '/var/www/html/tools_diginext/files/export_ctc_by_contract/' . str_replace("/", "_", $time_export_excel) . '_CTC_' . str_replace("/", "_", $contract_code) . '_' . date('Y_m_d', $startDateTimestamp) . '_' . date('Y_m_d', $endDateTimestamp) . '.xlsx';
    $zipFile = '/var/www/html/tools_diginext/files/export_ctc_by_contract/' . str_replace("/", "_", $time_export_excel) . '_CTC_' . str_replace("/", "_", $contract_code) . '_' . date('Y_m_d', $startDateTimestamp) . '_' . date('Y_m_d', $endDateTimestamp) . '.zip';
    $randstring = generateRandomString();
    $subject = "Báo cáo chi tiết cước hợp đồng: $contract_code" . PHP_EOL . "Thời gian thực hiện: $now_day"  . PHP_EOL . "Mật khẩu giải nén: $randstring";

    // Export data from queries and combine into an Excel file
    $exportStatus = exportToExcels($queries, $dbName, $header, $excelFile);

    if (strpos($exportStatus, 'successfully') !== false) {
        $convertStatus = ConvertToZipFile($excelFile, $zipFile, $randstring);

        // Check if the zip file exists
        if (strpos($exportStatus, 'successfully') !== false) {
            // Successfully converted to zip, now send via Telegram
            $telegramStatus = sendTelegramMessagesWithFileZip($zipFile, $subject, $botToken, $chatId);

            // Remove session data after successful Telegram message send
            unset($_SESSION['caller_object'], $_SESSION['call_type'], $_SESSION['contract_code'], $_SESSION['day_start'], $_SESSION['day_end'], $_SESSION['Caller']);

            // Display success message
            echo '<div class="alert alert-success my-3" role="alert">' . $convertStatus . '</div>';
        } else {
            // Error in converting to zip
            echo '<div class="alert alert-warning my-3" role="alert">' . $convertStatus . '</div>';
        }
    } else {
        // Error in exporting to Excel
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