<?php
session_start(); // Start the session

require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/send_email/includes/send_telegram_message.php';
require '/var/www/html/send_email/check_customer/includes/export_list_numbers.php';

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

function handleExport()
{
    // Check if the session data is empty
    if (empty($_SESSION['result_list_numbers'])) {
        echo '<div class="alert alert-warning my-3" role="alert"><span style="color: red;">No data to export. Please convert a number sequence first!</span></div>';
        return;
    }

    // Retrieve the result from the session
    $result_list_numbers = $_SESSION['result_list_numbers'];

    $sql_query = "SELECT customer_name AS CustomerName, user_name AS SalerName, contract_code AS ContracCode, ext_number AS Number 
        FROM `dcn202402`
        WHERE ext_number IN $result_list_numbers GROUP BY ext_number";

    $header = [
        'CustomerName', 'ContractCode', 'Number', 'SalerName'
    ];

    $dbName = $_ENV['DB_DATABASE_VOICEREPORT'];
    $botToken = $_ENV['TELEGRAM_BOT_TOKEN'];
    $chatId = $_ENV['TELEGRAM_CHAT_ID'];

    $attachment = "/var/www/html/check_customer/files/report_data.xlsx";
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
    } elseif (isset($_POST['export_excel'])) {
        handleExport();
    }
}