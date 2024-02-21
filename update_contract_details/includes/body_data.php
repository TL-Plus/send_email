<?php
session_start(); // Start the session

require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/update_contract_details/includes/export_list_numbers.php';

// Function to connect to the database and fetch data based on input values
function fetchDataFromDB($numberSequence, $contractCode, $numberStatus)
{
    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT_TEST'],
        $_ENV['DB_USERNAME_DIGINEXT_TEST'],
        $_ENV['DB_PASSWORD_DIGINEXT_TEST'],
        $_ENV['DB_DATABASE_BILLING_DIGINEXT']
    );

    $resultListNumbers = convertNumberSequence($numberSequence);

    $query = "SELECT customer_name, contract_code, ext_number, activated_at, suspension_at, status
              FROM contracts_details
              WHERE ext_number IN $resultListNumbers
              AND contract_code = '$contractCode'
              AND status = '$numberStatus'";

    $result = $conn->query($query);

    // Fetch data and generate the HTML table
    $htmlTable = '<table>';
    $htmlTable .= '<tr><th>Customer Name</th><th>Contract Code</th><th>Ext Number</th><th>Activated At</th><th>Suspension At</th><th>Status</th></tr>';

    $rowCount = 0;

    while ($row = $result->fetch_assoc()) {
        $htmlTable .= '<tr>';
        $htmlTable .= '<td>' . $row['customer_name'] . '</td>';
        $htmlTable .= '<td>' . $row['contract_code'] . '</td>';
        $htmlTable .= '<td>' . $row['ext_number'] . '</td>';
        $htmlTable .= '<td>' . $row['activated_at'] . '</td>';
        $htmlTable .= '<td>' . $row['suspension_at'] . '</td>';
        $htmlTable .= '<td>' . $row['status'] . '</td>';
        $htmlTable .= '</tr>';
        $rowCount++;
    }

    $htmlTable .= '</table>';

    // Store input values in the session
    $_SESSION['numberSequence'] = $numberSequence;
    $_SESSION['contractCode'] = $contractCode;
    $_SESSION['numberStatus'] = $numberStatus;

    // Return the HTML table string and row count
    return array('table' => $htmlTable, 'rowCount' => $rowCount);
}

if (isset($_POST['check_data'])) {
    // Get values from the form
    $numberSequence = $_POST['number_sequence'];
    $contractCode = $_POST['contract_code'];
    $numberStatus = $_POST['number_status'];

    // Call the function to fetch data
    $resultData = fetchDataFromDB($numberSequence, $contractCode, $numberStatus);

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . '</div>';

    // Display the result table on the web page
    echo $resultData['table'];
}


// Function to update data in the database
function updateDataInDB($activatedAt, $contractDetailsLog, $numberSequence, $contractCode, $numberStatus)
{
    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT_TEST'],
        $_ENV['DB_USERNAME_DIGINEXT_TEST'],
        $_ENV['DB_PASSWORD_DIGINEXT_TEST'],
        $_ENV['DB_DATABASE_BILLING_DIGINEXT']
    );

    // Format activatedAt as needed
    $activatedAtFormatted = date('Y-m-d H:i:s', strtotime($activatedAt));

    $resultListNumbers = convertNumberSequence($numberSequence);

    // You should modify the update query based on your actual database schema
    $query = "UPDATE contracts_details 
              SET activated_at = '$activatedAtFormatted', 
                  log = CONCAT(log, '\n', CONCAT(NOW(), '__', '$contractDetailsLog/')) 
              WHERE ext_number IN $resultListNumbers
                AND contract_code = '$contractCode'
                AND status = '$numberStatus'";

    $result = $conn->query($query);

    // Check for errors
    if (!$result) {
        echo "Error: " . $conn->error;
    }

    $conn->close();

    return $result;
}

if (isset($_POST['update_data'])) {
    // Get values from the update form
    $activatedAt = $_POST['activated_at'];
    $contractDetailsLog = $_POST['contract_details_log'];

    $numberSequence = $_SESSION['numberSequence'];
    $contractCode = $_SESSION['contractCode'];
    $numberStatus = $_SESSION['numberStatus'];

    // Store input values in the session
    $_SESSION['activated_at'] = $activatedAt;
    $_SESSION['contract_details_log'] = $contractDetailsLog;

    // Call the function to update data
    $updateResult = updateDataInDB($activatedAt, $contractDetailsLog, $numberSequence, $contractCode, $numberStatus);

    // Display a success or failure message
    if ($updateResult) {
        echo '<div class="mt-3" style="color: green;">Data updated successfully!</div>';

        // Unset all session variables
        session_unset();

        // Destroy the session
        session_destroy();
    } else {
        echo '<div style="color: red;">Failed to update data. Please try again.</div>';
    }

    // Call the function to fetch updated data
    $resultData = fetchDataFromDB($numberSequence, $contractCode, $numberStatus);

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . '</div>';

    // Display the updated result table on the web page
    echo $resultData['table'];
}