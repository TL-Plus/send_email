<?php

session_start();

require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';
require '/var/www/html/tools_diginext/includes/export_list_numbers.php';

// Function to connect to the database and fetch data based on input values
function fetchDataFromDB($numberSequence, $statusNumberCheck)
{
    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $_ENV['DB_DATABASE_BILLING_DIGINEXT']
    );

    $resultListNumbers = convertNumberSequence($numberSequence);

    $validStatus = array("", "inStock", "holding", "pending", "actived", "liquidated", "expired");
    if (!in_array($statusNumberCheck, $validStatus)) {
        echo '<div class="alert alert-danger my-3" role="alert"><strong>Lỗi:</strong>Trạng thái kiểm tra không hợp lệ!</div>';
        exit;
    }

    $query = "SELECT customer_name, order_number, order_time, status, user_updated, updated_at 
              FROM `order_numbers`
              WHERE order_number IN $resultListNumbers";

    if (!empty($statusNumberCheck) || $statusNumberCheck != "") {
        $query .= " AND status = '$statusNumberCheck'";
    }

    $result = $conn->query($query);

    // Fetch data and generate the HTML table
    $htmlTable = '<div class="table-responsive">';
    $htmlTable .= '<table class="table table-bordered">';
    $htmlTable .= '<thead><tr><th>Customer Name</th><th>Order Number</th><th>Order Time</th><th>Status</th><th>User Updated</th><th>Updated At</th></tr></thead>';
    $htmlTable .= '<tbody>';
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

    $htmlTable .= '</tbody>';
    $htmlTable .= '</table>';
    $htmlTable .= '</div>';

    // Store input values in the session
    $_SESSION['number_sequence'] = $numberSequence;
    $_SESSION['status_number_check'] = $statusNumberCheck;

    // Return the HTML table string and row count
    return array('table' => $htmlTable, 'rowCount' => $rowCount);
}

if (isset($_POST['check_data'])) {
    // Get values from the form
    $numberSequence = $_POST['number_sequence'];
    $statusNumberCheck = $_POST['status_number_check'];

    // Call the function to fetch data
    $resultData = fetchDataFromDB($numberSequence, $statusNumberCheck);

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . '</div>';

    // Display the result table on the web page
    echo $resultData['table'];
}


// Function to update data in the database
function updateDataInDB($statusNumber, $orderNumberLog, $numberSequence, $statusNumberCheck)
{
    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $_ENV['DB_DATABASE_BILLING_DIGINEXT']
    );

    $resultListNumbers = convertNumberSequence($numberSequence);

    $validStatus = array("inStock", "holding", "pending", "actived", "liquidated", "expired");
    $validStatusCheck = array("", "inStock", "holding", "pending", "actived", "liquidated", "expired");
    if (!in_array($statusNumberCheck, $validStatusCheck)) {
        echo '<div class="alert alert-danger my-3" role="alert"><strong>Lỗi:</strong>Trạng thái kiểm tra không hợp lệ!</div>';
        exit;
    }
    if (!in_array($statusNumber, $validStatus)) {
        echo '<div class="alert alert-danger my-3" role="alert"><strong>Lỗi:</strong>Trạng thái mới không hợp lệ!</div>';
        exit;
    }

    // You should modify the update query based on your actual database schema
    $query = "UPDATE order_numbers 
              SET status = '$statusNumber', 
                  log = CONCAT(log, '\n', CONCAT(NOW(), '__', '$orderNumberLog/')) 
              WHERE order_number IN $resultListNumbers";

    if (!empty($statusNumberCheck)) {
        $query .= " AND status = '$statusNumberCheck'";
    } else {
        $query .= " AND status = 'holding'";
    }

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

    $numberSequence = $_SESSION['number_sequence'];
    $statusNumberCheck = $_SESSION['status_number_check'];

    // Call the function to update data
    $updateResult = updateDataInDB($statusNumber, $orderNumberLog, $numberSequence, $statusNumberCheck);

    // Display a success or failure message
    if ($updateResult) {
        unset($_SESSION['status_number'], $_SESSION['status_number']);
        echo '<div class="mt-3" style="color: green;">Data updated successfully!</div>';
    } else {
        echo '<div style="color: red;">Failed to update data. Please try again.</div>';
    }

    // Call the function to fetch updated data
    $resultData = fetchDataFromDB($numberSequence, $statusNumberCheck);

    // Display the total number of rows
    echo '<div class="total-row mt-3">Total Rows: ' . $resultData['rowCount'] . '</div>';

    // Display the updated result table on the web page
    echo $resultData['table'];
}
