<?php
require_once '/var/www/html/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/export_excel.php';
require_once '/var/www/html/send_email/report_ctc/includes/helpers.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Specify the path to your Excel file
$today = date('Y_m_d');
$excelFilePath = "/var/www/html/Report_CTC_$today.xlsx";

// Load the Excel file
$spreadsheet = IOFactory::load($excelFilePath);

// Get the data from the first sheet
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray();

// Display data in a table
date_default_timezone_set("Asia/Ho_Chi_Minh");
$currentTime = date('d-m-Y H:i:s');
echo '<form id="generatePdfForm" action="generate_pdf.php" method="post">';
echo '<div class="row mt-3">';
echo '<div class="col-md-12">';
echo '<h5 id="currentTime" class="mt-4">Thời gian kiểm tra: ' . $currentTime . '</h5>';
echo '</div>';
echo '</div>';
echo '<div class="table-responsive">';
echo '<table class="table table-bordered">';
echo '<colgroup>';
foreach ($data[0] as $index => $column) {
    if ($data[0][$index] == 'CustomerName') {
        echo '<col class="custom-col">';
    } else {
        echo '<col class="custom-col-1">';
    }
}

echo '</colgroup>';

foreach ($data as $index => $row) {
    // Skip applying background color to the header row
    $backgroundColor = ($index === 0) ? '' : ' style="background-color: rgb(' . implode(" ,", getRowColor($index - 1)) . ');"';

    echo "<tr$backgroundColor>";

    foreach ($row as $key => $cell) {
        $modifiedCell = modifyValue($data[0][$key], $cell);
        $translatedHeader = translateHeader($data[0][$key]);

        echo '<' . (($index == 0) ? 'th' : 'td') . ' class="custom-td">';

        if ($index == 0) {
            echo $translatedHeader;
        } else {
            if ($data[0][$key] == 'TotalCurrentCall') {
                echo '<input class="custom-col-input" type="number" pattern="\d*" name="ccu[' . $index . ']" value="' . $modifiedCell['value'] . '" placeholder="Nhập CCU">';
            } elseif ($data[0][$key] == 'CustomerName') {
                echo '<p class="text-left"> ' . $modifiedCell['value'] . '</p>';
            } elseif ($data[0][$key] == 'BlockViettel' || $data[0][$key] == 'BlockMobifone') {
                $cellValue = ($cell === null) ? 0 : $modifiedCell['value'];
                echo '<p class="text-center"> ' . $cellValue . '</p>';
            } else {
                echo $modifiedCell['value'];
            }
        }

        echo '</' . (($index == 0) ? 'th' : 'td') . '>';
    }

    echo '</tr>';
}

echo '<tr style="background-color: #35a4e5;">';
$mergedColumnLabel = 'TỔNG KHÁCH HÀNG LỚN';
$colspan = 2; // Set the colspan for the merged columns
echo '<td class="custom-td font-weight-bold" colspan="' . $colspan . '">' . $mergedColumnLabel . '</td>';
foreach ($data[0] as $key => $header) {
    if ($key >= 2) {
        echo '<td class="custom-td">';
        if ($key == 2) {
            $totalCost = array_sum(array_column(array_slice($data, 1), $key));
            $totalCost = number_format($totalCost, 0, '.', ',');
            echo '<span style="color: red;">' . $totalCost . '</span>';
        } elseif ($key == 3) {
            echo '<input type="number" class="custom-col-input" name="ccuTotals" id="ccuTotals" style="color: red;" placeholder="Tổng CCU" required/>';
        } elseif ($key == 4) {
            $totalInput = array_sum(array_map(function ($row) {
                return (int)$row[4];
            }, array_slice($data, 1)));
            echo '<span style="color: red;">' . $totalInput . '</span>';
        } elseif ($key == 5) {
            $totalInput = array_sum(array_map(function ($row) {
                return (int)$row[5];
            }, array_slice($data, 1)));
            echo '<span style="color: red;">' . $totalInput . '</span>';
        }
        echo '</td>';
    }
}

echo '</tr>';
echo '</table>';
echo '</div>';
echo '<div class="row mt-3">';
echo '<div class="col-md-6">';
echo '<div class="row mt-3">';
echo '<div class="col-md-4">';
echo '<label for="userName" class="user-name mt-2 ms-4"><h5>Tên Nhân Viên:</h5></label>';
echo '</div>';
echo '<div class="col-md-8">';
echo '<input type="text" name="userName" id="userName" class="form-control label-user-name" placeholder="Nhập Tên Nhân Viên" required/>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '<div class="col-md-6 text-md-end center-on-mobile">';
echo '<button type="button" class="btn btn-primary btn-lg mt-3 mx-auto" onclick="generatePDF()">Xuất PDF</button>';
echo '</div>';
echo '</div>';

echo '</form>';

// data-toggle="modal" data-target="#notificationModal"