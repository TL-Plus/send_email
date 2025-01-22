<?php

require_once '/var/www/html/send_email/vendor/autoload.php';
require_once '/var/www/html/send_email/config.php';
require_once '/var/www/html/send_email/includes/database_connection.php';
require_once '/var/www/html/send_email/includes/export_excel.php';
require_once '/var/www/html/report_ctc/includes/helpers.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Specify the path to your Excel file
$today = date('Y_m_d');
$excelFilePath = "/var/www/html/report_ctc/files/Report_CTC_$today.xlsx";

// Load the Excel file
$spreadsheet = IOFactory::load($excelFilePath);

// Get the data from the first sheet
$sheet = $spreadsheet->getActiveSheet();
$data = $sheet->toArray();

// Display data in a table
date_default_timezone_set("Asia/Ho_Chi_Minh");
$currentTime = date('d-m-Y H:i:s');
?>

<form id="checkData" action="check_data.php" method="post">
    <div class="row mt-3">
        <div class="col-md-12">
            <h5 id="currentTime" class="mt-4">Thời gian kiểm tra: <?php echo $currentTime; ?></h5>
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
            foreach ($data as $index => $row) {
                $backgroundColor = ($index === 0) ? '' : ' style="background-color: rgb(' . implode(" ,", getRowColor($index - 1)) . ');"';

                echo "<tr$backgroundColor>";

                foreach ($row as $key => $cell) {
                    $modifiedCell = modifyValue($data[0][$key], $cell);
                    $translatedHeader = translateHeader($data[0][$key]);

                    $cellClass = ($index == 0) ? 'th' : 'td';

                    echo "<$cellClass class='custom-td' style='vertical-align: middle;''>";

                    if ($index == 0) {
                        echo $translatedHeader;
                    } else {
                        if ($data[0][$key] == 'TotalCurrentCall') {
                            $ccuValue = isset($_SESSION['ccu'][$index]) ? htmlspecialchars($_SESSION['ccu'][$index]) : '';
                            echo '<input class="custom-col-input" type="number" pattern="\d*" name="ccu[' . $index . ']" value="' . $ccuValue . '" placeholder="Nhập CCU" />';
                        } elseif ($data[0][$key] == 'CustomerName') {
                            echo '<p class="text-left" style="vertical-align: middle; margin: 0 !important"> ' . $modifiedCell['value'] . '</p>';
                        } elseif ($data[0][$key] == 'BlockViettel' || $data[0][$key] == 'ActiveViettel') {
                            $cellValue = ($cell === null) ? 0 : $modifiedCell['value'];
                            echo '<p class="text-center" style="vertical-align: middle; margin: 0 !important"> ' . $cellValue . '</p>';
                        } else {
                            echo $modifiedCell['value'];
                        }
                    }

                    echo "</$cellClass>";
                }

                echo '</tr>';
            }
            ?>

            <tr style="background-color: #007bff; color: #fff;">
                <?php
                $mergedColumnLabel = 'TỔNG KHÁCH HÀNG LỚN';
                $colspan = 2; // Set the colspan for the merged columns
                echo '<td class="custom-td" style="vertical-align: middle;" colspan="' . $colspan . '"><b>' . $mergedColumnLabel . '</b></td>';

                foreach ($data[0] as $key => $header) {
                    if ($key >= 2) {
                        echo '<td class="custom-td" style="vertical-align: middle;">';
                        if ($key == 2) {
                            $totalCost = array_sum(array_column(array_slice($data, 1), $key));
                            $totalCost = number_format($totalCost, 0, '.', ',');
                            echo '<span style="color: #fff;"><b>' . $totalCost . '</b></span>';
                        } elseif ($key == 3) {
                            $ccuTotalsValue = isset($_POST['ccuTotals']) ? htmlspecialchars($_POST['ccuTotals']) : (isset($_SESSION['ccuTotals']) ? htmlspecialchars($_SESSION['ccuTotals']) : '');
                            echo '<input type="number" class="custom-col-input" name="ccuTotals" id="ccuTotals" style="color: red;font-weight: bold;" placeholder="Tổng CCU" required value="' . $ccuTotalsValue . '">';
                            echo '<script>sessionStorage.setItem("ccuTotals", ' . json_encode($ccuTotalsValue) . ');</script>';
                        } elseif ($key == 4) {
                            $totalInput = array_sum(array_map(function ($row) {
                                return (int) $row[4];
                            }, array_slice($data, 1)));
                            echo '<span style="color: #fff;"><b>' . $totalInput . '</b></span>';
                        } elseif ($key == 5) {
                            $totalInput = array_sum(array_map(function ($row) {
                                return (int) $row[5];
                            }, array_slice($data, 1)));
                            echo '<span style="color: #fff;"><b>' . $totalInput . '</b></span>';
                        }
                        echo '</td>';
                    }
                }
                ?>
            </tr>

        </table>
    </div>

    <div class="row mt-3">
        <div class="col-md-6">
            <div class="row mt-3">
                <div class="col-md-4">
                    <label for="userName" class="user-name mt-2 ms-4">
                        <h5>Tên Nhân Viên:</h5>
                    </label>
                </div>
                <div class="col-md-8">
                    <input type="text" name="userName" id="userName" class="form-control label-user-name"
                        placeholder="Nhập Tên Nhân Viên"
                        value="<?php echo isset($_SESSION['userName']) ? htmlspecialchars($_SESSION['userName']) : ''; ?>"
                        required />
                </div>
            </div>
        </div>
        <div class="col-md-6 text-md-end center-on-mobile">
            <button type="button" class="btn btn-primary btn-lg mt-3 mx-auto" data-toggle="modal"
                data-target="#notificationModal" onclick="checkData()">Kiểm tra lại dữ
                liệu</button>
        </div>
    </div>
</form>