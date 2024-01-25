<?php

require_once '/var/www/html/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/export_excel.php';
require_once '/var/www/html/send_email/report_ctc/includes/helpers.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

date_default_timezone_set("Asia/Ho_Chi_Minh");
// Specify the path to your Excel file
$today = date('Y_m_d');
$excelFilePath = "/var/www/html/Report_CTC_$today.xlsx";

// Load the Excel file
$spreadsheet = IOFactory::load($excelFilePath);

// Get the data from the first sheet
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray();

// Display data in a table
$currentTime = date('d-m-Y H:i:s');

$ccuValues = isset($_POST['ccu']) ? $_POST['ccu'] : '';
$userName = isset($_POST['userName']) ? $_POST['userName'] : '';
$ccuTotals = isset($_POST['ccuTotals']) ? $_POST['ccuTotals'] : '';

$_SESSION['ccu'] = isset($_POST['ccu']) ? $_POST['ccu'] : '';
$_SESSION['userName'] = isset($_POST['userName']) ? $_POST['userName'] : '';
$_SESSION['ccuTotals'] = isset($_POST['ccuTotals']) ? $_POST['ccuTotals'] : '';

?>

<form id="generatePdfForm" action="generate_pdf.php" method="post">
    <div class="row mt-3">
        <div class="col-md-6 header-current-time">
            <h5 id="currentTime" class="mt-4">Thời gian kiểm tra: <?php echo $currentTime; ?></h5>
        </div>
        <div class="col-md-6 mt-4 header-user-name">
            <h5>Nhân Viên: <?php echo $userName ?></h5>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <colgroup>
                <?php
                foreach ($data[0] as $index => $column) {
                    if ($data[0][$index] == 'CustomerName') {
                        echo '<col class="custom-col">';
                    } else {
                        echo '<col class="custom-col-1">';
                    }
                }
                ?>
            </colgroup>

            <?php
            // Process header row
            echo '<tr>';
            foreach ($data[0] as $headerValue) {
                $headerValue = translateHeader($headerValue);

                switch ($headerValue) {
                    default:
                        echo "<th style='font-size:20px'>$headerValue</th>";
                        break;
                }
            }
            echo '</tr>';

            // Process data rows
            $rowCounter = 0;

            foreach (array_slice($data, 1) as $row) {
                $rowCounter++;

                $ccuValue = isset($_POST['ccu'][$rowCounter]) ? $_POST['ccu'][$rowCounter] : '';
                if (empty($ccuValue)) {
                    continue; // Skip the row if CCU value is empty
                } else {
                    $_SESSION['ccu'][$rowCounter] = $ccuValue;
                }

                $backgroundColor = ' style="background-color: rgb(' . implode(" ,", getRowColor($rowCounter - 1)) . ');"';

                echo "<tr$backgroundColor id='$rowCounter'>";

                foreach ($row as $key => $cell) {
                    $modifiedCell = modifyValue($data[0][$key], $cell);
                    $translatedHeader = translateHeader($data[0][$key]);

                    $cellClass = ($rowCounter == 0) ? 'th' : 'td';

                    echo "<$cellClass class='custom-td'>";

                    if ($rowCounter == 0) {
                        echo $translatedHeader;
                    } else {
                        if ($data[0][$key] == 'TotalCurrentCall') {
                            echo '<p class="text-center"> ' . $ccuValue . '</p>';
                        } elseif ($data[0][$key] == 'CustomerName') {
                            echo '<p class="text-left"> ' . $modifiedCell['value'] . '</p>';
                        } elseif ($data[0][$key] == 'BlockViettel' || $data[0][$key] == 'BlockMobifone') {
                            $cellValue = ($cell === null) ? 0 : $modifiedCell['value'];
                            echo '<p class="text-center"> ' . $cellValue . '</p>';
                        } elseif ($data[0][$key] == 'TotalCCU') {
                            // Display CCU value for each row
                            echo '<p class="text-center">' . $ccuValue . '</p>';
                        } else {
                            echo $modifiedCell['value'];
                        }
                    }

                    echo "</$cellClass>";
                }

                echo '</tr>';
            }
            ?>


            <tr style="background-color: #35a4e5;">
                <?php
                $mergedColumnLabel = 'TỔNG KHÁCH HÀNG LỚN';
                $colspan = 2; // Set the colspan for the merged columns
                echo '<td class="custom-td" colspan="' . $colspan . '">' . $mergedColumnLabel . '</td>';

                // Initialize totals
                $totalCost = 0;
                $totalCCU = 0;
                $totalBlockViettel = 0;
                $totalBlockMobifone = 0;

                $rowCounter = 0;

                foreach (array_slice($data, 1) as $row) {
                    $rowCounter++;

                    $ccuValue = isset($_POST['ccu'][$rowCounter]) ? $_POST['ccu'][$rowCounter] : '';
                    if (empty($ccuValue)) {
                        continue; // Skip the row if CCU value is empty
                    }

                    foreach ($row as $key => $cell) {
                        $cellInfo = modifyValue($data[0][$key], $cell);

                        // Handle the total calculation for each column
                        switch ($data[0][$key]) {
                            case 'TotalCost':
                                $totalCost += $row[$key];
                                break;

                            case 'BlockViettel':
                                $totalBlockViettel += $row[$key];
                                break;

                            case 'BlockMobifone':
                                $totalBlockMobifone += $row[$key];
                                break;

                            case 'TotalCurrentCall':
                                $totalCCU += (int)$ccuValue;
                                break;
                        }
                    }
                }

                foreach ($data[0] as $key => $header) {
                    if ($key >= 2) {
                        echo '<td class="custom-td">';
                        if ($key == 2) {
                            $totalCostFormatted = number_format($totalCost, 0, '.', ',');
                            echo '<span style="color: red;">' . $totalCostFormatted . '</span>';
                        } elseif ($key == 3) {
                            echo '<span style="color: red;">' . $totalCCU . '</span>';
                        } elseif ($key == 4) {
                            echo '<span style="color: red;">' . $totalBlockViettel . '</span>';
                        } elseif ($key == 5) {
                            echo '<span style="color: red;">' . $totalBlockMobifone . '</span>';
                        }
                        echo '</td>';
                    }
                }
                ?>
            </tr>

            <tr style="background-color: #5fbff7;">
                <?php
                $mergedColumnLabel = 'TỔNG KHÁCH HÀNG CÒN LẠI';
                $colspan = 2; // Set the colspan for the merged columns
                echo '<td class="custom-td" colspan="' . $colspan . '">' . $mergedColumnLabel . '</td>';

                foreach ($data[0] as $key => $header) {
                    if ($key >= 2) {
                        echo '<td class="custom-td">';
                        if ($key == 2) {
                            $totalCost = array_sum(array_column(array_slice($data, 1), $key));
                            $totalCost = number_format($totalCost, 0, '.', ',');
                            echo '<span style="color: red;"></span>';
                        } elseif ($key == 3) {
                            echo '<span style="color: red;">' . $ccuTotals - $totalCCU . '</span>';
                        } elseif ($key == 4 || $key == 5) {
                            echo '<span style="color: red;"></span>';
                        }
                        echo '</td>';
                    }
                }
                ?>
            </tr>

            <tr style="background-color: #8bcdf3;">
                <?php
                $mergedColumnLabel = 'TỔNG HỆ THỐNG';
                $colspan = 2; // Set the colspan for the merged columns
                echo '<td class="custom-td" colspan="' . $colspan . '">' . $mergedColumnLabel . '</td>';

                foreach ($data[0] as $key => $header) {
                    if ($key >= 2) {
                        echo '<td class="custom-td">';
                        if ($key == 2) {
                            $totalCost = array_sum(array_column(array_slice($data, 1), $key));
                            $totalCost = number_format($totalCost, 0, '.', ',');
                            echo '<span style="color: red;"></span>';
                        } elseif ($key == 3) {
                            echo '<span style="color: red;">' . $ccuTotals . '</span>';
                        } elseif ($key == 4 || $key == 5) {
                            echo '<span style="color: red;"></span>';
                        }
                        echo '</td>';
                    }
                }
                ?>
            </tr>

        </table>
    </div>

    <div class="row mt-3">
        <div class="col-md-6 center-on-mobile-back">
            <a href="/report_ctc/" class="btn btn-primary btn-lg mt-3" onclick="showReport()">
                Quay lại trang chỉnh sửa
            </a>
        </div>
        <div class="col-md-6 text-md-end center-on-mobile">
            <button type="button" class="btn btn-primary btn-lg mt-3" data-toggle="modal"
                data-target="#notificationModal" onclick="generatePDF()">Xuất PDF</button>
        </div>
    </div>

</form>