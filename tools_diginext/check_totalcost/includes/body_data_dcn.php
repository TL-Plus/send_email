<?php
session_start();

require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';

// check dcn all query
function fetchDataFromDB($start_at, $end_at)
{
    date_default_timezone_set("Asia/Ho_Chi_Minh");

    $start_date = date_create($start_at);
    $end_date = date_create($end_at);

    $start_year = date_format($start_date, 'Y');
    $start_month = date_format($start_date, 'm');
    $end_year = date_format($end_date, 'Y');
    $end_month = date_format($end_date, 'm');

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
    $htmlTable .= '<thead><tr><th>Month</th><th>Total Call</th><th>Total Cost</th></tr></thead>';
    $htmlTable .= '<tbody>';

    for ($year = $start_year; $year <= $end_year; $year++) {
        $start = ($year == $start_year) ? $start_month : 1;
        $end = ($year == $end_year) ? $end_month : 12;

        for ($month = $start; $month <= $end; $month++) {
            $table_name = "dcn" . $year . str_pad($month, 2, '0', STR_PAD_LEFT);

            $query = "SELECT COUNT(*) AS TotalCall, SUM(TotalCost) AS TotalCost 
                FROM $table_name
                WHERE year = $year AND month = $month";

            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                $totalCall += $row['TotalCall'];
                $totalCost += $row['TotalCost'];
                $htmlTable .= '<tr>';
                $htmlTable .= '<td>' . $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '</td>';
                $htmlTable .= '<td>' . $row['TotalCall'] . '</td>';
                $htmlTable .= '<td>' . $row['TotalCost'] . '</td>';
                $htmlTable .= '</tr>';
            }
        }
    }

    $htmlTable .= '</tbody>';
    $htmlTable .= '</table>';
    $htmlTable .= '</div>';

    return array(
        'table' => $htmlTable,
        'totalCall' => $totalCall,
        'totalCost' => $totalCost,
        'start_date' => date_format($start_date, 'd-m-Y'),
        'end_date' => date_format($end_date, 'd-m-Y'),
    );
}

if (isset($_POST['check_data_dcn'])) {
    try {
        $start_at = $_POST['start_at_dcn'];
        $end_at = $_POST['end_at_dcn'];

        $resultData = fetchDataFromDB($start_at, $end_at);

        echo '<div class="total-row text-center">DCN - Start At: ' . $resultData['start_date'] . ' - End At: ' . $resultData['end_date'] . '</div>';
        echo '<div class="total-row text-center">Total Call: ' . $resultData['totalCall'] . ' - Total Cost: ' . $resultData['totalCost'] . '</div>';
        echo $resultData['table'];
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}

// check cdr theo tháng
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

//     $table_name = "dcn" . $start_year . $start_month;

//     $query = "SELECT COUNT(*) AS TotalCall, SUM(TotalCost) AS TotalCost 
//     FROM $table_name
//     WHERE year = $start_year AND year = $end_year
//     AND month = $start_month AND month = $end_month
//     AND day >= $start_day
//     AND day <= $end_day";

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

// if (isset($_POST['check_data_dcn'])) {
//     // Get the start_at and end_at values from the form
//     $start_at = $_POST['start_at_dcn'];
//     $end_at = $_POST['end_at_dcn'];

//     // Call the function to fetch data
//     $resultData = fetchDataFromDB($start_at, $end_at);

//     // Display start and end dates
//     echo '<div class="total-row text-center">DCN - Start At: ' . $resultData['start_date'] . ' - End At: ' . $resultData['end_date'] . '</div>';

//     // Display the result table on the web page
//     echo $resultData['table'];
// }