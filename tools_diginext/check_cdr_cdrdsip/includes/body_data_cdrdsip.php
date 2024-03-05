<?php
session_start();

require '/var/www/html/send_email/config.php';
require '/var/www/html/send_email/includes/database_connection.php';

// check cdrdsip all query
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
    $totalDuration = 0;

    $htmlTable = '<div class="table-responsive">';
    $htmlTable .= '<table class="table table-bordered">';
    $htmlTable .= '<thead><tr><th>Date</th><th>Total Call</th><th>Total Duration</th></tr></thead>';
    $htmlTable .= '<tbody>';

    for ($year = $start_year; $year <= $end_year; $year++) {
        $start = ($year == $start_year) ? $start_month : 1;
        $end = ($year == $end_year) ? $end_month : 12;

        for ($month = $start; $month <= $end; $month++) {
            $table_name = "cdrdsip" . $year . str_pad($month, 2, '0', STR_PAD_LEFT);

            $query = "SELECT DATE(time) AS Date, COUNT(*) AS TotalCall, SUM(duration) AS TotalDuration 
                FROM $table_name
                WHERE callee_gw LIKE 'RT_DIGISIP_VINAPHONE' AND duration > 0 
                GROUP BY DATE(time)";

            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                $totalCall += $row['TotalCall'];
                $totalDuration += $row['TotalDuration'];
                $htmlTable .= '<tr>';
                $htmlTable .= '<td>' . $row['Date'] . '</td>';
                $htmlTable .= '<td>' . $row['TotalCall'] . '</td>';
                $htmlTable .= '<td>' . $row['TotalDuration'] . '</td>';
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
        'totalDuration' => $totalDuration,
        'start_date' => date_format($start_date, 'd-m-Y'),
        'end_date' => date_format($end_date, 'd-m-Y'),
    );
}


if (isset($_POST['check_data_cdrdsip'])) {
    try {
        $start_at = $_POST['start_at_cdrdsip'];
        $end_at = $_POST['end_at_cdrdsip'];

        $resultData = fetchDataFromDB($start_at, $end_at);

        echo '<div class="total-row text-center">CDRDSIP - Start At: ' . $resultData['start_date'] . ' - End At: ' . $resultData['end_date'] . '</div>';
        echo '<div class="total-row text-center">Total Call: ' . $resultData['totalCall'] . ' - Total Duration: ' . $resultData['totalDuration'] . '</div>';
        echo $resultData['table'];
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}