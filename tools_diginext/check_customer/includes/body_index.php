<?php
session_start(); // Start the session

require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/send_email/includes/send_telegram_message.php';
require '/var/www/html/tools_diginext/includes/export_list_numbers.php';

function handleConvert()
{
    // Check if the input is empty
    if (empty($_POST['number_sequence'])) {
        echo '<div class="alert alert-warning my-3" role="alert"><strong>Error:</strong> Please enter a number sequence!</div>';
        return;
    }

    // Convert number sequence
    $result_list_numbers = convertNumberSequence($_POST['number_sequence']);

    // Store the result in the session
    $_SESSION['result_list_numbers'] = $result_list_numbers;

    echo '<div class="alert alert-success my-3" role="alert"><strong>Success:</strong> Number sequence converted successfully!</div>';
    echo '<div class="form-group">
            <label for="output">List of Numbers:</label>
            <textarea class="form-control" id="output" rows="15">' . $result_list_numbers . '</textarea>
          </div>';
    echo '<button class="btn btn-primary my-3" id="copyButton">Copy All</button>';
    echo '<div id="copyMessage" class="alert alert-info my-3" style="display: none;"><strong>Info:</strong> Copied successfully!</div>';
}

// Function to connect to the database and fetch data based on input values
function fetchDataFromDB($numberSequence)
{
    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $_ENV['DB_DATABASE_VOICEREPORT']
    );

    // Check if the session data is empty
    if (empty($_SESSION['result_list_numbers'])) {
        echo '<div class="alert alert-warning my-3" role="alert"><span style="color: red;">No data to export. Please convert a number sequence first!</span></div>';
        return;
    }

    // Retrieve the result from the session
    $resultListNumbers = convertNumberSequence($numberSequence);

    date_default_timezone_set("Asia/Ho_Chi_Minh");

    $now_day = date('Y-m-d H:i:s');
    $year = date('Y', strtotime($now_day));
    $month = date('m', strtotime($now_day));

    $query = "SELECT customer_name AS CustomerName, user_name AS SalerName, contract_code AS ContracCode, ext_number AS Number 
        FROM dcn" . $year . $month . "
        WHERE ext_number IN $resultListNumbers 
        GROUP BY ext_number";

    $result = $conn->query($query);

    // Fetch data and generate the HTML table
    $htmlTable = '<table>';
    $htmlTable .= '<tr><th>Customer Name</th><th>Saler Name</th><th>Contract Code</th><th>Number</th></tr>';

    $rowCount = 0;

    while ($row = $result->fetch_assoc()) {
        $htmlTable .= '<tr>';
        $htmlTable .= '<td>' . $row['CustomerName'] . '</td>';
        $htmlTable .= '<td>' . $row['SalerName'] . '</td>';
        $htmlTable .= '<td>' . $row['ContracCode'] . '</td>';
        $htmlTable .= '<td>' . $row['Number'] . '</td>';
        $htmlTable .= '</tr>';
        $rowCount++;
    }

    $htmlTable .= '</table>';

    // Store input values in the session
    $_SESSION['numberSequence'] = $numberSequence;

    // Return the HTML table string and row count
    return array('table' => $htmlTable, 'rowCount' => $rowCount);
}

function checkData()
{
    // Get values from the form
    $numberSequence = $_POST['number_sequence'];

    // Call the function to fetch data
    $resultData = fetchDataFromDB($numberSequence);

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . '</div>';

    // Display the result table on the web page
    echo $resultData['table'];
}

function handleExport()
{
    // Check if the session data is empty
    if (empty($_SESSION['result_list_numbers'])) {
        echo '<div class="alert alert-warning my-3" role="alert"><span style="color: red;">No data to export. Please convert a number sequence first!</span></div>';
        return;
    }

    // Retrieve the result from the session
    $result_list_numbers = $_SESSION['result_list_numbers'];

    $now_day = date('Y-m-d H:i:s');
    $year = date('Y', strtotime($now_day));
    $month = date('m', strtotime($now_day));

    $sql_query = "SELECT customer_name AS CustomerName, user_name AS SalerName, contract_code AS ContracCode, ext_number AS Number 
        FROM dcn" . $year . $month . "
        WHERE ext_number IN $result_list_numbers 
        GROUP BY ext_number";

    $header = [
        'CustomerName', 'SalerName', 'ContractCode', 'Number',
    ];

    $dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
    $botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
    $chatId = $_ENV['TELEGRAM_CHAT_ID'];

    $attachment = "/var/www/html/tools_diginext/files/check_customer/report_customer_data.xlsx";
    $subject = "Report Data From Number";

    $exportStatus = exportToExcel($sql_query, $dbName, $header, $attachment);

    if (strpos($exportStatus, 'successfully') !== false) {
        $telegramStatus = sendTelegramMessages($attachment, $subject, $botToken, $chatId);

        // Remove the session data after successful Telegram message send
        unset($_SESSION['result_list_numbers']);
        // Alternatively, you can use session_destroy() to destroy the entire session
        // session_destroy();

        // Display the Telegram message status
        echo '<div class="alert alert-success my-3" role="alert">' . $telegramStatus . '</div>';
    } else {
        echo '<div class="alert alert-warning my-3" role="alert">' . $exportStatus . '</div>';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['convert'])) {
        handleConvert();
    } elseif (isset($_POST['check_data'])) {
        checkData();
    } elseif (isset($_POST['export_excel'])) {
        handleExport();
    }
}