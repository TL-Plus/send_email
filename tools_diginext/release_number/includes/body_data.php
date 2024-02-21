<?php
session_start(); // Start the session

require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/tools_diginext/includes/export_list_numbers.php';

// Function to connect to the database and fetch data based on input values
function fetchDataFromDB($numberSequence)
{
    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT_TEST'],
        $_ENV['DB_USERNAME_DIGINEXT_TEST'],
        $_ENV['DB_PASSWORD_DIGINEXT_TEST'],
        $_ENV['DB_DATABASE_BILLING_DIGINEXT']
    );

    $resultListNumbers = convertNumberSequence($numberSequence);

    $query = "SELECT customer_name, order_number, order_time, status, user_updated, updated_at 
              FROM `order_numbers`
              WHERE order_number IN $resultListNumbers";

    $result = $conn->query($query);

    // Fetch data and generate the HTML table
    $htmlTable = '<table>';
    $htmlTable .= '<tr><th>Customer Name</th><th>Order Number</th><th>Order Time</th><th>Status</th><th>User Updated</th><th>Updated At</th></tr>';

    $rowCount = 0;

    while ($row = $result->fetch_assoc()) {
        $htmlTable .= '<tr>';
        $htmlTable .= '<td>' . $row['customer_name'] . '</td>';
        $htmlTable .= '<td>' . $row['order_number'] . '</td>';
        $htmlTable .= '<td>' . $row['order_time'] . '</td>';
        $htmlTable .= '<td>' . $row['status'] . '</td>';
        $htmlTable .= '<td>' . $row['user_updated'] . '</td>';
        $htmlTable .= '<td>' . $row['updated_at'] . '</td>';
        $htmlTable .= '</tr>';
        $rowCount++;
    }

    $htmlTable .= '</table>';

    // Store input values in the session
    $_SESSION['numberSequence'] = $numberSequence;

    // Return the HTML table string and row count
    return array('table' => $htmlTable, 'rowCount' => $rowCount);
}

if (isset($_POST['check_data'])) {
    // Get values from the form
    $numberSequence = $_POST['number_sequence'];

    // Call the function to fetch data
    $resultData = fetchDataFromDB($numberSequence);

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . '</div>';

    // Display the result table on the web page
    echo $resultData['table'];
}


// Function to update data in the database
function updateDataInDB($statusNumber, $orderNumberLog, $numberSequence)
{
    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT_TEST'],
        $_ENV['DB_USERNAME_DIGINEXT_TEST'],
        $_ENV['DB_PASSWORD_DIGINEXT_TEST'],
        $_ENV['DB_DATABASE_BILLING_DIGINEXT']
    );

    $resultListNumbers = convertNumberSequence($numberSequence);

    // You should modify the update query based on your actual database schema
    $query = "UPDATE order_numbers 
              SET status = '$statusNumber', 
                  log = CONCAT(log, '\n', CONCAT(NOW(), '__', '$orderNumberLog/')) 
              WHERE order_number IN $resultListNumbers";

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
    $statusNumber = $_POST['status_number'];
    $orderNumberLog = $_POST['order_numbers_log'];

    $numberSequence = $_SESSION['numberSequence'];

    // Store input values in the session
    $_SESSION['status_number'] = $statusNumber;
    $_SESSION['order_numbers_log'] = $orderNumberLog;

    // Call the function to update data
    $updateResult = updateDataInDB($statusNumber, $orderNumberLog, $numberSequence);

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
    $resultData = fetchDataFromDB($numberSequence);

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . '</div>';

    // Display the updated result table on the web page
    echo $resultData['table'];
}