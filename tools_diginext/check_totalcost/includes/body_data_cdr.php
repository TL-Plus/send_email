<?php

require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';

// check cdr all query
function fetchDataFromDB($start_at, $end_at)
{
    date_default_timezone_set("Asia/Ho_Chi_Minh");

    $conn = connectDatabase(
        $_ENV['DB_HOSTNAME_DIGINEXT'],
        $_ENV['DB_USERNAME_DIGINEXT'],
        $_ENV['DB_PASSWORD_DIGINEXT'],
        $_ENV['DB_DATABASE_VOICEREPORT']
    );

    $totalCall = 0;
    $totalCost = 0;

    $htmlTable = '<div class="table-responsive">';
    $htmlTable .= '<table class="table table-bordered">';
    $htmlTable .= '<thead><tr><th>Date</th><th>Total Call</th><th>Total Cost</th></tr></thead>';
    $htmlTable .= '<tbody>';

    $startDateTimestamp = strtotime($start_at);
    $endDateTimestamp = strtotime($end_at);

    $currentDateTimestamp = $startDateTimestamp;
    while ($currentDateTimestamp <= $endDateTimestamp) {
        $currentDate = date('Y-m-d', $currentDateTimestamp);
        $table_name = "cdr" . date('Ymd', $currentDateTimestamp);

        $query = "SELECT COUNT(*) AS TotalCall, SUM(cost) AS TotalCost 
            FROM $table_name
            WHERE DATE(`time`) = ?";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $currentDate);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $totalCall += $row['TotalCall'];
            $totalCost += $row['TotalCost'];
            $htmlTable .= '<tr>';
            $htmlTable .= '<td>' . $currentDate . '</td>';
            $htmlTable .= '<td>' . $row['TotalCall'] . '</td>';
            $htmlTable .= '<td>' . $row['TotalCost'] . '</td>';
            $htmlTable .= '</tr>';
        }

        $currentDateTimestamp = strtotime('+1 day', $currentDateTimestamp);
    }

    $htmlTable .= '</tbody>';
    $htmlTable .= '</table>';
    $htmlTable .= '</div>';

    return array(
        'table' => $htmlTable,
        'totalCall' => $totalCall,
        'totalCost' => $totalCost,
        'start_date' => date('d-m-Y', $startDateTimestamp),
        'end_date' => date('d-m-Y', $endDateTimestamp),
    );
}

if (isset($_POST['check_data_cdr'])) {
    try {
        $start_at = $_POST['start_at_cdr'];
        $end_at = $_POST['end_at_cdr'];

        $resultData = fetchDataFromDB($start_at, $end_at);

        echo '<div class="total-row text-center">CDR - Start At: ' . $resultData['start_date'] . ' - End At: ' . $resultData['end_date'] . '</div>';
        echo '<div class="total-row text-center">Total Call: ' . $resultData['totalCall'] . ' - Total Cost: ' . $resultData['totalCost'] . '</div>';
        echo $resultData['table'];
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}

// // check cdr theo tháng
// function fetchDataFromDB($start_at, $end_at)
// {
//     date_default_timezone_set("Asia/Ho_Chi_Minh");

//     $start_date = date_create($start_at);
//     $end_date = date_create($end_at);

//     $start_year = date_format($start_date, 'Y');
//     $start_month = date_format($start_date, 'm');
//     $start_day = date_format($start_date, 'd');

//     $end_year = date_format($end_date, 'Y');
//     $end_month = date_format($end_date, 'm');
//     $end_day = date_format($end_date, 'd');

//     $conn = connectDatabase(
//         $_ENV['DB_HOSTNAME_DIGINEXT'],
//         $_ENV['DB_USERNAME_DIGINEXT'],
//         $_ENV['DB_PASSWORD_DIGINEXT'],
//         $_ENV['DB_DATABASE_VOICEREPORT']
//     );

//     $table_name = "cdr" . $start_year . $start_month;

//     $query = "SELECT COUNT(*) AS TotalCall, SUM(cost) AS TotalCost 
//     FROM $table_name
//     WHERE YEAR(time) >= $start_year AND YEAR(time) <= $end_year
//     AND MONTH(time) >= $start_month AND MONTH(time) <= $end_month
//     AND DAY(time) >= $start_day
//     AND DAY(time) <= $end_day";

//     $result = $conn->query($query);

//     // Fetch data and generate the HTML table
//     $htmlTable = '<div class="table-responsive">';
//     $htmlTable .= '<table class="table table-bordered">';
//     $htmlTable .= '<thead><tr><th>Total Call</th><th>Total Cost</th></tr></thead>';
//     $htmlTable .= '<tbody>';

//     while ($row = $result->fetch_assoc()) {
//         $htmlTable .= '<tr>';
//         $htmlTable .= '<td>' . $row['TotalCall'] . '</td>';
//         $htmlTable .= '<td>' . $row['TotalCost'] . '</td>';
//         $htmlTable .= '</tr>';
//     }

//     $htmlTable .= '</tbody>';
//     $htmlTable .= '</table>';
//     $htmlTable .= '</div>';

//     // Return the HTML table string and row count
//     return array(
//         'table' => $htmlTable,
//         'start_date' => date_format($start_date, 'd-m-Y'),
//         'end_date' => date_format($end_date, 'd-m-Y'),
//     );
// }

// if (isset($_POST['check_data_cdr'])) {
//     // Get the start_at and end_at values from the form
//     $start_at = $_POST['start_at_cdr'];
//     $end_at = $_POST['end_at_cdr'];

//     // Call the function to fetch data
//     $resultData = fetchDataFromDB($start_at, $end_at);

//     // Display start and end dates
//     echo '<div class="total-row text-center">CDR - Start At: ' . $resultData['start_date'] . ' - End At: ' . $resultData['end_date'] . '</div>';

//     // Display the result table on the web page
//     echo $resultData['table'];
// }